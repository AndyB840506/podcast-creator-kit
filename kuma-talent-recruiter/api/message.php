<?php
ignore_user_abort(true);
set_time_limit(0);
require_once __DIR__ . '/../config.php';

// ── Debug log ─────────────────────────────────────────────────────────────────
$logFile = __DIR__ . '/../debug.log';
$log = fn(string $m) => file_put_contents($logFile, date('H:i:s') . " $m\n", FILE_APPEND);

// ── Capture fatal errors ──────────────────────────────────────────────────────
register_shutdown_function(function () use ($logFile) {
    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        file_put_contents($logFile, date('H:i:s') . " FATAL: {$e['message']} in {$e['file']} line {$e['line']}\n", FILE_APPEND);
        if (!headers_sent()) header('Content-Type: application/json');
        echo json_encode(['error' => 'PHP Fatal: ' . $e['message']]);
    }
});

header('Content-Type: application/json');
$log("=== request start ===");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']); exit;
}

// ── Session ───────────────────────────────────────────────────────────────────
$log("starting session");
session_start();
$log("session started, job_title=" . ($_SESSION['job_title'] ?? 'MISSING'));

if (empty($_SESSION['job_title'])) {
    echo json_encode(['error' => 'Session expired']); exit;
}
if (!empty($_SESSION['interview_done'])) {
    echo json_encode(['error' => 'Interview already completed']); exit;
}

$jobTitle        = $_SESSION['job_title'];
$jobDescription  = $_SESSION['job_description'];
$language        = $_SESSION['language'];
$jobLevel        = $_SESSION['job_level'] ?? 'professional';
$requiresEnglish = (bool) ($_SESSION['requires_english'] ?? false);
$customQuestions = $_SESSION['custom_questions'] ?? [];
$jdParsed        = $_SESSION['jd_parsed'] ?? [];
$cognitiveMemory = $_SESSION['cognitive_memory'] ?? [];
$messages        = $_SESSION['messages'];
$log("messages in session: " . count($messages));
session_write_close();
$log("session closed");

// ── Input ─────────────────────────────────────────────────────────────────────
$rawInput    = file_get_contents('php://input');
$log("raw input: " . substr($rawInput, 0, 100));
$input       = json_decode($rawInput, true) ?? [];
$isStart     = !empty($input['start']);
$userMessage = trim($input['message'] ?? '');
$log("isStart=$isStart userMessage=" . substr($userMessage, 0, 50));

if (!$isStart && $userMessage === '') {
    echo json_encode(['error' => 'Empty message']); exit;
}
if (count($messages) >= MAX_MESSAGES) {
    session_start();
    $_SESSION['interview_done'] = true;
    session_write_close();
    echo json_encode(['success' => true, 'message' => '', 'interview_done' => true]); exit;
}

// If start:true but interview already has messages, return last assistant message
if ($isStart && count($messages) > 0) {
    $log("duplicate start — returning last message");
    $lastAssistant = '';
    foreach (array_reverse($messages) as $m) {
        if ($m['role'] === 'assistant') { $lastAssistant = $m['content']; break; }
    }
    echo json_encode(['success' => true, 'message' => $lastAssistant, 'interview_done' => false]);
    exit;
}

// ── Call LLM ──────────────────────────────────────────────────────────────────
$effectiveMessage = $isStart ? 'begin' : $userMessage;
$messagesForApi   = $messages;
$messagesForApi[] = ['role' => 'user', 'content' => $effectiveMessage];
$log("messages for API: " . count($messagesForApi));

$systemPrompt = buildSystemPrompt($jobTitle, $jobDescription, $language, $jobLevel, $requiresEnglish, $customQuestions, $jdParsed, $cognitiveMemory);
$log("calling LLM (provider=" . LLM_CHAT_PROVIDER . " model=" . LLM_CHAT_MODEL . ")...");

$aiText = callLLMChat($messagesForApi, $systemPrompt, 1200);
$log("aiText length: " . strlen((string)$aiText));

if ($aiText === null) {
    echo json_encode(['error' => 'LLM call failed. Check provider config in admin settings.']); exit;
}

if (empty($aiText)) {
    echo json_encode(['error' => 'Empty AI response']); exit;
}

// ── Parse and strip cognitive update block ─────────────────────────────────────
$cognitiveUpdate = [];
$cleanAiText = $aiText;
if (preg_match('/\[COGNITIVE_UPDATE\](.*?)\[\/COGNITIVE_UPDATE\]/s', $aiText, $cuMatch)) {
    $cuData = json_decode(trim($cuMatch[1]), true);
    if (is_array($cuData)) $cognitiveUpdate = $cuData;
    $cleanAiText = trim(preg_replace('/\[COGNITIVE_UPDATE\].*?\[\/COGNITIVE_UPDATE\]/s', '', $aiText));
}
$log("cognitiveUpdate parsed: " . json_encode($cognitiveUpdate));

