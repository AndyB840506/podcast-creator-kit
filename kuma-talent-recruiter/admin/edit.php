<?php
require_once 'auth.php';

$jobs   = loadJobs();
$isNew  = true;
$code   = '';
$error  = '';
$job    = [
    'title'            => '',
    'description'      => '',
    'language'         => 'auto',
    'level'            => 'professional',
    'requires_english' => false,
    'active'           => true,
];

// Load existing job for edit
$editCode = strtoupper(trim($_GET['code'] ?? ''));
if ($editCode && array_key_exists($editCode, $jobs)) {
    $isNew = false;
    $code  = $editCode;
    $job   = $jobs[$editCode];
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newCode     = strtoupper(preg_replace('/[^A-Z0-9\-]/', '', strtoupper(trim($_POST['code'] ?? ''))));
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $language    = in_array($_POST['language'] ?? '', ['auto','spanish','english']) ? $_POST['language'] : 'auto';
    $level       = in_array($_POST['level'] ?? '', ['agent','professional','executive']) ? $_POST['level'] : 'professional';
    $reqEn       = !empty($_POST['requires_english']);
    $active      = !empty($_POST['active']);
    $customQs    = array_values(array_filter(array_map('trim', explode("\n", str_replace("\r", "\n", str_replace("\r\n", "\n", $_POST['custom_questions'] ?? ''))))));

    if (!$newCode) {
        $error = 'Job code is required (letters, numbers, hyphens only).';
    } elseif (!$title) {
        $error = 'Title is required.';
    } elseif (!$description) {
        $error = 'Description is required.';
    } elseif ($isNew && array_key_exists($newCode, $jobs)) {
        $error = "Code {$newCode} already exists. Use a different code.";
    } else {
        // Remove old key if code changed on edit
        if (!$isNew && $code !== $newCode) {
            unset($jobs[$code]);
        }
        $jobs[$newCode] = [
            'title'            => $title,
            'description'      => $description,
            'language'         => $language,
            'level'            => $level,
            'requires_english' => $reqEn,
            'active'           => $active,
            'custom_questions' => $customQs,
        ];
        saveJobs($jobs);
        $_SESSION['flash'] = $isNew ? "Job {$newCode} created." : "Job {$newCode} updated.";
        header('Location: index.php'); exit;
    }

    // Re-populate form values on error
    $code = $_POST['code'];
    $job  = compact('title', 'description', 'language', 'level') + ['requires_english' => $reqEn, 'active' => $active];
}

