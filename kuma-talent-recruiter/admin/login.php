<?php
require_once __DIR__ . '/../config.php';
session_start();

if (!empty($_SESSION['admin_auth'])) {
    header('Location: index.php'); exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['password'] ?? '') === ADMIN_PASSWORD) {
        $_SESSION['admin_auth'] = true;
        header('Location: index.php'); exit;
    }
    $error = 'Incorrect password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kuma Talent — Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:Inter,sans-serif;background:#f0f4f8;display:flex;align-items:center;justify-content:center;min-height:100vh}
    .card{background:#fff;border-radius:12px;padding:44px 40px;width:380px;box-shadow:0 4px 24px rgba(0,0,0,.09)}
    .brand{font-size:22px;font-weight:700;color:#1e3a5f}
    .sub{font-size:13px;color:#64748b;margin:4px 0 28px}
    label{display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px}
    input[type=password]{width:100%;padding:10px 12px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;font-family:inherit;outline:none;transition:.15s}
    input[type=password]:focus{border-color:#1e3a5f;box-shadow:0 0 0 3px rgba(30,58,95,.1)}
    .btn{width:100%;margin-top:16px;padding:11px;background:#1e3a5f;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;transition:.15s}
    .btn:hover{background:#16305a}
    .error{background:#fef2f2;color:#dc2626;font-size:13px;padding:10px 12px;border-radius:8px;margin-bottom:16px}
  </style>
</head>
<body>
  <div class="card">
    <div class="brand">Kuma Talent</div>
    <div class="sub">Job Manager — Admin Access</div>
    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
      <label for="pw">Password</label>
      <input type="password" id="pw" name="password" autofocus autocomplete="current-password">
      <button type="submit" class="btn">Sign In &rarr;</button>
    </form>
  </div>
</body>
</html>