// ── Update cognitive memory ────────────────────────────────────────────────────
$updatedMemory = $cognitiveMemory;
if (!empty($cognitiveUpdate)) {
    if (!empty($cognitiveUpdate['evasion']))  $updatedMemory['evasion_count']   = ($updatedMemory['evasion_count'] ?? 0) + 1;
    if (!empty($cognitiveUpdate['generic']))  $updatedMemory['generic_count']   = ($updatedMemory['generic_count'] ?? 0) + 1;
    if (!empty($cognitiveUpdate['strong']))   $updatedMemory['strong_answers']  = ($updatedMemory['strong_answers'] ?? 0) + 1;
    if (!empty($cognitiveUpdate['weak']))     $updatedMemory['weak_answers']    = ($updatedMemory['weak_answers'] ?? 0) + 1;

    $scriptSignal = (int)($cognitiveUpdate['script_signal'] ?? 0);
    if ($scriptSignal >= 2) $updatedMemory['script_signals'] = ($updatedMemory['script_signals'] ?? 0) + 1;

    // Accumulate technologies mentioned
    if (!empty($cognitiveUpdate['tech_mentioned']) && is_array($cognitiveUpdate['tech_mentioned'])) {
        $existing = $updatedMemory['technologies_mentioned'] ?? [];
        $merged   = array_values(array_unique(array_merge($existing, $cognitiveUpdate['tech_mentioned'])));
        $updatedMemory['technologies_mentioned'] = array_slice($merged, -20); // keep last 20
    }

    // Add memory hook
    if (!empty($cognitiveUpdate['memory_hook'])) {
        $updatedMemory['memory_hooks'][] = $cognitiveUpdate['memory_hook'];
        $updatedMemory['memory_hooks']   = array_slice($updatedMemory['memory_hooks'], -5);
    }

    // Add contradiction
    if (!empty($cognitiveUpdate['contradiction'])) {
        $updatedMemory['contradictions'][] = $cognitiveUpdate['contradiction'];
        $updatedMemory['contradictions']   = array_slice($updatedMemory['contradictions'], -3);
    }

    // Update difficulty
    if (($cognitiveUpdate['difficulty_adjust'] ?? 'same') === 'up')   $updatedMemory['current_difficulty'] = 'high';
    if (($cognitiveUpdate['difficulty_adjust'] ?? 'same') === 'down') $updatedMemory['current_difficulty'] = 'low';

    // Update stage
    if (!empty($cognitiveUpdate['stage'])) $updatedMemory['stage'] = $cognitiveUpdate['stage'];

    // Update authenticity score: good answers push up, evasion/script push down
    $authDelta = 0;
    if (!empty($cognitiveUpdate['strong']))   $authDelta += 5;
    if (!empty($cognitiveUpdate['evasion']))  $authDelta -= 8;
    if ($scriptSignal >= 2)                   $authDelta -= 10;
    if (!empty($cognitiveUpdate['generic']))  $authDelta -= 4;
    $updatedMemory['authenticity_score'] = max(0, min(100, ($updatedMemory['authenticity_score'] ?? 50) + $authDelta));
}
$log("updatedMemory: auth=" . ($updatedMemory['authenticity_score'] ?? 50) . " difficulty=" . ($updatedMemory['current_difficulty'] ?? 'medium'));

// ── Save session ──────────────────────────────────────────────────────────────
$log("saving to session...");
session_start();
if ($isStart) {
    $_SESSION['messages'][] = ['role' => 'user',      'content' => 'begin'];
    $_SESSION['messages'][] = ['role' => 'assistant', 'content' => $aiText];
} else {
    $_SESSION['messages'][] = ['role' => 'user',      'content' => $userMessage];
    $_SESSION['messages'][] = ['role' => 'assistant', 'content' => $aiText];
}
$_SESSION['cognitive_memory'] = $updatedMemory;
$interviewDone = str_contains($aiText, '[INTERVIEW_COMPLETE]');
if ($interviewDone) $_SESSION['interview_done'] = true;
session_write_close();
$log("session saved. interviewDone=$interviewDone");

// ── Output (send clean text without cognitive update block) ───────────────────
$log("sending response");
echo json_encode([
    'success'        => true,
    'message'        => $cleanAiText,
    'interview_done' => $interviewDone,
]);
$log("=== request end ===");
