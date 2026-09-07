<?php
echo "<h1>✅ PHP está funcionando correctamente!</h1>";
echo "<p>Si ves este mensaje, PHP está configurado.</p>";

// Probar escritura de archivos
$test_file = 'test_escritura.txt';
if (file_put_contents($test_file, 'Prueba: ' . date('Y-m-d H:i:s')) !== false) {
    echo "<p style='color: green;'>✅ Escritura de archivos funciona</p>";
    // Leer el archivo para confirmar
    $contenido = file_get_contents($test_file);
    echo "<p>Contenido del archivo: " . htmlspecialchars($contenido) . "</p>";
} else {
    echo "<p style='color: red;'>❌ Error en escritura de archivos</p>";
}

// Mostrar información del directorio
echo "<p>Directorio actual: " . __DIR__ . "</p>";
echo "<p>Permisos de escritura: " . (is_writable('.') ? '✅ Sí' : '❌ No') . "</p>";
?>