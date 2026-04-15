<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
header('Content-Type: application/json');

if ($uri === '/health') {
    echo json_encode(['status' => 'ok']);
    exit;
}

if ($uri === '/status') {
    $result = ['service' => 'appdev', 'status' => 'ok', 'connections' => []];
    try {
        $host = getenv('DB_HOST') ?: 'db';
        $port = getenv('DB_PORT') ?: '5432';
        $db   = getenv('DB_DATABASE') ?: 'db';
        $user = getenv('DB_USERNAME') ?: '';
        $pass = getenv('DB_PASSWORD') ?: '';
        $t = microtime(true);
        $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass, [PDO::ATTR_TIMEOUT => 5]);
        $pdo->query('SELECT 1');
        $ms = round((microtime(true) - $t) * 1000);
        $result['connections']['db'] = ['status' => 'ok', 'latency_ms' => $ms];
    } catch (Exception $e) {
        $result['connections']['db'] = ['status' => 'error', 'error' => $e->getMessage()];
    }
    echo json_encode($result);
    exit;
}

echo json_encode(['service' => 'appdev', 'message' => 'Service: appdev']);
