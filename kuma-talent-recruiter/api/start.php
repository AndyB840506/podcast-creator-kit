<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

// Validate required fields
if (empty(trim($input['candidate_name'] ?? ''))) {
    http_response_code(400);
    echo json_encode(['error' => "Field 'candidate_name' is required"]);
    exit;
}

if (empty(trim($input['job_code'] ?? ''))) {
    http_response_code(400);
    echo json_encode(['error' => "Field 'job_code' is required"]);
    exit;
}

// Sanitize name and email
$candidateName  = trim(htmlspecialchars($input['candidate_name'], ENT_QUOTES, 'UTF-8'));
$candidateEmail = trim(filter_var($input['candidate_email'] ?? '', FILTER_SANITIZE_EMAIL));
$jobCodeRaw     = trim($input['job_code'] ?? '');

if (strlen($candidateName) > 120) {
    http_response_code(400);
    echo json_encode(['error' => 'Name too long']);
    exit;
}

// Look up job server-side
$job = getJob($jobCodeRaw);
if ($job === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Job code not found or inactive']);
    exit;
}

$jobCode        = strtoupper(trim($jobCodeRaw));
$jobTitle       = $job['title'];
$jobDescription = $job['description'];
$language       = $job['language'] ?? 'auto';
$jobLevel       = $job['level'] ?? 'agent';
$requiresEnglish = (bool) ($job['requires_english'] ?? false);

// Parse JD structure for adaptive interviewing
$jdParsed = parseJobDescription($jobDescription);

// Start session and store interview data
session_start();
session_regenerate_id(true);

$_SESSION['candidate_name']   = $candidateName;
$_SESSION['candidate_email']  = $candidateEmail;
$_SESSION['job_code']         = $jobCode;
$_SESSION['job_title']        = $jobTitle;
$_SESSION['job_description']  = $jobDescription;
$_SESSION['job_level']        = $jobLevel;
$_SESSION['requires_english']  = $requiresEnglish;
$_SESSION['language']          = $language;
$_SESSION['custom_questions']  = $job['custom_questions'] ?? [];
$_SESSION['jd_parsed']         = $jdParsed;
$_SESSION['messages']          = [];
$_SESSION['started_at']       = date('Y-m-d H:i:s');
$_SESSION['interview_done']   = false;
$_SESSION['cognitive_memory']  = [
    'technologies_mentioned' => [],
    'contradictions'         => [],
    'memory_hooks'           => [],
    'evasion_count'          => 0,
    'generic_count'          => 0,
    'script_signals'         => 0,
    'strong_answers'         => 0,
    'weak_answers'           => 0,
    'authenticity_score'     => 50,
    'current_difficulty'     => 'medium',
    'stage'                  => 'opening',
];

session_write_close();

echo json_encode(['success' => true]);
