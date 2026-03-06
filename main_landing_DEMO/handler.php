<?php
// ЭТАЛОН-СТИЛЬ ОБРАБОТЧИКА: МИНИМАЛЬНЫЙ, САМОСТОЯТЕЛЬНЫЙ, НАДЕЖНЫЙ
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Разрешаем только POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Логи
$log_dir = 'logs';
if (!is_dir($log_dir)) { @mkdir($log_dir, 0777, true); }

function logMessage($level, $source, $message, $data = null) {
    global $log_dir;
    $ts = date('H:i:s');
    $line = "{$ts}\t{$level}\t{$source}\t{$message}"; // табулированный лог
    if (is_array($data) && !empty($data)) {
        // Нормализуем ключи: http_code, url, params_keys_count, body, затем остальные по алфавиту
        $norm = [];
        if (isset($data['http_code'])) $norm['http_code'] = $data['http_code'];
        if (isset($data['url'])) $norm['url'] = $data['url'];
        if (isset($data['params_keys'])) {
            $count = is_array($data['params_keys']) ? count($data['params_keys']) : 0;
            $norm['params_count'] = $count;
            unset($data['params_keys']);
        }
        if (isset($data['body'])) {
            $body = is_string($data['body']) ? preg_replace('/\s+/', ' ', $data['body']) : '[non-string]';
            if (strlen($body) > 300) $body = substr($body, 0, 300) . '…';
            $norm['body'] = $body;
            unset($data['body']);
        }
        // Добавляем оставшиеся поля по алфавиту
        ksort($data);
        foreach ($data as $k => $v) {
            if (is_array($v)) $v = json_encode($v, JSON_UNESCAPED_UNICODE);
            $norm[$k] = $v;
        }
        foreach ($norm as $k => $v) {
            $line .= "\t{$k}={$v}";
        }
    } elseif ($data !== null) {
        $line .= "\t" . (string)$data;
    }
    @file_put_contents($log_dir . '/handler.log', $line . "\n", FILE_APPEND);
}

function flattenParams($arr) {
    if (!is_array($arr)) return (string)$arr;
    ksort($arr);
    $pairs = [];
    foreach ($arr as $k => $v) {
        if (is_array($v)) $v = json_encode($v, JSON_UNESCAPED_UNICODE);
        $pairs[] = $k . '=' . $v;
    }
    $s = implode('&', $pairs);
    if (strlen($s) > 1200) $s = substr($s, 0, 1200) . '…';
    return $s;
}

// Красивые блочные логи вида:
// keitaro\n{\n  key = value\n  ...\n}\nresponse\n{\n  http_code = 200\n  body = ...\n}
function logKVBlock($section, $title, $assoc) {
    global $log_dir;
    $ts = date('H:i:s');
    $lines = [];
    $lines[] = $ts . "\t" . strtolower($section);
    $lines[] = strtolower($title);
    $lines[] = '{';
    if (is_array($assoc)) {
        ksort($assoc);
        $maxLen = 0;
        foreach ($assoc as $k => $_) { $len = strlen((string)$k); if ($len > $maxLen) $maxLen = $len; }
        if ($maxLen > 28) $maxLen = 28; // ограничим ширину ключа
        foreach ($assoc as $k => $v) {
            if (is_array($v)) { $v = json_encode($v, JSON_UNESCAPED_UNICODE); }
            if (is_string($v)) {
                $v = preg_replace('/\s+/', ' ', $v);
                if (strlen($v) > 2000) $v = substr($v, 0, 2000) . '…';
            }
            $key = str_pad((string)$k, $maxLen, ' ', STR_PAD_RIGHT);
            $lines[] = '  ' . $key . ' = ' . $v;
        }
    }
    $lines[] = '}';
    $lines[] = '';
    @file_put_contents($log_dir . '/handler.log', implode("\n", $lines) . "\n", FILE_APPEND);
}

function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']); return trim($ipList[0]); }
    return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
}

function getRequestValue($key, $default = '') {
    if (isset($_POST[$key]) && $_POST[$key] !== '' && $_POST[$key] !== "{{$key}}") return htmlspecialchars($_POST[$key]);
    if (isset($_GET[$key]) && $_GET[$key] !== '' && $_GET[$key] !== "{{$key}}") return htmlspecialchars($_GET[$key]);
    return $default;
}

function isDuplicateLead($phone, $timeout = 86400) {
    $shared_dir = '../shared';
    if (!is_dir($shared_dir)) { @mkdir($shared_dir, 0777, true); }
    $processed_file = $shared_dir . '/processed_phones.json';
    $lock_file = $processed_file . '.lock';
    $lock = @fopen($lock_file, 'w');
    if (!$lock) return false;
    @flock($lock, LOCK_EX);
    $data = [];
    if (file_exists($processed_file)) {
        $content = @file_get_contents($processed_file);
        $data = json_decode($content, true) ?: [];
    }
    $normalized = preg_replace('/[^0-9]/', '', $phone);
    if (isset($data[$normalized]) && (time() - (int)$data[$normalized]) < $timeout) {
        @flock($lock, LOCK_UN); @fclose($lock); @unlink($lock_file);
        return true;
    }
    $data[$normalized] = time();
    @file_put_contents($processed_file, json_encode($data, JSON_UNESCAPED_UNICODE));
    @flock($lock, LOCK_UN); @fclose($lock); @unlink($lock_file);
    return false;
}

