<?php
require_once 'auth.php';

$flash = '';
$error = '';

// Load current settings
$current = file_exists(SETTINGS_FILE)
    ? (json_decode(file_get_contents(SETTINGS_FILE), true) ?? [])
    : [];

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'test_email') {
    header('Location: test-email.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new = $current; // start from existing (preserves fields not in this form)

    // API
    $apiKey = trim($_POST['anthropic_api_key'] ?? '');
    if ($apiKey !== '' && $apiKey !== '••••') $new['anthropic_api_key'] = $apiKey;

    // Email
    $new['recruiter_email'] = trim($_POST['recruiter_email'] ?? '');
    $new['sender_name']     = trim($_POST['sender_name']     ?? '') ?: 'Kuma Talent Screener';
    $new['app_name']        = trim($_POST['app_name']        ?? '') ?: 'Kuma Talent';

    // SMTP
    $new['smtp_host']   = trim($_POST['smtp_host']   ?? '') ?: 'smtp.hostinger.com';
    $new['smtp_port']   = (int)(trim($_POST['smtp_port'] ?? '465') ?: 465);
    $new['smtp_secure'] = in_array($_POST['smtp_secure'] ?? '', ['ssl','tls','']) ? ($_POST['smtp_secure'] ?? 'ssl') : 'ssl';
    $new['smtp_user']   = trim($_POST['smtp_user']   ?? '');
    $smtpPass = trim($_POST['smtp_pass'] ?? '');
    if ($smtpPass !== '' && $smtpPass !== '••••') $new['smtp_pass'] = $smtpPass;

    // CC emails
    $new['cc_emails'] = trim($_POST['cc_emails'] ?? '');

    // LLM models fixed — always use best Claude models (configured in config.php)
    unset($new['llm_chat_provider'], $new['llm_chat_model'], $new['llm_chat_url'], $new['llm_chat_key']);
    unset($new['llm_report_provider'], $new['llm_report_model'], $new['llm_report_url'], $new['llm_report_key']);

    // Logo upload
    if (!empty($_FILES['logo']['tmp_name']) && !$_FILES['logo']['error']) {
        $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['png','jpg','jpeg'])) {
            $dataDir = __DIR__ . '/../data/';
            if (!is_dir($dataDir)) mkdir($dataDir, 0775, true);
            foreach (['png','jpg','jpeg'] as $e) {
                $old = $dataDir . 'logo.' . $e;
                if (file_exists($old)) unlink($old);
            }
            $logoFilename = 'logo.' . $ext;
            move_uploaded_file($_FILES['logo']['tmp_name'], $dataDir . $logoFilename);
            $new['logo_filename'] = $logoFilename;
        }
    }
    if (!empty($_POST['remove_logo'])) {
        foreach (['png','jpg','jpeg'] as $e) {
            $old = __DIR__ . '/../data/logo.' . $e;
            if (file_exists($old)) unlink($old);
        }
        unset($new['logo_filename']);
    }

    // Dev mode
    $new['dev_mode'] = !empty($_POST['dev_mode']);

    // Admin password
    $newPw  = trim($_POST['admin_password_new']    ?? '');
    $confPw = trim($_POST['admin_password_confirm'] ?? '');
    if ($newPw !== '') {
        if ($newPw !== $confPw) {
            $error = 'New passwords do not match.';
        } else {
            $new['admin_password'] = $newPw;
        }
    }

    if (!$error) {
        if (!is_dir(dirname(SETTINGS_FILE))) mkdir(dirname(SETTINGS_FILE), 0775, true);
        file_put_contents(SETTINGS_FILE, json_encode($new, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $current = $new;
        $_SESSION['flash'] = 'Settings saved successfully.';
        header('Location: settings.php'); exit;
    }
}

function mask(string $val): string {
    if ($val === '') return '';
    return '••••••••';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Settings — Kuma Talent Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:Inter,sans-serif;background:#f0f4f8;color:#1e293b;font-size:14px}
    .topbar{background:#1e3a5f;padding:0 32px;height:56px;display:flex;align-items:center;justify-content:space-between}
    .topbar-brand{font-size:17px;font-weight:700;color:#fff}
    .topbar-nav{display:flex;gap:24px;align-items:center}
    .nav-link{color:#94a3b8;font-size:13px;text-decoration:none;transition:.15s}
    .nav-link:hover,.nav-link.active{color:#fff}
    .main{max-width:720px;margin:32px auto;padding:0 24px}
    .page-title{font-size:20px;font-weight:700;color:#1e3a5f;margin-bottom:24px}
    .section{background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.07);padding:28px 32px;margin-bottom:20px}
    .section-title{font-size:12px;font-weight:700;color:#1e3a5f;text-transform:uppercase;letter-spacing:.6px;border-bottom:1px solid #e2e8f0;padding-bottom:10px;margin-bottom:20px}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px}
    .form-group{margin-bottom:18px}
    .form-group:last-child{margin-bottom:0}
    label{display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px}
    .hint{font-size:11px;color:#94a3b8;margin-top:4px}
    input[type=text],input[type=email],input[type=password],input[type=number],select{
      width:100%;padding:10px 12px;border:1.5px solid #d1d5db;border-radius:8px;
      font-size:14px;font-family:inherit;outline:none;transition:.15s;background:#fff}
    input:focus,select:focus{border-color:#1e3a5f;box-shadow:0 0 0 3px rgba(30,58,95,.1)}
    .pw-wrap{position:relative}
    .pw-wrap input{padding-right:44px}
    .pw-eye{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;font-size:13px;padding:0}
    .pw-eye:hover{color:#1e3a5f}
    .toggle-row{display:flex;align-items:center;gap:12px;padding:4px 0}
    .toggle-label{font-size:13px;color:#374151}
    input[type=checkbox]{width:16px;height:16px;cursor:pointer;accent-color:#1e3a5f}
    .status-dot{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:6px}
    .dot-green{background:#16a34a}
    .dot-amber{background:#d97706}
    .badge-set{display:inline-block;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;background:#f0fdf4;color:#16a34a}
    .badge-empty{display:inline-block;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;background:#fef9c3;color:#d97706}
    .flash{background:#f0fdf4;border-left:3px solid #16a34a;color:#15803d;padding:10px 14px;border-radius:6px;margin-bottom:20px;font-size:13px}
    .error-msg{background:#fef2f2;border-left:3px solid #dc2626;color:#dc2626;padding:10px 14px;border-radius:6px;margin-bottom:20px;font-size:13px}
    .btn-save{padding:11px 28px;background:#1e3a5f;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;transition:.15s}
    .btn-save:hover{background:#16305a}
    .warning-box{background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:12px 14px;font-size:13px;color:#92400e;margin-bottom:18px}
  </style>
</head>
<body>

<div class="topbar">
  <div class="topbar-brand">Kuma Talent &nbsp;/&nbsp; <span style="font-weight:400;color:#94a3b8">Admin</span></div>
  <div class="topbar-nav">
    <a href="index.php" class="nav-link">Jobs</a>
    <a href="settings.php" class="nav-link active">Settings</a>
    <a href="../index.php" class="nav-link">View site</a>
    <a href="logout.php" class="nav-link">Sign out</a>
  </div>
</div>

<div class="main">
  <div class="page-title">Settings</div>

  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="flash"><?= htmlspecialchars($_SESSION['flash']) ?></div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="error-msg"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">

    <!-- ── API Key ───────────────────────────────────────────── -->
    <div class="section">
      <div class="section-title">Anthropic API Key</div>

      <?php $hasKey = !empty($current['anthropic_api_key']); ?>
      <div style="margin-bottom:16px">
        Status: <?php if ($hasKey): ?>
          <span class="badge-set">Configured</span>
        <?php else: ?>
          <span class="badge-empty">Not set — interviews will fail</span>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label for="api_key">API Key</label>
        <div class="pw-wrap">
          <input type="password" id="api_key" name="anthropic_api_key"
                 placeholder="<?= $hasKey ? 'Leave blank to keep current key' : 'sk-ant-api03-...' ?>"
                 autocomplete="off">
          <button type="button" class="pw-eye" onclick="togglePw('api_key')">Show</button>
        </div>
        <div class="hint">Get your key at console.anthropic.com. Leave blank to keep the existing key.</div>
      </div>
    </div>

    <!-- LLM models are fixed in config.php — always Sonnet for chat, Opus for reports -->

    <!-- ── Email & Reports ──────────────────────────────────── -->
    <div class="section">
      <div class="section-title">Email &amp; Reports</div>

      <?php $emailReady = !empty($current['recruiter_email']) && !empty($current['smtp_user']); ?>
      <div style="margin-bottom:16px">
        Status: <?php if ($emailReady): ?>
          <span class="badge-set"><?= DEV_MODE ? 'Configured (DEV mode — emails not sent)' : 'Configured — sending enabled' ?></span>
        <?php else: ?>
          <span class="badge-empty">Incomplete — reports will only be saved locally</span>
        <?php endif; ?>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="recruiter_email">Recruiter / HR email *</label>
          <input type="email" id="recruiter_email" name="recruiter_email"
                 value="<?= htmlspecialchars($current['recruiter_email'] ?? '') ?>"
                 placeholder="hr@yourcompany.com">
          <div class="hint">Where PDF reports are sent after each interview.</div>
        </div>
        <div class="form-group">
          <label for="sender_name">Sender name</label>
          <input type="text" id="sender_name" name="sender_name"
                 value="<?= htmlspecialchars($current['sender_name'] ?? 'Kuma Talent Screener') ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="smtp_host">SMTP Host</label>
          <input type="text" id="smtp_host" name="smtp_host"
                 value="<?= htmlspecialchars($current['smtp_host'] ?? 'smtp.hostinger.com') ?>"
                 placeholder="smtp.hostinger.com">
        </div>
        <div class="form-row" style="gap:12px;align-items:end">
          <div class="form-group" style="margin-bottom:0">
            <label for="smtp_port">Port</label>
            <input type="number" id="smtp_port" name="smtp_port"
                   value="<?= (int)($current['smtp_port'] ?? 465) ?>"
                   placeholder="465" style="max-width:100px">
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label for="smtp_secure">Encryption</label>
            <select id="smtp_secure" name="smtp_secure">
              <option value="ssl"<?= ($current['smtp_secure'] ?? 'ssl') === 'ssl' ? ' selected' : '' ?>>SSL (465)</option>
              <option value="tls"<?= ($current['smtp_secure'] ?? '') === 'tls' ? ' selected' : '' ?>>TLS (587)</option>
            </select>
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="smtp_user">SMTP Username / Email</label>
          <input type="text" id="smtp_user" name="smtp_user"
                 value="<?= htmlspecialchars($current['smtp_user'] ?? '') ?>"
                 placeholder="noreply@yourcompany.com" autocomplete="off">
        </div>
        <div class="form-group">
          <label for="smtp_pass">SMTP Password</label>
          <div class="pw-wrap">
            <input type="password" id="smtp_pass" name="smtp_pass"
                   placeholder="<?= !empty($current['smtp_pass']) ? 'Leave blank to keep current' : 'SMTP password' ?>"
                   autocomplete="new-password">
            <button type="button" class="pw-eye" onclick="togglePw('smtp_pass')">Show</button>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="cc_emails">CC recipients <span style="font-weight:400;font-size:11px;color:#94a3b8">optional</span></label>
        <input type="text" id="cc_emails" name="cc_emails"
               value="<?= htmlspecialchars($current['cc_emails'] ?? '') ?>"
               placeholder="manager@company.com, director@company.com">
        <div class="hint">Comma-separated. These addresses receive a copy of every report.</div>
      </div>

      <div style="margin-bottom:18px">
        <button type="submit" name="action" value="test_email"
                style="padding:8px 16px;background:#f0fdf4;color:#16a34a;border:1.5px solid #bbf7d0;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">
          Send test email to <?= htmlspecialchars(RECRUITER_EMAIL ?: 'recruiter email') ?>
        </button>
        <div class="hint" style="margin-top:5px">Sends a test message using the current SMTP settings.</div>
      </div>

      <div class="form-group">
        <div class="toggle-row">
          <input type="checkbox" id="dev_mode" name="dev_mode" value="1"
                 <?= !empty($current['dev_mode']) ? 'checked' : '' ?>>
          <label class="toggle-label" for="dev_mode">
            DEV mode — save reports locally but <strong>do not send emails</strong>
          </label>
        </div>
        <div class="hint" style="margin-top:6px;margin-left:28px">Uncheck this to enable email delivery in production.</div>
      </div>
    </div>

    <!-- ── App ──────────────────────────────────────────────── -->
    <div class="section">
      <div class="section-title">App Configuration</div>

      <div class="form-group">
        <label for="app_name">Company / App name</label>
        <input type="text" id="app_name" name="app_name"
               value="<?= htmlspecialchars($current['app_name'] ?? 'Kuma Talent') ?>"
               placeholder="Kuma Talent" maxlength="60">
        <div class="hint">Used in email subjects and PDF headers.</div>
      </div>

      <div class="form-group">
        <label for="logo">Logo <span style="font-weight:400;font-size:11px;color:#94a3b8">PNG or JPG, shown in PDF reports and email headers</span></label>
        <?php if (!empty($current['logo_filename']) && file_exists(__DIR__ . '/../data/' . $current['logo_filename'])): ?>
          <div style="margin-bottom:8px;padding:10px;background:#f8fafc;border-radius:8px;display:flex;align-items:center;gap:12px">
            <img src="../data/<?= htmlspecialchars($current['logo_filename']) ?>" style="max-height:36px;max-width:140px;">
            <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:#dc2626;cursor:pointer;font-weight:500">
              <input type="checkbox" name="remove_logo" value="1"> Remove logo
            </label>
          </div>
        <?php endif; ?>
        <input type="file" id="logo" name="logo" accept="image/png,image/jpeg"
               style="padding:8px;border-radius:8px;border:1.5px solid #d1d5db;width:100%;font-size:13px">
        <div class="hint">Recommended: 300×80px, transparent background. Leave blank to keep current.</div>
      </div>
    </div>

    <!-- ── Admin Password ────────────────────────────────────── -->
    <div class="section">
      <div class="section-title">Admin Password</div>
      <div class="form-row">
        <div class="form-group">
          <label for="pw_new">New password</label>
          <div class="pw-wrap">
            <input type="password" id="pw_new" name="admin_password_new"
                   placeholder="Leave blank to keep current" autocomplete="new-password">
            <button type="button" class="pw-eye" onclick="togglePw('pw_new')">Show</button>
          </div>
        </div>
        <div class="form-group">
          <label for="pw_confirm">Confirm new password</label>
          <input type="password" id="pw_confirm" name="admin_password_confirm"
                 placeholder="Repeat new password" autocomplete="new-password">
        </div>
      </div>
      <div class="hint">Minimum 8 characters recommended. Current password required to access this panel.</div>
    </div>

    <button type="submit" class="btn-save">Save Settings</button>

  </form>
</div>

<script>
function togglePw(id) {
  const el = document.getElementById(id);
  const btn = el.nextElementSibling;
  if (el.type === 'password') { el.type = 'text'; btn.textContent = 'Hide'; }
  else { el.type = 'password'; btn.textContent = 'Show'; }
}

</script>

</body>
</html>
