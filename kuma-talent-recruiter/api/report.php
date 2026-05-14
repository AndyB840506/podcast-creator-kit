<?php
file_put_contents(__DIR__ . '/../debug.log', date('H:i:s') . " [report] ENTRY\n", FILE_APPEND);
ignore_user_abort(true);
set_time_limit(0);

// ── use statements (compile-time — must be before any class usage) ────────────
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;
use Dompdf\Options;

// ── Config ────────────────────────────────────────────────────────────────────
require_once __DIR__ . '/../config.php';

// ── Debug log (set up FIRST, before anything that can fail) ──────────────────
$logFile = __DIR__ . '/../debug.log';
$log = fn(string $m) => file_put_contents($logFile, date('H:i:s') . " [report] $m\n", FILE_APPEND);

// ── Capture fatal errors ──────────────────────────────────────────────────────
register_shutdown_function(function () use ($logFile) {
    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        file_put_contents($logFile, date('H:i:s') . " [report] FATAL: {$e['message']} line {$e['line']}\n", FILE_APPEND);
        if (!headers_sent()) header('Content-Type: application/json');
        echo json_encode(['error' => 'PHP Fatal: ' . $e['message']]);
    }
});

header('Content-Type: application/json');
$log("=== request start ===");

// ── Diagnostic GET (visit in browser to test) ─────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $autoloadExists = file_exists(__DIR__ . '/../vendor/autoload.php');
    $reportsWritable = is_writable(dirname(REPORTS_DIR)) || (is_dir(REPORTS_DIR) && is_writable(REPORTS_DIR));
    echo json_encode([
        'status'          => 'report.php OK',
        'autoload_exists' => $autoloadExists,
        'reports_dir'     => REPORTS_DIR,
        'reports_writable'=> $reportsWritable,
        'php_version'     => PHP_VERSION,
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']); exit;
}
$log("A: method=POST");

// ── Load autoloader safely ────────────────────────────────────────────────────
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
$log("B: checking autoload at $autoloadPath");
if (!file_exists($autoloadPath)) {
    $log("ERROR: vendor/autoload.php not found");
    echo json_encode(['error' => 'Run composer update first']);
    exit;
}
$log("C: autoload exists, requiring...");
require_once $autoloadPath;
$log("D: autoload required OK");

if (!class_exists('Dompdf\Dompdf')) {
    $log("ERROR: Dompdf class not found after autoload");
    echo json_encode(['error' => 'Dompdf not loaded. Run composer update.']);
    exit;
}
$log("E: Dompdf class exists");

// ── Session ───────────────────────────────────────────────────────────────────
$log("F: calling session_start...");
session_start();
$log("G: session started, job_title=" . ($_SESSION['job_title'] ?? 'MISSING'));

if (empty($_SESSION['job_title']) || empty($_SESSION['messages'])) {
    echo json_encode(['error' => 'Session expired.']);
    exit;
}

$candidateName   = $_SESSION['candidate_name'];
$candidateEmail  = $_SESSION['candidate_email'] ?? '';
$jobCode         = $_SESSION['job_code'] ?? '';
$jobTitle        = $_SESSION['job_title'];
$jobDescription  = $_SESSION['job_description'];
$requiresEnglish = (bool) ($_SESSION['requires_english'] ?? false);
$messages        = $_SESSION['messages'];
session_write_close();
$log("session closed. messages=" . count($messages) . " candidate=$candidateName");

// ── Build transcript ──────────────────────────────────────────────────────────
$transcript = '';
foreach ($messages as $msg) {
    $role    = $msg['role'] === 'user' ? $candidateName : 'Interviewer';
    $content = str_replace('[INTERVIEW_COMPLETE]', '', $msg['content']);
    $transcript .= "**{$role}:** " . trim($content) . "\n\n";
}

$date = date('Y-m-d');
$log("transcript built, length=" . strlen($transcript));

// ── Generate markdown report via Claude ───────────────────────────────────────
$log("calling Claude for report...");
$reportMarkdown = generateMarkdownReport($candidateName, $jobTitle, $jobDescription, $transcript, $date, $requiresEnglish);

if (!$reportMarkdown) {
    $log("ERROR: generateMarkdownReport returned false");
    echo json_encode(['error' => 'Failed to generate report.']);
    exit;
}
$log("report markdown generated, length=" . strlen($reportMarkdown));

// ── Parse report data ─────────────────────────────────────────────────────────
$verdict  = parseVerdict($reportMarkdown);
$scores   = parseScores($reportMarkdown);
$sections = parseSections($reportMarkdown);
$log("parsed: verdict={$verdict['text']} score={$verdict['score']} dimensions=" . count($scores));

// ── Generate PDF ──────────────────────────────────────────────────────────────
$log("generating PDF...");
$pdfContent = generatePdf($candidateName, $jobTitle, $date, $verdict, $scores, $sections, $messages);
$log("PDF generated, bytes=" . strlen($pdfContent));

$safeCandidate    = preg_replace('/[^a-zA-Z0-9_-]/', '-', strtolower($candidateName));
$uid              = substr(uniqid(), -4);
$pdfFilename      = "report-{$safeCandidate}-{$date}-{$uid}.pdf";
$transcriptFilename = "transcript-{$safeCandidate}-{$date}-{$uid}.txt";
$pdfPath          = REPORTS_DIR . $pdfFilename;
$transcriptPath   = REPORTS_DIR . $transcriptFilename;

if (!is_dir(REPORTS_DIR)) mkdir(REPORTS_DIR, 0775, true);
file_put_contents($pdfPath, $pdfContent);
$log("PDF saved to $pdfPath");

// Save transcript text file
$transcriptText = buildTranscriptText($candidateName, $jobTitle, $jobCode, $date, $messages);
file_put_contents($transcriptPath, $transcriptText);
$log("Transcript saved to $transcriptPath");

// Log interview to history
logInterview([
    'id'               => $uid,
    'candidate_name'   => $candidateName,
    'candidate_email'  => $candidateEmail ?: null,
    'job_code'         => $jobCode,
    'job_title'        => $jobTitle,
    'verdict'          => $verdict['text'],
    'score'            => $verdict['score'],
    'date'             => $date,
    'time'             => date('H:i'),
    'pdf_file'         => $pdfFilename,
    'transcript_file'  => $transcriptFilename,
]);
$log("Interview logged to history");

// ── Send email ────────────────────────────────────────────────────────────────
$emailSent = false;
if (!DEV_MODE) {
    $emailSent = sendReportEmail($candidateName, $jobTitle, $verdict, $pdfPath, $pdfFilename, $date);
    if ($candidateEmail) sendCandidateCopy($candidateName, $candidateEmail, $jobTitle, $date);
}
$log("emailSent=$emailSent devMode=" . (DEV_MODE ? 'true' : 'false'));

echo json_encode([
    'success'    => true,
    'email_sent' => $emailSent,
    'report_id'  => $pdfFilename,
    'dev_mode'   => DEV_MODE,
]);
$log("=== request end ===");

// ════════════════════════════════════════════════════════════════════
// REPORT GENERATION
// ════════════════════════════════════════════════════════════════════

function generateMarkdownReport(string $name, string $title, string $desc, string $transcript, string $date, bool $requiresEnglish = false): string|false
{
    $prompt = buildReportPrompt($name, $title, $desc, $transcript, $date, $requiresEnglish);

    $result = callLLMReport($prompt, 2048);
    return $result ?? false;
}

// ── Parse verdict from markdown ───────────────────────────────────────────────
function parseVerdict(string $md): array
{
    $verdict = ['text' => 'Evaluation Complete', 'score' => 0.0, 'color' => '#64748b', 'seniority' => '', 'script_suspicion' => '', 'hire_recommendation' => ''];

    if (preg_match('/##\s*Verdict:\s*(.+)/i', $md, $m)) {
        $verdict['text'] = trim(strip_tags($m[1]));
    }
    if (preg_match('/Overall Score:\s*([\d.]+)\s*\/\s*10/i', $md, $m)) {
        $verdict['score'] = (float) $m[1];
    }
    if (preg_match('/Seniority Estimation:\s*\*?\*?(.+?)\*?\*?\s*$/im', $md, $m)) {
        $verdict['seniority'] = trim($m[1]);
    }
    if (preg_match('/Script Suspicion Level:\s*\*?\*?(.+?)\*?\*?\s*$/im', $md, $m)) {
        $verdict['script_suspicion'] = trim($m[1]);
    }
    if (preg_match('/Hire Recommendation:\s*\*?\*?(.+?)\*?\*?\s*$/im', $md, $m)) {
        $verdict['hire_recommendation'] = trim($m[1]);
    }

    $text = $verdict['text'];
    if (str_contains($text, 'Strong Fit'))    $verdict['color'] = '#16a34a';
    elseif (str_contains($text, 'Strong Maybe')) $verdict['color'] = '#0ea5e9';
    elseif (str_contains($text, 'Possible'))  $verdict['color'] = '#d97706';
    elseif (str_contains($text, 'Not a Fit')) $verdict['color'] = '#dc2626';

    return $verdict;
}

// ── Parse dimension scores ────────────────────────────────────────────────────
function parseScores(string $md): array
{
    $scores = [];
    $skip   = ['dimension', 'score', 'key evidence', '---', 'strong fit', 'possible fit'];

    if (preg_match_all('/\|\s*([^|]+?)\s*\|\s*([\d.]+)\/10\s*\|/i', $md, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $m) {
            $label = trim($m[1]);
            $lower = strtolower($label);
            $skip_ = false;
            foreach ($skip as $s) { if (str_contains($lower, $s)) { $skip_ = true; break; } }
            if (!$skip_ && strlen($label) > 4) {
                $scores[] = ['label' => $label, 'score' => (float) $m[2]];
            }
        }
    }
    return $scores;
}

// ── Parse text sections ───────────────────────────────────────────────────────
function parseSections(string $md): array
{
    $s = [
        'strengths'          => [],
        'concerns'           => [],
        'quotes'             => [],
        'recommendation'     => '',
        'authenticity'       => [],
        'recruiter_insights' => [],
    ];

    // Strengths
    if (preg_match('/##\s*Key Strengths\s*\n(.*?)(?=\n##|\z)/si', $md, $m)) {
        preg_match_all('/^-\s+\*\*(.+?)\*\*[:\s]+(.+)$/m', $m[1], $items);
        for ($i = 0; $i < count($items[1]); $i++) {
            $s['strengths'][] = ['title' => rtrim(trim($items[1][$i]), ':'), 'text' => trim($items[2][$i])];
        }
    }

    // Concerns
    if (preg_match('/##\s*Concerns[^\n]*\n(.*?)(?=\n##|\z)/si', $md, $m)) {
        preg_match_all('/^-\s+\*\*(.+?)\*\*[:\s]+(.+)$/m', $m[1], $items);
        for ($i = 0; $i < count($items[1]); $i++) {
            $s['concerns'][] = ['title' => rtrim(trim($items[1][$i]), ':'), 'text' => trim($items[2][$i])];
        }
    }

    // Authenticity signals
    if (preg_match('/##\s*Authenticity Signals\s*\n(.*?)(?=\n##|\z)/si', $md, $m)) {
        preg_match_all('/^-\s+\*\*(.+?)\*\*[:\s]+(.+)$/m', $m[1], $items);
        for ($i = 0; $i < count($items[1]); $i++) {
            $s['authenticity'][] = ['title' => rtrim(trim($items[1][$i]), ':'), 'text' => trim($items[2][$i])];
        }
    }

    // Recruiter insights
    if (preg_match('/##\s*Recruiter Insights\s*\n(.*?)(?=\n##|\z)/si', $md, $m)) {
        preg_match_all('/^-\s+(.+)$/m', $m[1], $items);
        $s['recruiter_insights'] = array_map('trim', $items[1] ?? []);
    }

    // Quotes
    if (preg_match_all('/^>\s*"?(.+?)"?\s*$/m', $md, $m)) {
        $s['quotes'] = array_map('trim', $m[1]);
    }

    // Recommendation
    if (preg_match('/##\s*Recommendation[^\n]*\n(.*?)(?=\n---|\z)/si', $md, $m)) {
        $s['recommendation'] = trim(preg_replace('/\*\*(.+?)\*\*/', '$1', $m[1]));
    }

    return $s;
}

// ════════════════════════════════════════════════════════════════════
// PDF GENERATION
// ════════════════════════════════════════════════════════════════════

function generatePdf(string $name, string $title, string $date, array $verdict, array $scores, array $sections, array $messages = []): string
{
    $logoBase64 = '';
    $logoMime   = 'image/png';
    if (LOGO_PATH) {
        $logoBase64 = base64_encode(file_get_contents(LOGO_PATH));
        $ext = strtolower(pathinfo(LOGO_PATH, PATHINFO_EXTENSION));
        $logoMime = in_array($ext, ['jpg','jpeg']) ? 'image/jpeg' : ($ext === 'gif' ? 'image/gif' : 'image/png');
    }

    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', false);
    $options->set('defaultFont', 'DejaVu Sans');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml(buildPdfHtml($name, $title, $date, $verdict, $scores, $sections, $messages, $logoBase64, $logoMime), 'UTF-8');
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    return $dompdf->output();
}

function buildPdfHtml(string $name, string $title, string $date, array $verdict, array $scores, array $sections, array $messages = [], string $logoBase64 = '', string $logoMime = 'image/png'): string
{
    $bars   = buildBarChart($scores);
    $radar  = buildScoreCards($scores);

    $vColor  = htmlspecialchars($verdict['color']);
    $vText   = htmlspecialchars($verdict['text']);
    $vScore  = number_format($verdict['score'], 1);
    $vSeniority = htmlspecialchars($verdict['seniority'] ?? '');
    $vScript    = htmlspecialchars($verdict['script_suspicion'] ?? '');
    $vHire      = htmlspecialchars($verdict['hire_recommendation'] ?? '');

    $logoHtml = $logoBase64
        ? '<img src="data:' . $logoMime . ';base64,' . $logoBase64 . '" style="max-height:34px;max-width:160px;">'
        : '<div class="header-brand">' . e(APP_NAME) . '</div>';

    $strengthsHtml = '';
    foreach ($sections['strengths'] as $item) {
        $strengthsHtml .= '<p style="margin:0 0 7px 0;"><span style="color:#16a34a;font-weight:bold;">&#10003;</span> <strong>' . e($item['title']) . ':</strong> ' . e($item['text']) . '</p>';
    }
    if (!$strengthsHtml) $strengthsHtml = '<p style="color:#94a3b8;">No specific strengths identified.</p>';

    $concernsHtml = '';
    foreach ($sections['concerns'] as $item) {
        $concernsHtml .= '<p style="margin:0 0 7px 0;"><span style="color:#dc2626;">!</span> <strong>' . e($item['title']) . ':</strong> ' . e($item['text']) . '</p>';
    }
    if (!$concernsHtml) $concernsHtml = '<p style="color:#94a3b8;">No significant concerns identified.</p>';

    $authenticityHtml = '';
    foreach ($sections['authenticity'] ?? [] as $item) {
        $authenticityHtml .= '<p style="margin:0 0 7px 0;"><span style="color:#7c3aed;">&#9670;</span> <strong>' . e($item['title']) . ':</strong> ' . e($item['text']) . '</p>';
    }
    if (!$authenticityHtml) $authenticityHtml = '<p style="color:#94a3b8;">No authenticity signals detected.</p>';

    $insightsHtml = '';
    foreach ($sections['recruiter_insights'] ?? [] as $insight) {
        $insightsHtml .= '<p style="margin:0 0 6px 0;color:#1e3a5f;">&#8594; ' . e($insight) . '</p>';
    }
    if (!$insightsHtml) $insightsHtml = '<p style="color:#94a3b8;">No additional recruiter insights.</p>';

    $quotesHtml = '';
    foreach ($sections['quotes'] as $q) {
        $quotesHtml .= '<div style="border-left:3px solid #f59e0b;padding:6px 10px;margin:0 0 8px 0;font-style:italic;color:#475569;">"' . e($q) . '"</div>';
    }
    if (!$quotesHtml) $quotesHtml = '<p style="color:#94a3b8;">No quotes recorded.</p>';

    $metaBadges = '';
    if ($vSeniority) $metaBadges .= '<span style="background:#e0f2fe;color:#0369a1;padding:3px 9px;border-radius:10px;font-size:9px;font-weight:bold;margin-right:6px;">' . $vSeniority . '</span>';
    if ($vScript)    $metaBadges .= '<span style="background:#f3e8ff;color:#7c3aed;padding:3px 9px;border-radius:10px;font-size:9px;font-weight:bold;margin-right:6px;">Script: ' . $vScript . '</span>';
    if ($vHire)      $metaBadges .= '<span style="background:#f0fdf4;color:#16a34a;padding:3px 9px;border-radius:10px;font-size:9px;font-weight:bold;">' . $vHire . '</span>';

    $rec     = nl2br(e($sections['recommendation']));
    $appName = e(APP_NAME);

    // Build transcript page
    $transcriptPage = '';
    if (!empty($messages)) {
        $tRows = '';
        foreach ($messages as $msg) {
            $content = trim(str_replace('[INTERVIEW_COMPLETE]', '', $msg['content']));
            if (!$content || $content === 'begin') continue;
            if ($msg['role'] === 'assistant') {
                $tRows .= '<div style="margin-bottom:10px;">'
                       .  '<div style="font-size:9px;font-weight:bold;color:#1e3a5f;margin-bottom:2px;text-transform:uppercase;">Interviewer</div>'
                       .  '<div style="font-size:9px;color:#475569;line-height:1.55;background:#f8fafc;padding:7px 9px;border-radius:4px;">' . nl2br(e($content)) . '</div>'
                       .  '</div>';
            } else {
                $tRows .= '<div style="margin-bottom:10px;">'
                       .  '<div style="font-size:9px;font-weight:bold;color:#dc2626;margin-bottom:2px;text-transform:uppercase;">' . e($name) . '</div>'
                       .  '<div style="font-size:9px;color:#1e293b;line-height:1.55;background:#fffbeb;padding:7px 9px;border-radius:4px;">' . nl2br(e($content)) . '</div>'
                       .  '</div>';
            }
        }
        $transcriptPage = '<div style="page-break-before:always;">'
            . '<div class="header"><' . ($logoBase64 ? 'div>' . $logoHtml . '</div' : 'div class="header-brand">' . e(APP_NAME) . '</div') . '>'
            . '<div class="header-sub">Interview Transcript</div></div>'
            . '<div style="padding:16px 32px 10px;">'
            . '<div style="font-size:10px;font-weight:bold;color:#1e3a5f;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e2e8f0;padding-bottom:5px;margin-bottom:4px;">Full Conversation — ' . e($name) . ' / ' . e($title) . ' / ' . e($date) . '</div>'
            . '<div style="font-size:9px;color:#94a3b8;margin-bottom:12px;">To check for AI-generated responses, copy the candidate\'s answers (red labels) and paste them into Originality.ai, GPTZero, or Copyleaks.</div>'
            . $tRows
            . '</div></div>';
    }

    return <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size:11px; color:#1e293b; background:#fff; }
  .page { width:100%; }

  /* Header */
  .header { background-color:#1e3a5f; padding:22px 32px; }
  .header-brand { font-size:20px; font-weight:bold; color:#fff; }
  .header-sub   { font-size:10px; color:#94a3b8; margin-top:3px; }

  /* Candidate bar */
  .cbar { background-color:#f0f4f8; padding:14px 32px; border-bottom:3px solid #1e3a5f; }

  /* Section */
  .section { padding:14px 32px; }
  .stitle { font-size:10px; font-weight:bold; color:#1e3a5f; text-transform:uppercase;
            letter-spacing:0.5px; border-bottom:1px solid #e2e8f0; padding-bottom:5px; margin-bottom:10px; }

  /* Score badge */
  .badge { display:inline-block; padding:5px 14px; border-radius:14px; color:#fff; font-weight:bold; font-size:11px; }

  .footer { background-color:#f8fafc; border-top:1px solid #e2e8f0; padding:8px 32px; font-size:9px; color:#94a3b8; }
</style>
</head>
<body>
<div class="page">

  <!-- Header -->
  <div class="header">
    {$logoHtml}
    <div class="header-sub">AI Candidate Screening Report</div>
  </div>

  <!-- Candidate bar -->
  <div class="cbar">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td valign="middle">
          <div style="font-size:17px;font-weight:bold;color:#1e3a5f;">{$name}</div>
          <div style="font-size:10px;color:#64748b;margin-top:3px;">{$title} &nbsp;·&nbsp; {$date}</div>
          <div style="margin-top:7px;">{$metaBadges}</div>
        </td>
        <td valign="middle" align="right">
          <span class="badge" style="background-color:{$vColor};">{$vText}</span>
          <div style="font-size:19px;font-weight:bold;color:{$vColor};text-align:right;margin-top:4px;">{$vScore} / 10</div>
        </td>
      </tr>
    </table>
  </div>

  <!-- Charts -->
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td width="56%" valign="top" style="padding:14px 12px 4px 32px;">
        <div class="stitle">Score Breakdown</div>
        {$bars}
      </td>
      <td width="44%" valign="top" style="padding:14px 32px 4px 12px;">
        <div class="stitle">Dimension Scores</div>
        {$radar}
      </td>
    </tr>
  </table>

  <!-- Strengths -->
  <div class="section">
    <div class="stitle">Key Strengths</div>
    {$strengthsHtml}
  </div>

  <!-- Concerns -->
  <div class="section" style="padding-top:4px;">
    <div class="stitle">Concerns &amp; Gaps</div>
    {$concernsHtml}
  </div>

  <!-- Authenticity Signals -->
  <div class="section" style="padding-top:4px;">
    <div class="stitle">Authenticity Signals</div>
    {$authenticityHtml}
  </div>

  <!-- Quotes -->
  <div class="section" style="padding-top:4px;">
    <div class="stitle">Notable Quotes</div>
    {$quotesHtml}
  </div>

  <!-- Recruiter Insights -->
  <div class="section" style="padding-top:4px;">
    <div class="stitle">Recruiter Insights</div>
    {$insightsHtml}
  </div>

  <!-- Recommendation -->
  <div class="section" style="padding-top:4px;">
    <div class="stitle">Recommendation for the Hiring Team</div>
    <div style="background:#f8fafc;padding:12px;border-radius:5px;line-height:1.65;">{$rec}</div>
  </div>

  <!-- Footer -->
  <div class="footer">
    Generated by {$appName} AI Screener &nbsp;·&nbsp; Assessments based exclusively on the candidate's interview responses.
  </div>

  <!-- Transcript page -->
  {$transcriptPage}

</div>
</body>
</html>
HTML;
}

// ── SVG: Horizontal bar chart ─────────────────────────────────────────────────
function buildBarChart(array $scores): string
{
    if (empty($scores)) return '<p style="color:#94a3b8;font-size:10px;">Scores unavailable.</p>';

    $html = '';
    foreach ($scores as $dim) {
        $pct   = min(100, round(($dim['score'] / 10) * 100));
        $color = $dim['score'] >= 8 ? '#16a34a' : ($dim['score'] >= 5 ? '#d97706' : '#dc2626');
        $label = e(shorten($dim['label'], 40));
        $score = number_format($dim['score'], 1);

        $html .= <<<ROW
<div style="margin-bottom:10px;">
  <div style="font-size:10px;color:#64748b;margin-bottom:3px;">{$label}</div>
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td width="82%" valign="middle">
        <div style="background:#f1f5f9;border-radius:4px;height:13px;width:100%;">
          <div style="background:{$color};border-radius:4px;height:13px;width:{$pct}%;"></div>
        </div>
      </td>
      <td width="18%" align="right" valign="middle" style="font-weight:bold;font-size:11px;padding-left:6px;color:{$color};">{$score}/10</td>
    </tr>
  </table>
</div>
ROW;
    }
    return $html;
}

// ── Score cards grid (replaces radar chart — Dompdf-safe) ────────────────────
function buildScoreCards(array $scores): string
{
    if (empty($scores)) return '<p style="color:#94a3b8;font-size:10px;">Scores unavailable.</p>';

    $html = '<table width="100%" cellpadding="0" cellspacing="0">';
    $chunks = array_chunk($scores, 2);

    foreach ($chunks as $row) {
        $html .= '<tr>';
        foreach ($row as $i => $dim) {
            $color = $dim['score'] >= 8 ? '#16a34a' : ($dim['score'] >= 5 ? '#d97706' : '#dc2626');
            $score = number_format($dim['score'], 1);
            $label = e(shorten($dim['label'], 22));
            if ($i > 0) $html .= '<td width="8"></td>';
            $html .= '<td style="background:#f0f4f8;border-radius:6px;text-align:center;padding:11px 6px;">'
                  .  '<div style="font-size:24px;font-weight:bold;color:' . $color . ';line-height:1;">' . $score . '</div>'
                  .  '<div style="font-size:7px;color:#64748b;margin-top:4px;line-height:1.3;">' . $label . '</div>'
                  .  '</td>';
        }
        if (count($row) === 1) $html .= '<td width="8"></td><td style="width:45%;"></td>';
        $html .= '</tr><tr><td colspan="3" style="height:7px;"></td></tr>';
    }

    $html .= '</table>';
    return $html;
}

// ════════════════════════════════════════════════════════════════════
// EMAIL
// ════════════════════════════════════════════════════════════════════

function sendReportEmail(string $name, string $title, array $verdict, string $pdfPath, string $pdfFilename, string $date): bool
{
    try {
        $mail = buildMailer();
        $mail->addAddress(RECRUITER_EMAIL);
        foreach (array_filter(array_map('trim', explode(',', CC_EMAILS))) as $cc) {
            if (filter_var($cc, FILTER_VALIDATE_EMAIL)) $mail->addCC($cc);
        }
        $mail->Subject = APP_NAME . " — Screening Report: {$name} / {$title}";

        $vColor = $verdict['color'];
        $vText  = htmlspecialchars($verdict['text']);
        $vScore = number_format($verdict['score'], 1);
        $nameH  = htmlspecialchars($name);
        $titleH = htmlspecialchars($title);

        $mail->isHTML(true);
        $mail->Body = <<<HTML
<div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;color:#1e293b;">
  <div style="background:#1e3a5f;padding:24px 32px;">
    <h2 style="color:#fff;margin:0;font-size:20px;">Kuma Talent</h2>
    <p style="color:#94a3b8;margin:4px 0 0;font-size:12px;">AI Screening Report</p>
  </div>
  <div style="padding:28px 32px;">
    <h3 style="margin:0 0 4px;">{$nameH}</h3>
    <p style="color:#64748b;font-size:13px;margin:0 0 18px;">{$titleH} &nbsp;·&nbsp; {$date}</p>
    <div style="background:{$vColor};color:#fff;display:inline-block;padding:7px 18px;border-radius:20px;font-weight:bold;font-size:14px;">
      {$vText} &nbsp; {$vScore}/10
    </div>
    <p style="margin-top:20px;color:#475569;font-size:14px;">The full screening report with score breakdown and charts is attached as a PDF.</p>
  </div>
  <div style="background:#f8fafc;padding:14px 32px;border-top:1px solid #e2e8f0;font-size:11px;color:#94a3b8;">
    Generated by Kuma Talent AI Screener
  </div>
</div>
HTML;
        $mail->AltBody = "Screening Report — {$name} | {$title} | {$vText} {$vScore}/10\n\nSee attached PDF for full report.";

        // Attach PDF
        $mail->addAttachment($pdfPath, $pdfFilename);
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Kuma email error: ' . $e->getMessage());
        return false;
    }
}

function sendCandidateCopy(string $name, string $email, string $title, string $date): void
{
    try {
        $mail = buildMailer();
        $mail->addAddress($email, $name);
        $mail->Subject = 'Your screening interview — ' . APP_NAME;
        $mail->isHTML(true);
        $nameH  = htmlspecialchars($name);
        $titleH = htmlspecialchars($title);
        $mail->Body = <<<HTML
<div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;color:#1e293b;">
  <div style="background:#1e3a5f;padding:24px 32px;">
    <h2 style="color:#fff;margin:0;">Kuma Talent</h2>
  </div>
  <div style="padding:28px 32px;">
    <p>Hi {$nameH},</p>
    <p>Thank you for completing your screening interview for <strong>{$titleH}</strong>.</p>
    <p>Your responses have been recorded and the Kuma Talent team will review them carefully. We'll be in touch with next steps soon.</p>
    <p style="color:#64748b;font-size:12px;margin-top:24px;">Interview date: {$date}</p>
  </div>
</div>
HTML;
        $mail->AltBody = "Hi {$name},\n\nThank you for your screening interview for {$title}. The team will be in touch soon.\n\n— Kuma Talent";
        $mail->send();
    } catch (Exception $e) {
        error_log('Kuma candidate email error: ' . $e->getMessage());
    }
}

function buildMailer(): PHPMailer
{
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = SMTP_SECURE;
    $mail->Port       = SMTP_PORT;
    $mail->CharSet    = 'UTF-8';
    $mail->setFrom(SMTP_USER, SENDER_NAME);
    return $mail;
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function shorten(string $s, int $max): string { return mb_strlen($s) > $max ? mb_substr($s, 0, $max) . '…' : $s; }

function buildTranscriptText(string $candidateName, string $jobTitle, string $jobCode, string $date, array $messages): string
{
    $sep  = str_repeat('═', 60);
    $text = "INTERVIEW TRANSCRIPT\n";
    $text .= "Candidate : {$candidateName}\n";
    $text .= "Position  : {$jobTitle}" . ($jobCode ? " ({$jobCode})" : '') . "\n";
    $text .= "Date      : {$date}\n";
    $text .= $sep . "\n\n";

    foreach ($messages as $msg) {
        $content = trim(str_replace('[INTERVIEW_COMPLETE]', '', $msg['content']));
        if (!$content || $content === 'begin') continue;
        $role = $msg['role'] === 'user' ? strtoupper($candidateName) : 'INTERVIEWER';
        $text .= "[{$role}]\n{$content}\n\n";
    }

    $text .= $sep . "\n";
    $text .= "AI DETECTION: Copy the candidate's responses (sections labeled with their name)\n";
    $text .= "and paste into Originality.ai, GPTZero, or Copyleaks to check for AI-generated content.\n";
    return $text;
}

function logInterview(array $entry): void
{
    $file = INTERVIEWS_FILE;
    $log  = file_exists($file) ? (json_decode(file_get_contents($file), true) ?? []) : [];
    array_unshift($log, $entry);
    if (count($log) > 1000) $log = array_slice($log, 0, 1000);
    if (!is_dir(dirname($file))) mkdir(dirname($file), 0775, true);
    file_put_contents($file, json_encode($log, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
