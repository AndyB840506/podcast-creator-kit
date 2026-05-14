<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

$db     = getDB();
$input  = json_decode(file_get_contents('php://input'), true) ?? [];
$method = $_SERVER['REQUEST_METHOD'];
$action = $input['action'] ?? $_GET['action'] ?? '';

// ── GET: listar usuarios ──────────────────────────────────────
if ($method === 'GET' && $action === 'list') {
    $rows = $db->query("
        SELECT u.*, c.name, c.propuesta_valor,
               (u.plan_leads - u.leads_usados) AS leads_disponibles
        FROM users u
        JOIN clients c ON c.id = u.client_id
        ORDER BY c.name
    ")->fetchAll();
    echo json_encode(['success' => true, 'users' => $rows]);
    exit;
}

// ── POST: crear usuario/cliente ───────────────────────────────
if ($method === 'POST' && $action === 'create') {
    $name           = trim($input['name'] ?? '');
    $propuesta      = trim($input['propuesta_valor'] ?? '');
    $plan_nombre    = trim($input['plan_nombre'] ?? 'Básico');
    $plan_leads     = intval($input['plan_leads'] ?? 100);
    $pais_default   = trim($input['pais_default'] ?? 'Colombia');

    if (!$name) { echo json_encode(['error' => 'Nombre requerido']); exit; }

    try {
        // Crear client
        $db->prepare("INSERT OR IGNORE INTO clients (name, propuesta_valor) VALUES (?, ?)")
           ->execute([$name, $propuesta]);
        $clientId = $db->lastInsertId();
        if (!$clientId) {
            $clientId = $db->query("SELECT id FROM clients WHERE name=" . $db->quote($name))->fetchColumn();
        }

        // Crear carpeta leads
        $dir = LEADS_DIR . sanitizeFilename($name) . '/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        // Crear user con token
        $token = generarToken();
        $db->prepare("
            INSERT INTO users (client_id, token, plan_nombre, plan_leads, pais_default)
            VALUES (?, ?, ?, ?, ?)
        ")->execute([$clientId, $token, $plan_nombre, $plan_leads, $pais_default]);

        echo json_encode([
            'success'  => true,
            'token'    => $token,
            'url'      => 'c/' . $token,
            'client_id'=> $clientId,
        ]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// ── POST: actualizar usuario ──────────────────────────────────
if ($method === 'POST' && $action === 'update') {
    $id          = intval($input['id'] ?? 0);
    $plan_nombre = trim($input['plan_nombre'] ?? '');
    $plan_leads  = intval($input['plan_leads'] ?? 100);
    $activo      = intval($input['activo'] ?? 1);
    $propuesta   = trim($input['propuesta_valor'] ?? '');
    $pais_default= trim($input['pais_default'] ?? 'Colombia');

    if (!$id) { echo json_encode(['error' => 'ID requerido']); exit; }

    $db->prepare("UPDATE users SET plan_nombre=?, plan_leads=?, activo=?, pais_default=? WHERE id=?")
       ->execute([$plan_nombre, $plan_leads, $activo, $pais_default, $id]);

    // Actualizar propuesta en client
    $clientId = $db->query("SELECT client_id FROM users WHERE id=$id")->fetchColumn();
    if ($clientId) {
        $db->prepare("UPDATE clients SET propuesta_valor=? WHERE id=?")
           ->execute([$propuesta, $clientId]);
    }

    echo json_encode(['success' => true]);
    exit;
}

// ── POST: reset manual de leads ───────────────────────────────
if ($method === 'POST' && $action === 'reset_leads') {
    $id = intval($input['id'] ?? 0);
    if (!$id) { echo json_encode(['error' => 'ID requerido']); exit; }
    $db->prepare("UPDATE users SET leads_usados=0, fecha_reset=? WHERE id=?")
       ->execute([date('Y-m-01'), $id]);
    echo json_encode(['success' => true]);
    exit;
}

// ── POST: toggle activo ───────────────────────────────────────
if ($method === 'POST' && $action === 'toggle') {
    $id = intval($input['id'] ?? 0);
    if (!$id) { echo json_encode(['error' => 'ID requerido']); exit; }
    $db->prepare("UPDATE users SET activo = CASE WHEN activo=1 THEN 0 ELSE 1 END WHERE id=?")
       ->execute([$id]);
    $activo = $db->query("SELECT activo FROM users WHERE id=$id")->fetchColumn();
    echo json_encode(['success' => true, 'activo' => $activo]);
    exit;
}

// ── POST: guardar API key ─────────────────────────────────────
if ($method === 'POST' && $action === 'save_api_key') {
    $key = trim($input['api_key'] ?? '');
    if (!$key) { echo json_encode(['error' => 'API key requerida']); exit; }
    $dataDir = __DIR__ . '/../data/';
    if (!is_dir($dataDir)) mkdir($dataDir, 0755, true);
    $settingsFile = $dataDir . 'settings.json';
    $current = file_exists($settingsFile) ? (json_decode(file_get_contents($settingsFile), true) ?? []) : [];
    $current['anthropic_api_key'] = $key;
    file_put_contents($settingsFile, json_encode($current, JSON_PRETTY_PRINT));
    echo json_encode(['success' => true]);
    exit;
}

// ── GET: resumen de costos del mes ────────────────────────────
if ($method === 'GET' && $action === 'costs_summary') {
    $mesActual = date('Y-m-01');
    $row = $db->prepare("
        SELECT COUNT(id) AS reports,
               SUM(leads_count) AS leads,
               SUM(tokens_input) AS tokens_in,
               SUM(tokens_output) AS tokens_out,
               ROUND(SUM(costo_usd), 4) AS costo_total
        FROM reports WHERE created_at >= ?
    ");
    $row->execute([$mesActual]);
    $summary = $row->fetch();
    $summary['nombre']  = 'Claude Sonnet 4.6';
    $summary['model']   = 'claude-sonnet-4-6';
    echo json_encode(['success' => true, 'summary' => [$summary]]);
    exit;
}

echo json_encode(['error' => 'Acción no válida']);
