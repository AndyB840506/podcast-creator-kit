<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $rows = $db->query("
        SELECT c.id, c.name,
               COUNT(DISTINCT l.id)   AS leads_count,
               COUNT(DISTINCT r.id)   AS reports_count,
               SUM(l.categoria='PREMIUM') AS premium,
               SUM(l.categoria='HOT')     AS hot
        FROM clients c
        LEFT JOIN leads   l ON l.client_id = c.id
        LEFT JOIN reports r ON r.client_id = c.id
        GROUP BY c.id
        ORDER BY c.name
    ")->fetchAll();

    echo json_encode(['success' => true, 'clients' => $rows]);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $name  = trim($input['name'] ?? '');

    if (!$name) { echo json_encode(['error' => 'Nombre requerido']); exit; }
    if (strlen($name) > 80) { echo json_encode(['error' => 'Nombre demasiado largo']); exit; }

    try {
        $db->prepare("INSERT INTO clients (name) VALUES (?)")->execute([$name]);
        $id = $db->lastInsertId();

        // Create leads folder for client
        $dir = LEADS_DIR . sanitizeFilename($name) . '/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        echo json_encode(['success' => true, 'id' => $id, 'name' => $name]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'El cliente ya existe']);
    }
    exit;
}

echo json_encode(['error' => 'Method not allowed']);
