<?php
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Diagnóstico</title></head>
<body>
<h2>Kuma Talent — Diagnóstico</h2>
<pre>
<?php

// 1. Archivos clave
echo "=== ARCHIVOS ===\n";
$files = [
    'config.php', 'index.php', 'chat.php',
    'api/start.php', 'api/message.php', 'api/report.php',
    'vendor/autoload.php',
    'assets/css/style.css', 'assets/js/app.js',
];
foreach ($files as $f) {
    $exists = file_exists(__DIR__ . '/' . $f);
    echo ($exists ? '✓' : '✗ FALTA') . " $f\n";
}

// 2. Sesión
echo "\n=== SESIÓN ===\n";
session_start();
$_SESSION['job_title']       = 'TEST';
$_SESSION['job_description'] = 'Test description';
$_SESSION['candidate_name']  = 'Test User';
$_SESSION['language']        = 'auto';
$_SESSION['messages']        = [];
$sid = session_id();
session_write_close();

// Reabrir y verificar
session_start();
$ok = ($_SESSION['job_title'] ?? '') === 'TEST';
echo ($ok ? '✓' : '✗') . " Sesión read/write: " . ($ok ? 'OK' : 'FALLO') . "\n";
echo "✓ Session ID: $sid\n";

// Limpiar
unset($_SESSION['job_title'], $_SESSION['job_description'],
      $_SESSION['candidate_name'], $_SESSION['language'], $_SESSION['messages']);
session_write_close();

// 3. API Anthropic con streaming simulado
echo "\n=== API ANTHROPIC (streaming) ===\n";
$body = json_encode([
    'model'      => ANTHROPIC_MODEL,
    'max_tokens' => 40,
    'stream'     => true,
    'messages'   => [['role' => 'user', 'content' => 'Reply with exactly: STREAM_OK']],
]);

$collected = '';
$ch = curl_init(ANTHROPIC_API_URL);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $body,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'x-api-key: '         . ANTHROPIC_API_KEY,
        'anthropic-version: ' . ANTHROPIC_VERSION,
    ],
    CURLOPT_RETURNTRANSFER => false,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_WRITEFUNCTION  => function($ch, $chunk) use (&$collected) {
        $collected .= $chunk;
        return strlen($chunk);
    },
]);
curl_exec($ch);
$curlErr = curl_error($ch);
curl_close($ch);

if ($curlErr) {
    echo "✗ cURL streaming error: $curlErr\n";
} else {
    // Extraer texto de los eventos SSE
    $text = '';
    foreach (explode("\n", $collected) as $line) {
        if (strpos($line, 'data: ') === 0) {
            $e = json_decode(substr($line, 6), true);
            if (($e['type'] ?? '') === 'content_block_delta' && ($e['delta']['type'] ?? '') === 'text_delta') {
                $text .= $e['delta']['text'];
            }
        }
    }
    echo "✓ Streaming OK — Respuesta: $text\n";
}

// 4. PHP info relevante
echo "\n=== PHP ===\n";
echo "✓ Versión: " . PHP_VERSION . "\n";
echo "✓ output_buffering: " . ini_get('output_buffering') . "\n";
echo "✓ max_execution_time: " . ini_get('max_execution_time') . "s\n";
echo "✓ post_max_size: " . ini_get('post_max_size') . "\n";

?>
</pre>
<p><a href="index.php">← Volver al formulario</a></p>
</body>
</html>
