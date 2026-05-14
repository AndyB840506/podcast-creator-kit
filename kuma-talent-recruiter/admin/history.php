<?php
require_once 'auth.php';

$interviews = file_exists(INTERVIEWS_FILE)
    ? (json_decode(file_get_contents(INTERVIEWS_FILE), true) ?? [])
    : [];

$verdictColor = [
    'Strong Fit'  => ['bg' => '#f0fdf4', 'text' => '#16a34a'],
    'Possible Fit'=> ['bg' => '#fffbeb', 'text' => '#d97706'],
    'Not a Fit'   => ['bg' => '#fef2f2', 'text' => '#dc2626'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Interview History — Kuma Talent Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:Inter,sans-serif;background:#f0f4f8;color:#1e293b;font-size:14px}
    .topbar{background:#1e3a5f;padding:0 32px;height:56px;display:flex;align-items:center;justify-content:space-between}
    .topbar-brand{font-size:17px;font-weight:700;color:#fff}
    .topbar-nav{display:flex;gap:24px;align-items:center}
    .nav-link{color:#94a3b8;font-size:13px;text-decoration:none}
    .nav-link:hover,.nav-link.active{color:#fff}
    .main{max-width:1060px;margin:32px auto;padding:0 24px}
    .page-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px}
    .page-title{font-size:20px;font-weight:700;color:#1e3a5f}
    .table-wrap{background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.07);overflow:hidden}
    table{width:100%;border-collapse:collapse}
    thead{background:#f8fafc}
    th{padding:11px 14px;text-align:left;font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e2e8f0}
    td{padding:12px 14px;border-bottom:1px solid #f1f5f9;vertical-align:middle}
    tr:last-child td{border-bottom:none}
    tr:hover td{background:#fafbfc}
    .code-badge{display:inline-block;padding:2px 7px;border-radius:4px;background:#1e3a5f;color:#fff;font-size:11px;font-weight:700;letter-spacing:.07em}
    .verdict-pill{display:inline-block;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600}
    .score{font-weight:700;font-size:14px}
    .actions{display:flex;gap:6px}
    .btn-dl{padding:4px 10px;border-radius:6px;font-size:11px;font-weight:600;text-decoration:none;transition:.15s}
    .btn-pdf{background:#eff6ff;color:#2563eb}
    .btn-pdf:hover{background:#dbeafe}
    .btn-txt{background:#f0fdf4;color:#16a34a}
    .btn-txt:hover{background:#dcfce7}
    .empty{padding:48px;text-align:center;color:#94a3b8}
    .stat-bar{display:flex;gap:16px;margin-bottom:20px}
    .stat{background:#fff;border-radius:10px;padding:14px 20px;flex:1;box-shadow:0 2px 8px rgba(0,0,0,.06)}
    .stat-num{font-size:26px;font-weight:700;color:#1e3a5f}
    .stat-label{font-size:12px;color:#64748b;margin-top:2px}
  </style>
</head>
<body>

<div class="topbar">
  <div class="topbar-brand">Kuma Talent &nbsp;/&nbsp; <span style="font-weight:400;color:#94a3b8">Admin</span></div>
  <div class="topbar-nav">
    <a href="index.php" class="nav-link">Jobs</a>
    <a href="history.php" class="nav-link active">History</a>
    <a href="settings.php" class="nav-link">Settings</a>
    <a href="logout.php" class="nav-link">Sign out</a>
  </div>
</div>

<div class="main">
  <div class="page-head">
    <div class="page-title">Interview History (<?= count($interviews) ?>)</div>
  </div>

  <?php if (!empty($interviews)):
    $strong   = count(array_filter($interviews, fn($i) => str_contains($i['verdict'] ?? '', 'Strong')));
    $possible = count(array_filter($interviews, fn($i) => str_contains($i['verdict'] ?? '', 'Possible')));
    $notFit   = count(array_filter($interviews, fn($i) => str_contains($i['verdict'] ?? '', 'Not')));
  ?>
  <div class="stat-bar">
    <div class="stat"><div class="stat-num"><?= count($interviews) ?></div><div class="stat-label">Total interviews</div></div>
    <div class="stat"><div class="stat-num" style="color:#16a34a"><?= $strong ?></div><div class="stat-label">Strong Fit</div></div>
    <div class="stat"><div class="stat-num" style="color:#d97706"><?= $possible ?></div><div class="stat-label">Possible Fit</div></div>
    <div class="stat"><div class="stat-num" style="color:#dc2626"><?= $notFit ?></div><div class="stat-label">Not a Fit</div></div>
  </div>
  <?php endif; ?>

  <div class="table-wrap">
    <?php if (empty($interviews)): ?>
      <div class="empty">No interviews completed yet.</div>
    <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Date / Time</th>
          <th>Candidate</th>
          <th>Job</th>
          <th>Verdict</th>
          <th>Score</th>
          <th>Downloads</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($interviews as $iv): ?>
        <?php
          $vc = $verdictColor[$iv['verdict'] ?? ''] ?? ['bg' => '#f1f5f9', 'text' => '#64748b'];
          $scoreColor = ($iv['score'] ?? 0) >= 8 ? '#16a34a' : (($iv['score'] ?? 0) >= 5 ? '#d97706' : '#dc2626');
        ?>
        <tr>
          <td style="white-space:nowrap;color:#64748b;font-size:12px">
            <?= htmlspecialchars($iv['date'] ?? '') ?>
            <?php if (!empty($iv['time'])): ?><br><?= htmlspecialchars($iv['time']) ?><?php endif; ?>
          </td>
          <td>
            <div style="font-weight:500"><?= htmlspecialchars($iv['candidate_name'] ?? '') ?></div>
            <?php if (!empty($iv['candidate_email'])): ?>
              <div style="font-size:11px;color:#94a3b8"><?= htmlspecialchars($iv['candidate_email']) ?></div>
            <?php endif; ?>
          </td>
          <td>
            <?php if (!empty($iv['job_code'])): ?>
              <span class="code-badge"><?= htmlspecialchars($iv['job_code']) ?></span>
            <?php endif; ?>
            <div style="font-size:12px;color:#64748b;margin-top:3px"><?= htmlspecialchars($iv['job_title'] ?? '') ?></div>
          </td>
          <td>
            <span class="verdict-pill" style="background:<?= $vc['bg'] ?>;color:<?= $vc['text'] ?>">
              <?= htmlspecialchars($iv['verdict'] ?? 'N/A') ?>
            </span>
          </td>
          <td><span class="score" style="color:<?= $scoreColor ?>"><?= number_format($iv['score'] ?? 0, 1) ?>/10</span></td>
          <td>
            <div class="actions">
              <?php if (!empty($iv['pdf_file'])): ?>
                <a href="download.php?file=<?= urlencode($iv['pdf_file']) ?>&type=pdf" class="btn-dl btn-pdf">PDF</a>
              <?php endif; ?>
              <?php if (!empty($iv['transcript_file'])): ?>
                <a href="download.php?file=<?= urlencode($iv['transcript_file']) ?>&type=txt" class="btn-dl btn-txt">Transcript</a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