function sel(string $current, string $value): string {
    return $current === $value ? ' selected' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $isNew ? 'New Job' : 'Edit ' . htmlspecialchars($code) ?> — Kuma Talent Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:Inter,sans-serif;background:#f0f4f8;color:#1e293b;font-size:14px}
    .topbar{background:#1e3a5f;padding:0 32px;height:56px;display:flex;align-items:center;justify-content:space-between}
    .topbar-brand{font-size:17px;font-weight:700;color:#fff}
    .topbar-link{color:#94a3b8;font-size:13px;text-decoration:none}
    .topbar-link:hover{color:#fff}
    .main{max-width:680px;margin:32px auto;padding:0 24px}
    .page-head{display:flex;align-items:center;gap:12px;margin-bottom:24px}
    .back{color:#64748b;text-decoration:none;font-size:13px}
    .back:hover{color:#1e3a5f}
    .page-title{font-size:20px;font-weight:700;color:#1e3a5f}
    .card{background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.07);padding:32px}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px}
    .form-group{margin-bottom:20px}
    label{display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px}
    .hint{font-size:11px;color:#94a3b8;margin-top:4px}
    input[type=text], select, textarea{width:100%;padding:10px 12px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;font-family:inherit;outline:none;transition:.15s;background:#fff;color:#1e293b}
    input[type=text]:focus, select:focus, textarea:focus{border-color:#1e3a5f;box-shadow:0 0 0 3px rgba(30,58,95,.1)}
    textarea{resize:vertical;min-height:130px;line-height:1.55}
    .checkbox-row{display:flex;align-items:center;gap:10px;padding:10px 0}
    input[type=checkbox]{width:16px;height:16px;cursor:pointer;accent-color:#1e3a5f}
    .checkbox-label{font-size:13px;color:#374151;cursor:pointer}
    .error{background:#fef2f2;border-left:3px solid #dc2626;color:#dc2626;padding:10px 14px;border-radius:6px;margin-bottom:20px;font-size:13px}
    .actions{display:flex;gap:12px;margin-top:8px}
    .btn-save{padding:10px 24px;background:#1e3a5f;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;transition:.15s}
    .btn-save:hover{background:#16305a}
    .btn-cancel{padding:10px 20px;background:#f1f5f9;color:#64748b;border:none;border-radius:8px;font-size:14px;font-weight:500;cursor:pointer;font-family:inherit;text-decoration:none;display:inline-flex;align-items:center;transition:.15s}
    .btn-cancel:hover{background:#e2e8f0}
    .code-note{font-size:12px;color:#64748b;margin-top:5px}
  </style>
</head>
<body>

<div class="topbar">
  <div class="topbar-brand">Kuma Talent &nbsp;/&nbsp; <span style="font-weight:400;color:#94a3b8">Job Manager</span></div>
  <a href="logout.php" class="topbar-link">Sign out</a>
</div>

<div class="main">
  <div class="page-head">
    <a href="index.php" class="back">&larr; Back to jobs</a>
    <div class="page-title"><?= $isNew ? 'New Job' : 'Edit ' . htmlspecialchars($code) ?></div>
  </div>

  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <div class="card">
    <form method="POST">

      <div class="form-row">
        <div class="form-group">
          <label for="code">Job Code *</label>
          <input type="text" id="code" name="code"
                 value="<?= htmlspecialchars($code) ?>"
                 placeholder="KT-001"
                 style="text-transform:uppercase;letter-spacing:.05em"
                 <?= !$isNew ? 'readonly style="background:#f8fafc;text-transform:uppercase;letter-spacing:.05em"' : '' ?>
                 maxlength="20" required>
          <div class="hint">Letters, numbers, hyphens. Uppercase. Unique.</div>
        </div>

        <div class="form-group">
          <label for="level">Level *</label>
          <select id="level" name="level">
            <option value="agent"<?= sel($job['level'] ?? 'professional', 'agent') ?>>Agent — casual tone</option>
            <option value="professional"<?= sel($job['level'] ?? 'professional', 'professional') ?>>Professional — standard tone</option>
            <option value="executive"<?= sel($job['level'] ?? 'professional', 'executive') ?>>Executive — formal/peer tone</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="title">Job Title *</label>
        <input type="text" id="title" name="title"
               value="<?= htmlspecialchars($job['title']) ?>"
               placeholder="e.g. Agente de Servicio Bilingüe"
               maxlength="120" required>
      </div>

      <div class="form-group">
        <label for="description">Job Description *</label>
        <textarea id="description" name="description" placeholder="Paste the full job description here — the AI uses this to ask role-specific questions and evaluate the candidate." required><?= htmlspecialchars($job['description']) ?></textarea>
        <div class="hint">The more detail, the better the interview quality. Paste the full JD.</div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="language">Interview Language</label>
          <select id="language" name="language">
            <option value="auto"<?= sel($job['language'] ?? 'auto', 'auto') ?>>Auto-detect (candidate's language)</option>
            <option value="spanish"<?= sel($job['language'] ?? 'auto', 'spanish') ?>>Spanish only</option>
            <option value="english"<?= sel($job['language'] ?? 'auto', 'english') ?>>English only</option>
          </select>
        </div>

        <div class="form-group">
          <label>Options</label>
          <div class="checkbox-row">
            <input type="checkbox" id="req_en" name="requires_english" value="1"
                   <?= !empty($job['requires_english']) ? 'checked' : '' ?>>
            <label class="checkbox-label" for="req_en">Require English assessment</label>
          </div>
          <div class="checkbox-row">
            <input type="checkbox" id="active" name="active" value="1"
                   <?= ($job['active'] ?? true) ? 'checked' : '' ?>>
            <label class="checkbox-label" for="active">Active (accept candidates)</label>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="custom_questions">Custom questions <span style="font-size:11px;font-weight:400;color:#94a3b8">— one per line, optional</span></label>
        <textarea id="custom_questions" name="custom_questions" style="min-height:90px"
                  placeholder="Do you have experience with Salesforce?&#10;How do you handle an escalated complaint?"><?= htmlspecialchars(implode("\n", $job['custom_questions'] ?? [])) ?></textarea>
        <div class="hint">These are asked in addition to the standard interview flow, during the Role Fit section.</div>
      </div>

      <div class="actions">
        <button type="submit" class="btn-save"><?= $isNew ? 'Create Job' : 'Save Changes' ?></button>
        <a href="index.php" class="btn-cancel">Cancel</a>
      </div>

    </form>
  </div>
</div>

</body>
</html>
