<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $clientId = intval($_GET['client_id'] ?? 0);
    if (!$clientId) { echo json_encode(['error' => 'client_id requerido']); exit; }

    $where  = 'WHERE l.client_id = ?';
    $params = [$clientId];

    if (!empty($_GET['categoria'])) {
        $where .= ' AND l.categoria = ?';
        $params[] = strtoupper($_GET['categoria']);
    }
    if (!empty($_GET['status'])) {
        $where .= ' AND l.status = ?';
        $params[] = $_GET['status'];
    }
    if (!empty($_GET['q'])) {
        $where .= ' AND (l.empresa LIKE ? OR l.sector LIKE ? OR l.ciudad LIKE ?)';
        $q = '%' . $_GET['q'] . '%';
        $params = array_merge($params, [$q, $q, $q]);
    }

    $stmt = $db->prepare("
        SELECT l.*, c.name AS client_name
        FROM leads l
        JOIN clients c ON c.id = l.client_id
        {$where}
        ORDER BY l.score DESC, l.created_at DESC
    ");
    $stmt->execute($params);
    $leads = $stmt->fetchAll();

    echo json_encode(['success' => true, 'leads' => $leads]);
    exit;
}

if ($method === 'POST') {
    $input  = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $input['action'] ?? '';

    if ($action === 'update_status') {
        $id     = intval($input['id']     ?? 0);
        $status = $input['status'] ?? '';
        $valid  = ['nuevo', 'revisado', 'contactado', 'en_proceso', 'cerrado'];

        if (!$id || !in_array($status, $valid)) {
            echo json_encode(['error' => 'Parámetros inválidos']); exit;
        }

        $db->prepare("UPDATE leads SET status=?, updated_at=CURRENT_TIMESTAMP WHERE id=?")
           ->execute([$status, $id]);
        echo json_encode(['success' => true]);
        exit;
    }

    if ($action === 'update_notes') {
        $id    = intval($input['id'] ?? 0);
        $notes = trim($input['notas'] ?? '');

        if (!$id) { echo json_encode(['error' => 'id requerido']); exit; }

        $db->prepare("UPDATE leads SET notas=?, updated_at=CURRENT_TIMESTAMP WHERE id=?")
           ->execute([$notes, $id]);
        echo json_encode(['success' => true]);
        exit;
    }
}

echo json_encode(['error' => 'Method not allowed']);
