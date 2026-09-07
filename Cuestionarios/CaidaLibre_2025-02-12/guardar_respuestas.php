<?php
// CONFIGURACIÓN BÁSICA
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// RECIBIR DATOS
$json = file_get_contents('php://input');
$datos = json_decode($json, true);

// VERIFICAR DATOS
if (!$datos) {
    http_response_code(400);
    echo json_encode(['error' => 'No se recibieron datos']);
    exit;
}

// ARCHIVO CSV
$archivo_csv = 'respuestas.csv';

// CAMPOS
$campos = ['date', 'start', 'name', 'school', 'age', 'r1', 'r2', 'r3', 'r4', 'r5', 'r6'];

// CREAR CABECERA SI NO EXISTE
if (!file_exists($archivo_csv) || filesize($archivo_csv) == 0) {
    $cabecera = implode(",", $campos) . "\n";
    file_put_contents($archivo_csv, $cabecera, LOCK_EX);
}

// PREPARAR LÍNEA
$linea = [];
foreach ($campos as $campo) {
    $valor = isset($datos[$campo]) ? $datos[$campo] : '';
    $linea[] = '"' . str_replace('"', '""', $valor) . '"';
}

// GUARDAR
$nueva_linea = implode(",", $linea) . "\n";

if (file_put_contents($archivo_csv, $nueva_linea, FILE_APPEND | LOCK_EX)) {
    echo json_encode(['success' => true, 'message' => 'Guardado correctamente']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al guardar archivo']);
}
?>