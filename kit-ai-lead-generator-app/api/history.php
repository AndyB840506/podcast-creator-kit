<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

$db       = getDB();
$clientId = intval($_GET['client_id'] ?? 0);

if (!$clientId) { echo json_encode(['error' => 'client_id requerido']); exit; }

$reports = $db->prepare("
    SELECT r.*,
           SUM(l.categoria='PREMIUM') AS cnt_premium,
           SUM(l.categoria='HOT')     AS cnt_hot,
           SUM(l.categoria='WARM')    AS cnt_warm,
           SUM(l.categoria='COLD')    AS cnt_cold
    FROM reports r
    LEFT JOIN leads l ON l.report_id = r.id
    WHERE r.client_id = ?
    GROUP BY r.id
    ORDER BY r.created_at DESC
");
$reports->execute([$clientId]);
$rows = $reports->fetchAll();

// Build client folder path for URLs
$clientName = '';
$c = $db->prepare("SELECT name FROM clients WHERE id=?");
$c->execute([$clientId]);
$cr = $c->fetch();
if ($cr) $clientName = sanitizeFilename($cr['name']);

foreach ($rows as &$r) {
    $r['html_url'] = $r['archivo_html']
        ? "leads/{$clientName}/{$r['archivo_html']}"
        : null;
    $r['csv_url'] = $r['archivo_csv']
        ? "leads/{$clientName}/{$r['archivo_csv']}"
        : null;
}

echo json_encode(['success' => true, 'reports' => $rows]);
