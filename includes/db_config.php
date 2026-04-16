<?php
function loadEnvFile($filePath) {
    if (!file_exists($filePath)) {
        return;
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }

        if (!preg_match('/^([A-Za-z0-9_]+)\s*=\s*(.*)$/', $line, $matches)) {
            continue;
        }

        $key = $matches[1];
        $value = $matches[2];

        if ((strlen($value) >= 2 && $value[0] === '"' && substr($value, -1) === '"') ||
            (strlen($value) >= 2 && $value[0] === "'" && substr($value, -1) === "'")) {
            $value = substr($value, 1, -1);
        }

        if (getenv($key) === false) {
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

function parseDatabaseUrl($databaseUrl) {
    $parts = parse_url($databaseUrl);
    if (!$parts || empty($parts['host'])) {
        return null;
    }

    return [
        'host' => $parts['host'],
        'user' => isset($parts['user']) ? $parts['user'] : null,
        'pass' => isset($parts['pass']) ? $parts['pass'] : null,
        'name' => isset($parts['path']) ? ltrim($parts['path'], '/') : null,
        'port' => isset($parts['port']) ? $parts['port'] : null,
    ];
}

loadEnvFile(__DIR__ . '/../.env');

$db_host = getenv('DB_HOST');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');
$db_name = getenv('DB_NAME');
$db_port = getenv('DB_PORT');

$databaseUrl = getenv('DATABASE_URL');
if (!$db_host && $databaseUrl) {
    $parsed = parseDatabaseUrl($databaseUrl);
    if ($parsed) {
        $db_host = $parsed['host'];
        $db_user = $parsed['user'];
        $db_pass = $parsed['pass'];
        $db_name = $parsed['name'];
        $db_port = $db_port ?: $parsed['port'];
    }
}

$db_host = $db_host ?: 'localhost';
$db_user = $db_user ?: 'root';
$db_pass = $db_pass ?: '';
$db_name = $db_name ?: 'tms_database';
$db_port = $db_port ?: 3306;

try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    $conn->set_charset('utf8mb4');
} catch (Exception $e) {
    error_log('Database connection error: ' . $e->getMessage());
    error_log('Database config: host=' . $db_host . ' name=' . $db_name . ' port=' . $db_port);
    die('Database connection failed');
}
?>
