<?php
// Forzar JSON limpio
header('Content-Type: application/json');

// Evitar warnings
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

// Logging
function debug($label, $data) {
    error_log("$label: " . json_encode($data));
}

$logFile = __DIR__ . '/chat_log.jsonl';

function logChat($userMessage, $botMessage) {
    global $logFile;
    $entry = [
        "timestamp" => date("Y-m-d H:i:s"),
        "user" => $userMessage,
        "bot" => $botMessage
    ];
    // Añadir línea al final del archivo
    file_put_contents($logFile, json_encode($entry, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND | LOCK_EX);
}

// Recibir mensaje del frontend
$input = json_decode(file_get_contents("php://input"), true);
if (!$input || !isset($input["message"])) {
    echo json_encode(["error" => "No message received"]);
    exit;
}
$message = $input["message"];

// -------------------------------
// CONFIG
// -------------------------------
$apiKey = "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";
$assistantId = "asst_TAj07ZY0njNTGHIQq8axBkhr";
$apiBase = "https://api.openai.com/v1";

// -------------------------------
// cURL POST
// -------------------------------
function curlPost($url, $apiKey, $data = []) {
    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Authorization: Bearer $apiKey",
            "OpenAI-Beta: assistants=v2"
        ],
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);
    $resp = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    return $err ? ["error" => $err] : json_decode($resp, true);
}

// -------------------------------
// cURL GET
// -------------------------------
function curlGet($url, $apiKey) {
    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Authorization: Bearer $apiKey",
            "OpenAI-Beta: assistants=v2"
        ]
    ]);
    $resp = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    return $err ? ["error" => $err] : json_decode($resp, true);
}

// -------------------------------
// 1️⃣ Crear Thread
// -------------------------------
$threadResp = curlPost("$apiBase/threads", $apiKey);
debug("Thread response", $threadResp);
$threadId = $threadResp['id'] ?? null;
if (!$threadId) {
    echo json_encode(["error" => "No se pudo crear thread"]);
    exit;
}

// -------------------------------
// 2️⃣ Añadir mensaje
// -------------------------------
$msgResp = curlPost("$apiBase/threads/$threadId/messages", $apiKey, [
    "role" => "user",
    "content" => [
        ["type" => "text", "text" => "Responde en máximo 3 líneas, sin formato y sin referencias. Pregunta: $message"]
    ]
]);
debug("Add message response", $msgResp);

// -------------------------------
// 3️⃣ Ejecutar Run
// -------------------------------
$runResp = curlPost("$apiBase/threads/$threadId/runs", $apiKey, [
    "assistant_id" => $assistantId
]);
debug("Run response", $runResp);
$runId = $runResp['id'];

// -------------------------------
// 4️⃣ Esperar hasta que el run termine
// -------------------------------
$status = "queued";
$intentos = 0;

while ($status !== "completed" && $intentos < 20) {
    sleep(1);
    $check = curlGet("$apiBase/threads/$threadId/runs/$runId", $apiKey);
    debug("Polling", $check);
    $status = $check["status"] ?? "queued";
    $intentos++;
}

if ($status !== "completed") {
    echo json_encode(["error" => "Run no completado"]);
    exit;
}

// -------------------------------
// 5️⃣ Obtener la respuesta final del thread
// -------------------------------
$messages = curlGet("$apiBase/threads/$threadId/messages", $apiKey);
debug("Final messages", $messages);

$outputText = "";
if (isset($messages["data"])) {
    foreach ($messages["data"] as $m) {
        if ($m["role"] === "assistant" && isset($m["content"][0]["text"]["value"])) {
            $outputText = $m["content"][0]["text"]["value"];
            break;
        }
    }
}

logChat($message, $outputText);

// -------------------------------
// 6️⃣ Enviar respuesta al frontend
// -------------------------------
echo json_encode([
    "output" => [
        ["content" => [["text" => $outputText]]]
    ]
]);
?>