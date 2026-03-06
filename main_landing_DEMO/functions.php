<?php
/**
 * Общие функции для лендинга
 * Этот файл содержит вспомогательные функции, используемые в разных частях приложения
 */

/**
 * Получает реальный IP-адрес клиента
 *
 * @return string IP-адрес пользователя
 */
function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ipList[0]);
    } else {
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}

/**
 * Определяет страну пользователя по IP адресу с использованием кеширования
 *
 * @return string|null Двухбуквенный код страны или null при ошибке
 */
function getCountryByIP() {
    static $cached_country = null;

    if ($cached_country !== null) {
        return $cached_country;
    }

    try {
        $clientIP = getClientIP();

        // Пропускаем локальные IP
        if (filter_var($clientIP, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            $cached_country = config('geo.ip_geolocation.fallback_country');
            return $cached_country;
        }

        // Проверяем кеш файл
        $cache_file = sys_get_temp_dir() . '/geo_cache_' . md5($clientIP) . '.json';
        $cache_ttl = config('performance.cache_ttl', 3600);

        if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_ttl) {
            $cached_data = json_decode(file_get_contents($cache_file), true);
            if ($cached_data && isset($cached_data['country'])) {
                $cached_country = $cached_data['country'];
                return $cached_country;
            }
        }

        $apiUrl = config('geo.ip_geolocation.api_url') . $clientIP;

        // Используем cURL для запроса
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 3, // Не ждем долго
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'LandingPage/1.0'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            if ($data && isset($data['countryCode']) && $data['status'] === 'success') {
                $country = strtolower($data['countryCode']);

                // Кешируем результат
                file_put_contents($cache_file, json_encode(['country' => $country, 'timestamp' => time()]));

                $cached_country = $country;
                return $country;
            }
        }

        // Логируем ошибку геолокации
        error_log("IP Geolocation failed for IP: $clientIP, Response: $response");

    } catch (Exception $e) {
        error_log("IP Geolocation error: " . $e->getMessage());
    }

    $cached_country = config('geo.ip_geolocation.fallback_country');
    return $cached_country;
}

/**
 * Определяет страну пользователя для инициализации телефонного префикса
 *
 * @return string Двухбуквенный код страны в нижнем регистре
 */
function determineUserCountry() {
    // Проверяем, есть ли страна в сессии
    if (isset($_SESSION['user_country'])) {
        return $_SESSION['user_country'];
    }

    // Приоритет 1: Параметр из URL (?country=it)
    if (isset($_GET['country']) && !empty($_GET['country'])) {
        $country = strtolower(trim($_GET['country']));
        // Проверяем, что страна разрешена
        if (in_array($country, config('geo.allowed_countries'))) {
            $_SESSION['user_country'] = $country;
            return $country;
        }
    }

    // Приоритет 2: Определение по IP через геолокацию
    if (config('geo.ip_geolocation.enabled')) {
        $country = getCountryByIP();
        if ($country && in_array($country, config('geo.allowed_countries'))) {
            $_SESSION['user_country'] = $country;
            return $country;
        }
    }

    // Приоритет 3: Дефолтная страна из конфигурации
    $defaultCountry = config('geo.default_country');
    $_SESSION['user_country'] = $defaultCountry;

    return $defaultCountry;
}

/**
 * Проверяет rate limiting для предотвращения спама
 *
 * @param string $identifier Уникальный идентификатор (IP или другой)
 * @return bool True если запрос разрешен, False если превышен лимит
 */
function checkRateLimit($identifier) {
    if (!config('security.rate_limiting.enabled')) {
        return true;
    }

    $log_dir = 'logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0777, true);
    }

    $rate_file = $log_dir . '/rate_limit_' . md5($identifier) . '.json';
    $now = time();

    // Читаем существующие данные
    $rate_data = [];
    if (file_exists($rate_file)) {
        $rate_data = json_decode(file_get_contents($rate_file), true) ?: [];
    }

    // Очищаем старые записи (старше 1 часа)
    $rate_data = array_filter($rate_data, function($timestamp) use ($now) {
        return ($now - $timestamp) < 3600;
    });

    $max_per_minute = config('security.rate_limiting.max_attempts_per_minute', 5);
    $max_per_hour = config('security.rate_limiting.max_attempts_per_hour', 20);

    // Проверяем лимиты
    $requests_last_minute = count(array_filter($rate_data, function($timestamp) use ($now) {
        return ($now - $timestamp) < 60;
    }));

    $requests_last_hour = count($rate_data);

    if ($requests_last_minute >= $max_per_minute || $requests_last_hour >= $max_per_hour) {
        logMessage('WARNING', 'RATE_LIMIT', 'Rate limit exceeded', [
            'identifier' => $identifier,
            'requests_last_minute' => $requests_last_minute,
            'requests_last_hour' => $requests_last_hour
        ]);
        return false;
    }

    // Добавляем новый запрос
    $rate_data[] = $now;
    file_put_contents($rate_file, json_encode($rate_data));

    return true;
}

/**
 * Проверяет honeypot поле для защиты от спам-ботов
 *
 * @param array $post_data POST данные
 * @return bool True если не спам, False если спам
 */
