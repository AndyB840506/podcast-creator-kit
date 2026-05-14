<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$code = trim($_GET['code'] ?? '');

if ($code === '') {
    echo json_encode(['found' => false]);
    exit;
}

$job = getJob($code);

if ($job === null) {
    echo json_encode(['found' => false]);
    exit;
}

$canonicalCode = getJobCanonicalCode($code);

echo json_encode([
    'found' => true,
    'code'  => $canonicalCode,
    'title' => $job['title'],
]);
