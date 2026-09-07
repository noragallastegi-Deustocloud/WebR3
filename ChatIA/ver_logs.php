<?php
// -------------------------------
// Configuración de usuario y contraseña
// -------------------------------
$USER = "ProyectoR3Admin";    
$PASS = "R3JZVC2025";

// -------------------------------
// Autenticación básica
// -------------------------------
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] !== $USER || $_SERVER['PHP_AUTH_PW'] !== $PASS) {
    header('WWW-Authenticate: Basic realm="Logs Chat Proyecto R3"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Acceso denegado.';
    exit;
}

// -------------------------------
// Archivo de logs
// -------------------------------
$logFile = __DIR__ . '/chat_log.jsonl';

if (!file_exists($logFile)) {
    echo "No hay logs todavía.";
    exit;
}

// -------------------------------
// Leer y mostrar logs
// -------------------------------
$lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
echo "<h1>Logs del Chat</h1>";
echo "<ul>";
foreach ($lines as $line) {
    $entry = json_decode($line, true);
    if ($entry) {
        echo "<li><strong>Usuario:</strong> " . htmlspecialchars($entry['user']) . "<br>";
        echo "<strong>Bot:</strong> " . htmlspecialchars($entry['bot']) . "<br>";
        echo "<em>" . $entry['timestamp'] . "</em></li><hr>";
    }
}
echo "</ul>";
?>