// Данные формы
$name  = isset($_POST['name']) ? trim($_POST['name']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

if ($name === '' || $phone === '') {
    header('Location: index.php?error=' . urlencode('Invalid form'));
    exit;
}

$data = [
    'name' => htmlspecialchars($name),
    'phone' => htmlspecialchars($phone),
    'clickid' => getRequestValue('sub1') ?: getRequestValue('subid') ?: getRequestValue('clickid'),
    'fbpxl' => getRequestValue('pixel') ?: getRequestValue('sub2') ?: getRequestValue('fbpxl'),
    'cost' => 2499,
    'offer' => 'Demo Product',
    'ip' => getClientIP(),
    'timestamp' => date('Y-m-d H:i:s')
];

logKVBlock('FORM', 'received', $data);
@file_put_contents(
    $log_dir . '/leads.log',
    date('Y-m-d H:i:s') . "\tLEAD\tphone=" . $data['phone'] . "\tname=" . $data['name'] . "\tclickid=" . ($data['clickid'] ?: 'none') . "\tip=" . $data['ip'] . "\n",
    FILE_APPEND
);

// Дубликат — сразу на success без трекеров (как в эталоне)
if (isDuplicateLead($data['phone'])) {
    $params = ['phone' => $data['phone'], 'name' => $data['name'], 'duplicate' => '1'];
    if (!empty($data['fbpxl'])) $params['pixel'] = $data['fbpxl'];
    header('Location: success.php?' . http_build_query($params));
    exit;
}

// Leadbit (эталонная схема)
try {
    $leadbit = [
        'flow_hash' => getRequestValue('flow_hash'),
        'landing'   => getRequestValue('landing') ?: (isset($_SERVER['HTTP_HOST']) ? ('https://' . $_SERVER['HTTP_HOST']) : ''),
        'phone'     => $data['phone'],
        'name'      => $data['name'],
        'country'   => 'IN',
        'referrer'  => getRequestValue('referrer'),
        'address'   => getRequestValue('address'),
        'email'     => getRequestValue('email'),
        'lastname'  => getRequestValue('lastname'),
        'comment'   => getRequestValue('comment'),
        'layer'     => getRequestValue('layer'),
        'sub1'      => getRequestValue('sub1') ?: $data['clickid'],
        'sub2'      => getRequestValue('sub2'),
        'sub3'      => getRequestValue('sub3'),
        'sub4'      => 'IN',
        'sub5'      => getRequestValue('sub5'),
    ];
    $url = 'http://wapi.leadbit.com/api/pub/new-order/_67bc70cc74af8662538029';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $leadbit);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    logKVBlock('LEADBIT', 'request', ['url' => $url] + $leadbit);
    if ($err) {
        logMessage('ERROR', 'LEADBIT', 'Ошибка', ['error' => $err, 'code' => $code]);
    } else {
        logKVBlock('LEADBIT', 'response', [
            'http_code' => $code,
            'body' => is_string($res) ? $res : '[non-string]'
        ]);
    }
} catch (Throwable $e) { logMessage('ERROR', 'LEADBIT', 'Exception: ' . $e->getMessage()); }

// Keitaro постбек — как в эталоне, без фантазий
try {
    $k = ['subid' => $data['clickid'], 'status' => 'lead', 'payout' => $data['cost']];
    if (!empty($data['fbpxl'])) $k['fbpxl'] = $data['fbpxl'];
    foreach (['sub3','sub5'] as $sk) { $v = getRequestValue($sk); if (!empty($v)) $k[$sk] = $v; }
    $k['sub4'] = 'IN';
    $k_query = http_build_query($k);
    $k_url = 'http://217.114.12.132/2083254/postback?' . $k_query;
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $k_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
        CURLOPT_TIMEOUT => 10,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0
    ]);
    $k_res = curl_exec($ch);
    curl_close($ch);
    logKVBlock('KEITARO', 'request', ['url' => 'http://217.114.12.132/2083254/postback'] + $k);
    if (is_string($k_res)) {
        logKVBlock('KEITARO', 'response', ['body' => $k_res]);
    }
} catch (Throwable $e) { logMessage('ERROR', 'KEITARO', 'Exception: ' . $e->getMessage()); }

// Facebook pixel ping (серверный пинг, как в эталоне)
try {
    $pixel_id = '';
    if (!empty($data['fbpxl'])) $pixel_id = $data['fbpxl'];
    elseif (!empty($_POST['pixel'])) $pixel_id = htmlspecialchars($_POST['pixel']);
    if (!empty($pixel_id)) {
        $purl = "https://www.facebook.com/tr?id={$pixel_id}&ev=Lead&noscript=1";
        $ch = curl_init();
        curl_setopt_array($ch,[CURLOPT_URL=>$purl,CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>5,CURLOPT_CONNECTTIMEOUT=>5,CURLOPT_SSL_VERIFYPEER=>false,CURLOPT_SSL_VERIFYHOST=>0]);
        $p_res = curl_exec($ch); $p_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
        logKVBlock('FACEBOOK', 'request', ['url' => $purl, 'pixel_id' => $pixel_id]);
        logKVBlock('FACEBOOK', 'response', ['http_code' => $p_code]);
    }
} catch (Throwable $e) { logMessage('ERROR', 'FACEBOOK', 'Exception: ' . $e->getMessage()); }

// Редирект на классический success.php (эталон)
$redirect = ['phone'=>$data['phone'],'name'=>$data['name']];
if (!empty($pixel_id)) $redirect['pixel'] = $pixel_id;
if (!empty($data['clickid'])) $redirect['clickid'] = $data['clickid'];
logKVBlock('REDIRECT', 'success.php', $redirect);
header('Location: success.php?' . http_build_query($redirect));
exit;