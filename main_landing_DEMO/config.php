<?php
/**
 * Конфигурационный файл лендинга
 * Все настройки собраны в одном месте для удобства управления
 */

// Основные настройки лендинга
$config = [
    // GEO настройки
    'geo' => [
        // Дефолтная страна (Индия)
        'default_country' => 'in',

        // Разрешенные страны для работы лендинга (с приоритетом на Индию)
        'allowed_countries' => ['in', 'it', 'ru', 'es', 'mx', 'id', 'tr', 'pt', 'fr', 'de', 'hu', 'ro', 'fa', 'za', 'ke', 'ci'],

        // Настройки геолокации по IP
        'ip_geolocation' => [
            'enabled' => true,
            'fallback_country' => 'in', // Fallback на Индию
            'api_url' => 'http://ip-api.com/json/', // Бесплатный API для геолокации
        ],
    ],

        // Настройки продукта (обезличены для публичного репозитория)
        'product' => [
            'aff' => 'demo_aff',
            'offer' => 'DemoOffer',
            'offer_name' => 'Demo Product',
            'doctor_name' => 'Expert',
            'price_old' => 4998,
            'price_new' => 2499,
            'currency' => 'INR',
        ],

    // Настройки трекинга
    'tracking' => [
        'leadbit' => [
            'enabled' => true,
            'api_url' => 'http://wapi.leadbit.com/api/pub/new-order/_67bc70cc74af8662538029',
        ],
        'keitaro' => [
            'enabled' => true,
            'postback_url' => 'http://217.114.12.132/2083254/postback',
        ],
        'facebook' => [
            'enabled' => true,
            'conversion_api_url' => 'https://graph.facebook.com/v18.0/{pixel_id}/events',
        ],
        'affscale' => [
            'enabled' => false, // Отключен по умолчанию
            'api_key' => 'f3757724dbcf0ab3c9518ac8f6a3a17b6567b05d',
            'goal_id' => 398,
            'webmaster_id' => '3676',
        ],
        'everad' => [
            'enabled' => false, // Отключен по умолчанию
            'api_url' => 'https://tracker.everad.com/conversion/new',
        ],
    ],

    // Настройки безопасности
    'security' => [
        'rate_limiting' => [
            'enabled' => true,
            'max_attempts_per_minute' => 5,
            'max_attempts_per_hour' => 20,
        ],
        'honeypot' => [
            'enabled' => true,
            'field_name' => 'website_url',
        ],
    ],

    // Настройки логирования
    'logging' => [
        'enabled' => true,
        'level' => 'INFO', // DEBUG, INFO, WARNING, ERROR
        'max_file_size' => 10 * 1024 * 1024, // 10MB
        'max_files' => 5, // Количество файлов ротации
    ],

    // Настройки производительности
    'performance' => [
        'cache_enabled' => true,
        'cache_ttl' => 3600, // 1 час
        'compression_enabled' => true,
    ],

    // Настройки проверки дубликатов
    'duplicate_check' => [
        'shared_dir' => '../shared',
        'timeout_hours' => 24,
    ],
];

/**
 * Получить значение из конфигурации
 */
function config($key = null, $default = null) {
    global $config;

    if ($key === null) {
        return $config;
    }

    $keys = explode('.', $key);
    $value = $config;

    foreach ($keys as $k) {
        if (!isset($value[$k])) {
            return $default;
        }
        $value = $value[$k];
    }

    return $value;
}

/**
 * Установить значение в конфигурацию (только для runtime)
 */
function config_set($key, $value) {
    global $config;

    $keys = explode('.', $key);
    $temp = &$config;

    foreach ($keys as $k) {
        if (!isset($temp[$k]) || !is_array($temp[$k])) {
            $temp[$k] = [];
        }
        $temp = &$temp[$k];
    }

    $temp = $value;
}
