<?php
// Configura la cabecera para permitir la comunicación con el JS
header('Content-Type: application/json');

// ⚠️ EN PRODUCCIÓN: Cambia el '*' por tu dominio específico para mayor seguridad
// header('Access-Control-Allow-Origin: https://tudominio.com');
header('Access-Control-Allow-Origin: *'); 

// 1. Recibe el cuerpo de la solicitud POST
$json_data = file_get_contents('php://input');

// 2. Decodifica el JSON
$data = json_decode($json_data, true);

if ($data === null) {
    http_response_code(400);
    echo json_encode(['message' => 'Error: Datos JSON no válidos.']);
    exit;
}

// 3. Define el archivo donde se guardarán las respuestas
$file = 'respuestas.csv';
$new_line = "";

// 4. Campos que esperamos
$fields = [
    'date', 'start', 'name', 'school', 'age', 
    'r1', 'r2', 'r3', 'r4', 'r5'
];

// 5. Si el archivo no existe, escribe la cabecera
if (!file_exists($file) || filesize($file) == 0) {
    $new_line .= implode(",", $fields) . "\n";
}

// 6. Prepara los datos
$row_values = [];
foreach ($fields as $field) {
    $value = isset($data[$field]) ? $data[$field] : '';
    // Limpia el valor y envuélvelo en comillas
    $row_values[] = '"' . str_replace('"', '""', $value) . '"';
}

$new_line .= implode(",", $row_values) . "\n";

// 7. Escribe en el archivo
if (file_put_contents($file, $new_line, FILE_APPEND | LOCK_EX) === false) {
    http_response_code(500);
    echo json_encode(['message' => 'Error al escribir en el archivo.']);
    exit;
}

// 8. Respuesta de éxito
http_response_code(200);
echo json_encode(['message' => 'Respuestas guardadas correctamente']);
?>