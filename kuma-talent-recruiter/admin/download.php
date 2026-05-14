<?php
require_once 'auth.php';

$file = basename($_GET['file'] ?? '');
$type = $_GET['type'] ?? 'pdf';

if (!$file || !preg_match('/^[a-zA-Z0-9_\-\.]+$/', $file)) {
    http_response_code(400); exit;
}

$path = REPORTS_DIR . $file;
if (!file_exists($path)) {
    http_response_code(404); echo 'File not found.'; exit;
}

$mime = $type === 'txt' ? 'text/plain; charset=utf-8' : 'application/pdf';
header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
