<?php
// ver_respuestas.php - Panel de administración
$file = 'respuestas.txt';

if (!file_exists($file)) {
    die("No hay respuestas guardadas aún.");
}

$content = file_get_contents($file);
$lines = explode("\n", $content);

echo "<h1>Respuestas del Cuestionario</h1>";
echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>";

foreach ($lines as $index => $line) {
    if (empty(trim($line))) continue;
    
    $cells = str_getcsv($line);
    
    if ($index === 0) {
        // Cabecera
        echo "<tr style='background-color: #f2f2f2; font-weight: bold;'>";
        foreach ($cells as $cell) {
            echo "<th>" . htmlspecialchars($cell) . "</th>";
        }
        echo "</tr>";
    } else {
        // Datos
        echo "<tr>";
        foreach ($cells as $cell) {
            echo "<td>" . htmlspecialchars($cell) . "</td>";
        }
        echo "</tr>";
    }
}

echo "</table>";

// Mostrar estadísticas
$total_respuestas = count($lines) - 1; // Resta la cabecera
echo "<p><strong>Total de respuestas:</strong> " . $total_respuestas . "</p>";

// Botón para descargar
echo "<br><a href='respuestas.txt' download style='padding: 10px 15px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Descargar CSV</a>";
?>