function checkHoneypot($post_data) {
    if (!config('security.honeypot.enabled')) {
        return true;
    }

    $honeypot_field = config('security.honeypot.field_name', 'website_url');

    // Если honeypot поле заполнено - это спам
    if (!empty($post_data[$honeypot_field])) {
        logMessage('WARNING', 'HONEYPOT', 'Honeypot field filled - possible spam', [
            'field' => $honeypot_field,
            'value' => $post_data[$honeypot_field]
        ]);
        return false;
    }

    return true;
}

/**
 * Валидирует email адрес
 *
 * @param string $email Email для валидации
 * @return bool True если валидный, False если нет
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Валидирует номер телефона
 *
 * @param string $phone Номер телефона
 * @param string $country Код страны
 * @return bool True если валидный, False если нет
 */
function validatePhone($phone, $country = null) {
    // Убираем все кроме цифр, +, -, (, ), пробелов
    $cleaned = preg_replace('/[^\d+\-\(\)\s]/', '', $phone);

    // Базовая проверка на минимальную длину
    if (strlen($cleaned) < 7) {
        return false;
    }

    // Проверка на максимальную длину
    if (strlen($cleaned) > 20) {
        return false;
    }

    // Проверка на повторяющиеся цифры (спам фильтр)
    if (preg_match('/(.)\1{5,}/', $cleaned)) {
        return false;
    }

    return true;
}

/**
 * Очищает и нормализует номер телефона
 *
 * @param string $phone Номер телефона
 * @return string Нормализованный номер
 */
function normalizePhone($phone) {
    // Убираем все кроме цифр
    return preg_replace('/[^0-9]/', '', $phone);
}

/**
 * Создает безопасное имя файла
 *
 * @param string $filename Исходное имя файла
 * @return string Безопасное имя файла
 */
function sanitizeFilename($filename) {
    return preg_replace('/[^A-Za-z0-9\-_.]/', '', $filename);
}

/**
 * Записывает сообщение в лог с ротацией файлов
 *
 * @param string $level Уровень сообщения (INFO, WARNING, ERROR)
 * @param string $source Источник сообщения
 * @param string $message Текст сообщения
 * @param array $data Данные для логирования
 */
function logMessage($level, $source, $message, $data = null) {
    if (!config('logging.enabled')) {
        return;
    }

    $log_levels = ['DEBUG', 'INFO', 'WARNING', 'ERROR'];
    $current_level = array_search(config('logging.level', 'INFO'), $log_levels);

    if ($current_level === false || array_search($level, $log_levels) < $current_level) {
        return;
    }

    $log_dir = 'logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0777, true);
    }

    $log_file = $log_dir . '/handler.log';
    $max_file_size = config('logging.max_file_size', 10 * 1024 * 1024);
    $max_files = config('logging.max_files', 5);

    // Проверяем размер файла и ротируем если нужно
    if (file_exists($log_file) && filesize($log_file) > $max_file_size) {
        rotateLogFiles($log_file, $max_files);
    }

    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'level' => $level,
        'source' => $source,
        'message' => $message
    ];

    if ($data !== null) {
        $log_entry['data'] = $data;
    }

    $log_text = json_encode($log_entry, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
    file_put_contents($log_file, $log_text, FILE_APPEND);
}

/**
 * Ротирует лог файлы
 *
 * @param string $log_file Путь к основному лог файлу
 * @param int $max_files Максимальное количество файлов
 */
function rotateLogFiles($log_file, $max_files) {
    // Удаляем самый старый файл если достигнут лимит
    $oldest_file = $log_file . '.' . $max_files;
    if (file_exists($oldest_file)) {
        unlink($oldest_file);
    }

    // Сдвигаем все файлы
    for ($i = $max_files - 1; $i >= 1; $i--) {
        $current_file = $log_file . '.' . $i;
        $next_file = $log_file . '.' . ($i + 1);
        if (file_exists($current_file)) {
            rename($current_file, $next_file);
        }
    }

    // Переименовываем основной файл
    if (file_exists($log_file)) {
        rename($log_file, $log_file . '.1');
    }
}

/**
 * Создает HTTP запрос с обработкой ошибок
 *
 * @param string $url URL для запроса
 * @param array $options Опции для curl
 * @return array Результат ['success' => bool, 'response' => string, 'error' => string, 'http_code' => int]
 */
function makeHttpRequest($url, $options = []) {
    if (!function_exists('curl_init')) {
        return [
            'success' => false,
            'response' => null,
            'error' => 'cURL extension not available',
            'http_code' => 0
        ];
    }

    $result = [
        'success' => false,
        'response' => '',
        'error' => '',
        'http_code' => 0
    ];

    try {
        $ch = curl_init();

        $default_options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_USERAGENT => 'LandingPage/1.0'
        ];

        curl_setopt_array($ch, $default_options + $options);

        $result['response'] = curl_exec($ch);
        $result['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_error($ch)) {
            $result['error'] = curl_error($ch);
        } else {
            $result['success'] = true;
        }

        curl_close($ch);

    } catch (Exception $e) {
        $result['error'] = $e->getMessage();
    }

    return $result;
}
