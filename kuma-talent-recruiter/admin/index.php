<?php
require_once 'auth.php';

$jobs  = loadJobs();
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

// Handle toggle / delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $code   = strtoupper(trim($_POST['code'] ?? ''));

    if ($code && array_key_exists($code, $jobs)) {
        if ($action === 'toggle') {
            $jobs[$code]['active'] = !($jobs[$code]['active'] ?? true);
            saveJobs($jobs);
            $_SESSION['flash'] = 'Job ' . $code . ' ' . ($jobs[$code]['active'] ? 'activated' : 'deactivated') . '.';
        } elseif ($action === 'delete') {
            $title = $jobs[$code]['title'];
            unset($jobs[$code]);
            saveJobs($jobs);
            $_SESSION['flash'] = "Job {$code} ({$title}) deleted.";
        }
    }
    header('Location: index.php'); exit;
}

$levelLabel = ['agent' => 'Agent', 'professional' => 'Professional', 'executive' => 'Executive'];
$langLabel  = ['auto' => 'Auto-detect', 'spanish' => 'Spanish', 'english' => 'English'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Jobs — Kuma Talent Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:Inter,sans-serif;background:#f0f4f8;color:#1e293b;font-size:14px}
    .topbar{background:#1e3a5f;padding:0 32px;height:56px;display:flex;align-items:center;justify-content:space-between}
    .topbar-brand{font-size:17px;font-weight:700;color:#fff}
    .topbar-right{display:flex;gap:16px;align-items:center}
    .topbar-link{color:#94a3b8;font-size:13px;text-decoration:none}
    .topbar-link:hover{color:#fff}
    .main{max-width:960px;margin:32px auto;padding:0 24px}
    .page-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px}
    .page-title{font-size:20px;font-weight:700;color:#1e3a5f}
    .btn-new{background:#1e3a5f;color:#fff;padding:9px 18px;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;transition:.15s}
    .btn-new:hover{background:#16305a}
    .flash{background:#f0fdf4;border-left:3px solid #16a34a;color:#15803d;padding:10px 14px;border-radius:6px;margin-bottom:20px;font-size:13px}
    .table-wrap{background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.07);overflow:hidden}
    table{width:100%;border-collapse:collapse}
    thead{background:#f8fafc}
    th{padding:12px 16px;text-align:left;font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e2e8f0}
    td{padding:13px 16px;border-bottom:1px solid #f1f5f9;vertical-align:middle}
    tr:last-child td{border-bottom:none}
    tr:hover td{background:#fafbfc}
    .code-badge{display:inline-block;padding:2px 8px;border-radius:4px;background:#1e3a5f;color:#fff;font-size:11px;font-weight:700;letter-spacing:.07em}
    .pill{display:inline-block;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600}
    .pill-green{background:#f0fdf4;color:#16a34a}
    .pill-gray{background:#f1f5f9;color:#94a3b8}
    .pill-blue{background:#eff6ff;color:#2563eb}
    .pill-amber{background:#fffbeb;color:#d97706}
    .pill-purple{background:#faf5ff;color:#7c3aed}
    .actions{display:flex;gap:8px;align-items:center}
    .btn-edit{padding:5px 12px;background:#eff6ff;color:#2563eb;border-radius:6px;text-decoration:none;font-size:12px;font-weight:600;transition:.15s}
    .btn-edit:hover{background:#dbeafe}
    .btn-toggle-on{padding:5px 12px;background:#f0fdf4;color:#16a34a;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;transition:.15s}
    .btn-toggle-on:hover{background:#dcfce7}
    .btn-toggle-off{padding:5px 12px;background:#fffbeb;color:#d97706;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;transition:.15s}
    .btn-toggle-off:hover{background:#fef9c3}
    .btn-del{padding:5px 12px;background:#fef2f2;color:#dc2626;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;transition:.15s}
    .btn-del:hover{background:#fee2e2}
    .empty{padding:48px;text-align:center;color:#94a3b8}
  </style>
</head>
<body>

<div class="topbar">
  <div class="topbar-brand">Kuma Talent &nbsp;/&nbsp; <span style="font-weight:400;color:#94a3b8">Admin</span></div>
  <div class="topbar-right" style="display:flex;gap:24px;align-items:center">
    <a href="index.php" class="topbar-link" style="color:#fff">Jobs</a>
    <a href="history.php" class="topbar-link">History</a>
    <a href="settings.php" class="topbar-link">Settings</a>
    <a href="../index.php" class="topbar-link">View site</a>
    <a href="logout.php" class="topbar-link">Sign out</a>
  </div>
</div>

<div class="main">
  <div class="page-head">
    <div class="page-title">Jobs (<?= count($jobs) ?>)</div>
    <a href="edit.php" class="btn-new">+ New Job</a>
  </div>

  <?php if (!ANTHROPIC_API_KEY || !RECRUITER_EMAIL): ?>
    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:#92400e;">
      Setup incomplete &mdash;
      <?php if (!ANTHROPIC_API_KEY): ?><strong>API Key not configured.</strong><?php endif; ?>
      <?php if (!RECRUITER_EMAIL): ?><strong>Recruiter email not set.</strong><?php endif; ?>
      <a href="settings.php" style="color:#1e3a5f;font-weight:600;margin-left:6px;">Go to Settings &rarr;</a>
    </div>
  <?php endif; ?>

  <?php if ($flash): ?>
    <div class="flash"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <div class="table-wrap">
    <?php if (empty($jobs)): ?>
      <div class="empty">No jobs yet. <a href="edit.php">Create the first one.</a></div>
    <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Code</th>
          <th>Title</th>
          <th>Level</th>
          <th>Language</th>
          <th>English test</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($jobs as $code => $job): ?>
        <?php $active = $job['active'] ?? true; ?>
        <tr>
          <td><span class="code-badge"><?= htmlspecialchars($code) ?></span></td>
          <td style="font-weight:500;max-width:220px"><?= htmlspecialchars($job['title']) ?></td>
          <td>
            <?php
              $lv = $job['level'] ?? 'professional';
              $lvClass = $lv === 'agent' ? 'pill-blue' : ($lv === 'executive' ? 'pill-purple' : 'pill-amber');
            ?>
            <span class="pill <?= $lvClass ?>"><?= htmlspecialchars($levelLabel[$lv] ?? $lv) ?></span>
          </td>
          <td><span class="pill pill-gray"><?= htmlspecialchars($langLabel[$job['language'] ?? 'auto'] ?? 'Auto') ?></span></td>
          <td>
            <?php if ($job['requires_english'] ?? false): ?>
              <span class="pill pill-green">Yes</span>
            <?php else: ?>
              <span class="pill pill-gray">No</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($active): ?>
              <span class="pill pill-green">Active</span>
            <?php else: ?>
              <span class="pill pill-gray">Inactive</span>
            <?php endif; ?>
          </td>
          <td>
            <div class="actions">
              <a href="edit.php?code=<?= urlencode($code) ?>" class="btn-edit">Edit</a>
              <button type="button" class="btn-edit" style="background:#f0fdf4;color:#16a34a;border:none;cursor:pointer;font-family:inherit"
                onclick="copyLink('<?= htmlspecialchars(addslashes($code)) ?>', this)">Copy link</button>

              <form method="POST" style="margin:0">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="code" value="<?= htmlspecialchars($code) ?>">
                <button type="submit" class="<?= $active ? 'btn-toggle-on' : 'btn-toggle-off' ?>">
                  <?= $active ? 'Deactivate' : 'Activate' ?>
                </button>
              </form>

              <form method="POST" style="margin:0" onsubmit="return confirm('Delete <?= htmlspecialchars(addslashes($code)) ?>? This cannot be undone.')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="code" value="<?= htmlspecialchars($code) ?>">
                <button type="submit" class="btn-del">Delete</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>

<script>
function copyLink(code, btn) {
  const url = window.location.origin + window.location.pathname.replace('/admin/index.php', '') + '/?code=' + code;
  navigator.clipboard.writeText(url).then(() => {
    const orig = btn.textContent;
    btn.textContent = 'Copied!';
    setTimeout(() => btn.textContent = orig, 1800);
  });
}
</script>
</body>
</html>
