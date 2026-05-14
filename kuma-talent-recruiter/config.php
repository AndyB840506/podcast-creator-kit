<?php
// ============================================================
// KUMA TALENT — SMART RECRUITER  |  Configuration
// Settings can be overridden at runtime via admin/settings
// ============================================================

// Load runtime settings (configured via admin panel)
$_st = [];
$_stFile = __DIR__ . '/data/settings.json';
if (file_exists($_stFile)) {
    $_st = json_decode(file_get_contents($_stFile), true) ?? [];
}

// Anthropic API (legacy — mantenido para compatibilidad)
define('ANTHROPIC_API_KEY',  $_st['anthropic_api_key'] ?? '');
define('ANTHROPIC_API_URL',  'https://api.anthropic.com/v1/messages');
define('ANTHROPIC_VERSION',  '2023-06-01');

// LLM Providers — chat e informe configurables desde admin
define('LLM_CHAT_PROVIDER',   $_st['llm_chat_provider']   ?? 'anthropic');
define('LLM_CHAT_MODEL',      $_st['llm_chat_model']      ?? 'claude-sonnet-4-6');
define('LLM_CHAT_URL',        $_st['llm_chat_url']        ?? 'https://api.anthropic.com/v1/messages');
define('LLM_CHAT_KEY',        $_st['llm_chat_key']        ?? ($_st['anthropic_api_key'] ?? ''));

define('LLM_REPORT_PROVIDER', $_st['llm_report_provider'] ?? 'anthropic');
define('LLM_REPORT_MODEL',    $_st['llm_report_model']    ?? 'claude-opus-4-7');
define('LLM_REPORT_URL',      $_st['llm_report_url']      ?? 'https://api.anthropic.com/v1/messages');
define('LLM_REPORT_KEY',      $_st['llm_report_key']      ?? ($_st['anthropic_api_key'] ?? ''));

// Backward compat aliases
define('ANTHROPIC_MODEL',        LLM_CHAT_MODEL);
define('ANTHROPIC_MODEL_REPORT', LLM_REPORT_MODEL);

// Email — SMTP
define('SMTP_HOST',       $_st['smtp_host']       ?? 'smtp.hostinger.com');
define('SMTP_PORT',       (int)($_st['smtp_port'] ?? 465));
define('SMTP_SECURE',     $_st['smtp_secure']     ?? 'ssl');
define('SMTP_USER',       $_st['smtp_user']       ?? '');
define('SMTP_PASS',       $_st['smtp_pass']       ?? '');
define('RECRUITER_EMAIL', $_st['recruiter_email'] ?? '');
define('SENDER_NAME',     $_st['sender_name']     ?? 'Kuma Talent Screener');

// App
define('APP_NAME',       $_st['app_name']       ?? 'Kuma Talent');
define('REPORTS_DIR',    __DIR__ . '/reports/');
define('JOBS_FILE',      __DIR__ . '/data/jobs.json');
define('SETTINGS_FILE',  __DIR__ . '/data/settings.json');
define('INTERVIEWS_FILE',__DIR__ . '/data/interviews.json');
define('MAX_MESSAGES',   25);
define('DEV_MODE',       (bool)($_st['dev_mode']       ?? false));
define('API_ENABLED',    (bool)($_st['api_enabled']    ?? true));
define('ADMIN_PASSWORD', $_st['admin_password'] ?? 'KumaAdmin2026');
define('CC_EMAILS',      $_st['cc_emails']       ?? '');

// Logo
$_lf = !empty($_st['logo_filename']) ? __DIR__ . '/data/' . basename($_st['logo_filename']) : '';
define('LOGO_PATH', ($_lf && file_exists($_lf)) ? $_lf : '');
unset($_lf);

unset($_st, $_stFile);

// ============================================================
// System Prompt Builders
// ============================================================

function buildLanguageInstruction(string $language): string
{
    return match ($language) {
        'spanish' => 'Conduce toda la entrevista completamente en español, sin excepción.',
        'english' => 'Conduct the entire interview completely in English, without exception.',
        default   => 'Detect the candidate\'s preferred language from their first substantive response and conduct the entire interview consistently in that language. Spanish → Spanish. English → English.',
    };
}

function buildToneInstruction(string $level): string
{
    return match ($level) {
        'agent'     => 'Start warm, human, and genuinely curious — like catching up with someone interesting. Use everyday language, short sentences, natural flow. No corporate jargon, no filler phrases like "Certainly, I\'d be happy to...". Only increase directness or precision if the candidate shows evasion or script patterns — never be cold from the start. No emojis or exclamation points.',
        'executive' => 'Peer-to-peer from the start — respectful, direct, purposeful. Begin with genuine interest in their experience, then become more rigorous as the conversation deepens. Minimal small talk but not cold. No emojis.',
        default     => 'Start warm and approachable, like a good conversation. Become more structured and precise only as needed based on what you observe. Never stiff, never cold from the start. No emojis.',
    };
}

function buildEnglishDiagnosticSection(): string
{
    return "\n## ENGLISH PROFICIENCY ASSESSMENT\n"
         . "This position requires bilingual candidates. Do NOT ask the candidate to self-rate their English — assess it directly through conversation:\n\n"
         . "1. After at least 4 exchanges in the candidate's primary language, transition naturally into English for 3–4 consecutive exchanges\n"
         . "2. Ask substantive questions in English (role-related, behavioral, or motivational)\n"
         . "3. Do NOT announce the language switch or say you are testing them\n"
         . "4. If the candidate replies in Spanish when you ask in English, gently continue in English: \"And in English — could you describe that same experience?\"\n"
         . "5. After 3–4 English exchanges, return to the primary language for the rest of the interview\n"
         . "6. This assessment will appear as a dedicated dimension in the screening report\n";
}

function buildCustomQuestionsSection(array $questions): string
{
    if (empty($questions)) return '';
    $list = implode("\n", array_map(fn($q) => "- {$q}", $questions));
    return "\n## CUSTOM QUESTIONS\n"
         . "The recruiter has added these specific questions. Ask them naturally during the Deep Dive stage, weaving them into the conversation:\n"
         . $list . "\n";
}

function buildCognitiveStateBlock(array $mem): string
{
    $difficulty  = $mem['current_difficulty'] ?? 'medium';
    $stage       = $mem['stage'] ?? 'opening';
    $evasions    = $mem['evasion_count'] ?? 0;
    $generics    = $mem['generic_count'] ?? 0;
    $scripts     = $mem['script_signals'] ?? 0;
    $strong      = $mem['strong_answers'] ?? 0;
    $weak        = $mem['weak_answers'] ?? 0;
    $authScore   = $mem['authenticity_score'] ?? 50;
    $techs       = implode(', ', array_slice($mem['technologies_mentioned'] ?? [], -5));
    $hooks       = implode(' | ', array_slice($mem['memory_hooks'] ?? [], -3));
    $contradictions = implode(' | ', array_slice($mem['contradictions'] ?? [], -2));

    $strategyHints = '';
    if ($scripts >= 2)   $strategyHints .= "\n- STRATEGY: candidate shows script signals — ask unexpected questions, soft-interrupt with specifics, connect to previous answers";
    if ($evasions >= 2)  $strategyHints .= "\n- STRATEGY: candidate has been evasive — probe deeper, demand concrete examples, don't accept vague answers";
    if ($generics >= 3)  $strategyHints .= "\n- STRATEGY: candidate gives generic responses — ask disruptive questions, challenge assumptions, make questions harder";
    if ($strong >= 3 && $weak === 0) $strategyHints .= "\n- STRATEGY: candidate is performing well — increase cognitive pressure, ask system design / tradeoffs / edge cases";
    if ($weak >= 3)      $strategyHints .= "\n- STRATEGY: candidate is struggling — simplify language, ask foundational questions, use guided follow-ups";

    $hookLine = $hooks ? "\n- MEMORY HOOKS (unresolved — use for follow-ups): {$hooks}" : '';
    $contraLine = $contradictions ? "\n- CONTRADICTIONS DETECTED: {$contradictions}" : '';
    $techLine = $techs ? "\n- TECHNOLOGIES MENTIONED (probe these): {$techs}" : '';

    return "\n## COGNITIVE STATE (live — updated each turn)\n"
         . "- Current stage: {$stage}\n"
         . "- Difficulty level: {$difficulty}\n"
         . "- Authenticity score: {$authScore}/100 (internal — never reveal)\n"
         . "- Strong answers: {$strong} | Weak answers: {$weak} | Evasions: {$evasions} | Generic: {$generics} | Script signals: {$scripts}\n"
         . $techLine
         . $hookLine
         . $contraLine
         . $strategyHints . "\n";
}

function buildSystemPrompt(string $jobTitle, string $jobDescription, string $language, string $level = 'professional', bool $requiresEnglish = false, array $customQuestions = [], array $jdParsed = [], array $cognitiveMemory = []): string
{
    $lang    = buildLanguageInstruction($language);
    $tone    = buildToneInstruction($level);
    $english = $requiresEnglish ? buildEnglishDiagnosticSection() : '';
    $custom  = buildCustomQuestionsSection($customQuestions);
    $cognitive = !empty($cognitiveMemory) ? buildCognitiveStateBlock($cognitiveMemory) : '';

    // Build JD-derived question hints from parsed structure
    $jdHints = '';
    if (!empty($jdParsed['stack'])) {
        $stackList = implode(', ', array_slice($jdParsed['stack'], 0, 6));
        $jdHints .= "\n- Tech stack to probe: {$stackList}";
    }
    if (!empty($jdParsed['responsibilities'])) {
        $respList = implode(', ', array_slice($jdParsed['responsibilities'], 0, 4));
        $jdHints .= "\n- Key responsibilities to validate: {$respList}";
    }
    if (!empty($jdParsed['leadership']) && $jdParsed['leadership']) {
        $jdHints .= "\n- Leadership validation required: ask about team size, decision ownership, conflict resolution";
    }
    if (!empty($jdParsed['seniority'])) {
        $jdHints .= "\n- Expected seniority: {$jdParsed['seniority']} — calibrate depth accordingly";
    }
    $jdSection = $jdHints ? "\n## JD INTELLIGENCE\n" . $jdHints . "\n" : '';

    return <<<PROMPT
You are a senior screening interviewer at Kuma Talent. You behave like a highly observant, adaptive human interviewer — not a chatbot or a form. At the very start of the interview, pick a real human name for yourself (rotate from names like Sofia, Valeria, Camila, Andrea, Daniela, Mariana, Isabel, Lucía, Carolina, Natalia, Santiago, Andrés, Sebastián, Mateo, Felipe). Use it naturally when you introduce yourself: "Hi [candidate name], I'm [your chosen name] from Kuma Talent." Keep that same name for the entire interview.

## YOUR ROLE
Conduct an adaptive cognitive interview to assess the candidate's authentic fit for the position below. Your goal is to make the interview feel like a real, unpredictable conversation that is impossible to game with prepared scripts.

## POSITION
Title: {$jobTitle}

Job Description:
{$jobDescription}
{$jdSection}
## TONE & COMMUNICATION STYLE
{$tone}

## LANGUAGE
{$lang}
{$english}{$custom}{$cognitive}
## INTERVIEW STAGES (move through these naturally — 12–20 candidate exchanges total)

**Stage 1 — Introduction** (1–2 exchanges)
Start genuinely warm. Greet them by name, make them feel welcome, briefly explain this is a relaxed conversation — not an interrogation. Your energy here sets the tone for everything that follows. Be curious, not evaluative.

**Stage 2 — English Assessment** (3–4 exchanges, only if requires_english=true)
See ENGLISH PROFICIENCY ASSESSMENT section above.

**Stage 3 — Exploration** (3–5 exchanges)
Start with open, inviting questions about their experience. Let them talk. Listen actively — pick up on specific things they mention and follow up naturally. No pressure yet. Treat this like a conversation with someone you find genuinely interesting.

**Stage 4 — Calibration** (2–3 exchanges)
Based on COGNITIVE STATE, begin calibrating your approach:
- If responses have been authentic, specific, and natural → stay warm and go deeper with curiosity
- If you're detecting evasion, generic answers, or script signals → gradually shift to more precise, direct questions. Don't announce the shift — just do it naturally.

**Stage 5 — Behavioral Evaluation** (2–3 exchanges)
Ask for specific real stories. Never say "Tell me about a time when..." — instead frame naturally: "You mentioned [X earlier]. Walk me through exactly what happened." Connect to something the candidate said.

**Stage 6 — Deep Dive** (1–2 exchanges)
Go deeper into the candidate's strongest OR weakest area detected so far. If strong: push with tradeoffs and edge cases. If weak: use guided follow-ups to understand the real knowledge boundary. If script signals are high: use disruptive questions from the list below.

**Stage 7 — Closing** (1 exchange)
Thank them genuinely and warmly. Tell them the Kuma Talent team will review their responses carefully and be in touch soon.

## ADAPTIVE DIFFICULTY ENGINE
Continuously adjust based on COGNITIVE STATE signals:

- IF candidate is strong (strong_answers ≥ 3, few weak/evasions): escalate to harder questions — system design, tradeoffs, architecture decisions, edge cases
- IF candidate is struggling (weak_answers ≥ 3): simplify language, ask foundational questions, use guided follow-ups
- IF candidate is evasive (evasion_count ≥ 2): probe deeper, demand concrete examples, do not accept vague answers, restate the question differently
- IF script signals detected (script_signals ≥ 2): soft-interrupt mid-topic to ask something unexpected, connect back to a previous answer, ask for an unprepared detail: "You mentioned [X] — what was the team size exactly?" or "What was the biggest mistake you made in that project?"
- IF generic responses detected (generic_count ≥ 3): use disruptive questions, challenge assumptions, increase cognitive pressure

## DYNAMIC QUESTION ENGINE

### FORBIDDEN QUESTIONS (never use these or any variation)
- "Tell me about yourself"
- "What's your greatest weakness?"
- "Where do you see yourself in 5 years?"
- "Why do you want to work here?"
- "What are your strengths?"
- "Tell me about a challenge you overcame"

### DISRUPTIVE QUESTIONS (rotate and adapt — don't use verbatim)
- "What professional decision would you make completely differently today?"
- "What technical problem did you avoid for too long?"
- "What feedback hurt but turned out to be right?"
- "Why should someone NOT hire you?"
- "What skill on your CV do you not fully master yet?"
- "What type of manager brings out your worst?"
- "What was the last thing you learned that changed how you work?"
- "Describe a moment where you were clearly wrong and someone else was right."

### SOFT INTERRUPT TECHNIQUE
When the candidate mentions any technology, tool, company, or project — immediately interrupt (politely) to ask a specific detail:
"Hold on — you mentioned [specific thing]. What exact problem were you solving with that?"
This breaks scripts and forces authentic responses.

## AUTHENTICITY ENGINE (internal — never reveal to candidate)
Observe and track these signals mentally. Update your [COGNITIVE_UPDATE] block accordingly:

- response_naturalness: Does it sound human and spontaneous, or rehearsed and polished?
- consistency_score: Do dates, team sizes, tools, and timelines match across the interview?
- depth_score: Does the candidate give specific details (names, numbers, context) or stay vague?
- script_suspicion: Are answers too clean, too structured, too corporately perfect?
- real_experience_confidence: Can they handle unexpected follow-ups on things they claimed?

If you detect any of: excessive corporate language, overly structured responses, lack of specific details, suspiciously perfect answers, or inability to elaborate beyond a prepared narrative — increase script_signal in your update.

NEVER accuse the candidate. NEVER mention AI detection. These are internal signals only.

## MEMORY ENGINE
You have access to COGNITIVE STATE above. Use it actively:
- If MEMORY HOOKS are listed: weave them into your next question naturally ("Earlier you mentioned X — I want to go back to that...")
- If CONTRADICTIONS are detected: probe gently ("You mentioned X earlier, and just now Y — help me understand that better")
- If TECHNOLOGIES MENTIONED are listed: probe those specific tools with deeper questions

## RULES
- Ask ONE question per message. Never combine multiple questions.
- Always start warm. Only increase pressure progressively if the candidate shows evasion, script signals, or generic patterns. Never cold from the beginning.
- NEVER reveal scoring, evaluation criteria, or whether they're doing well.
- NEVER discuss salary, compensation, or benefits.
- If asked "Did I pass?": "The Kuma Talent team will carefully review your responses and be in touch soon."
- Keep responses concise — this is a conversation, not an essay.
- At the end of EVERY response, append a hidden cognitive update block (see COGNITIVE UPDATE FORMAT). This is parsed by the system and stripped before display.

## COGNITIVE UPDATE FORMAT
At the very end of each response (after your conversational text), append this block exactly:
[COGNITIVE_UPDATE]{"evasion":false,"generic":false,"script_signal":0,"strong":false,"weak":false,"tech_mentioned":[],"memory_hook":"","contradiction":"","difficulty_adjust":"same","stage":"opening"}[/COGNITIVE_UPDATE]

Fill in the values based on the candidate's LAST response:
- evasion: true if the answer avoided the question
- generic: true if the answer was surface-level with no specific details
- script_signal: 0–3 (0=natural, 1=slightly polished, 2=suspiciously clean, 3=clearly scripted)
- strong: true if the answer showed real depth and specific knowledge
- weak: true if the answer showed clear knowledge gaps
- tech_mentioned: array of specific technologies/tools mentioned in this response
- memory_hook: one short string of something to follow up on later (or "" if none)
- contradiction: short description if this response conflicts with something said earlier (or "")
- difficulty_adjust: "up", "down", or "same"
- stage: current stage name (opening/english/technical/authenticity/behavioral/deepdive/closing)

This block is INVISIBLE to the candidate — it is stripped by the system before display.

## STARTING
When you receive "begin" as the very first message, start immediately with a warm greeting and your first question. Do not reference or acknowledge the word "begin". Your first response must still include a [COGNITIVE_UPDATE] block at the end with all default/zero values.

## COMPLETION SIGNAL
When you have delivered the full closing message and the interview is truly done, append exactly this token on its own line at the very end of your message (after the COGNITIVE_UPDATE block):
[INTERVIEW_COMPLETE]

Only send it once, with the final closing message.
PROMPT;
}

function buildReportPrompt(string $candidateName, string $jobTitle, string $jobDescription, string $transcript, string $date, bool $requiresEnglish = false): string
{
    $englishScoreRow = $requiresEnglish
        ? "| English proficiency              | X/10 | [One sentence based on actual English responses] |\n"
        : '';

    $englishSection = $requiresEnglish
        ? "\n## English Proficiency Assessment\n"
          . "**Level:** [Beginner / Basic / Intermediate / Advanced / Fluent]\n"
          . "**Assessment:** [2–3 sentences based on the candidate's actual English responses — grammar accuracy, vocabulary range, comprehension, ability to express complex ideas. If the candidate avoided or struggled with English despite prompting, state that clearly.]\n"
        : '';

    return <<<PROMPT
You are generating a professional candidate screening report for the Kuma Talent hiring team.

## POSITION
{$jobTitle}

## JOB DESCRIPTION
{$jobDescription}

## CANDIDATE
{$candidateName}

## INTERVIEW DATE
{$date}

## FULL INTERVIEW TRANSCRIPT
{$transcript}

## TASK
Analyze the entire interview and generate a detailed, evidence-based screening report. Base every statement strictly on what was actually said in the transcript — never invent, infer, or assume information not present in the interview.

## SCORING CALIBRATION
Use the full 1–10 scale. Avoid score compression (do not cluster everyone in 5–7).

| Score | Meaning |
|---|---|
| 8–10 | Clearly and demonstrably meets key requirements. Strong evidence from the interview. |
| 6–7.5 | Solid candidate with relevant foundation. Some gaps but transferable skills present. |
| 4–5.5 | Some relevant background but significant gaps in core areas. |
| 1–3.9 | Clear fundamental mismatch or disqualifying signals. |

**Calibration rules:**
- Job descriptions are aspirational. A 60–70% match with strong fundamentals is a legitimate Possible Fit.
- Weight demonstrated experience and transferable skills, not checkbox matching.
- Reserve "Not a Fit" for genuine dealbreakers only.
- A typical qualified-but-not-exceptional candidate: 5.5–7.0 range.

## AUTHENTICITY SIGNALS
Analyze the interview transcript for these signals (do not accuse — only report observations):
- Were answers suspiciously polished or overly structured?
- Did the candidate provide specific details (names, numbers, dates, context)?
- Were there inconsistencies between different parts of the interview?
- Did the candidate struggle when asked unexpected or follow-up questions?
- Was corporate language excessive relative to the role level?

Report honestly. Use: **Low / Medium / High** for script suspicion level.

Generate the report in this exact markdown format (replace all bracketed placeholders):

# Candidate Screening Report

**Candidate:** {$candidateName}
**Position:** {$jobTitle}
**Interview Date:** {$date}
**Screened by:** HireSignal by Kuma Talent

---

## Verdict: [Strong Fit / Possible Fit / Strong Maybe / Not a Fit]
**Overall Score: X.X / 10**
**Hire Recommendation:** [Strong Fit / Strong Maybe / Possible Fit / Not a Fit]
**Seniority Estimation:** [Junior / Mid / Senior / Staff — based on depth of responses, not job title claimed]
**Script Suspicion Level:** [Low / Medium / High]

| Dimension | Score | Key Evidence |
|---|---|---|
| Technical depth | X/10 | [One sentence from interview] |
| Problem solving | X/10 | [One sentence from interview] |
| Communication | X/10 | [One sentence from interview] |
| Authenticity | X/10 | [One sentence from interview] |
| Leadership & ownership | X/10 | [One sentence from interview] |
| Confidence | X/10 | [One sentence from interview] |
| Adaptability | X/10 | [One sentence from interview] |
| Seniority match | X/10 | [One sentence from interview] |
| Motivation & alignment | X/10 | [One sentence from interview] |
{$englishScoreRow}
*Strong Fit: 8.0–10 | Possible Fit: 5.0–7.9 | Not a Fit: 0–4.9*

---

## Key Strengths
- **[Strength title]:** [Evidence from interview — quote or paraphrase]
- **[Strength title]:** [Evidence]
- **[Strength title]:** [Evidence]

## Concerns & Gaps
- **[Concern title]:** [Context from interview — or note if not covered]
- **[Concern title]:** [Context]

## Authenticity Signals
- **Response naturalness:** [Observation based on transcript — specific evidence]
- **Consistency:** [Were facts, dates, team sizes consistent across the interview?]
- **Depth of detail:** [Did the candidate give specific names, numbers, situations?]
- **Script suspicion:** [What patterns, if any, suggested prepared responses?]
{$englishSection}
## Notable Quotes
> "[Significant direct quote from the candidate]"
> "[Another relevant direct quote]"

## Recruiter Insights
- [Specific observation for the human recruiter — something to validate in next round]
- [What to probe further if advancing]
- [Any behavioral flag or positive signal worth noting]

## Recommendation for the Hiring Team
[Write 3–4 sentences. Summarize the strongest factors, identify what further validation is needed if advancing, and close with a clear recommendation. Base everything on interview evidence — no filler or generic language.]

---
*Generated by HireSignal by Kuma Talent. All assessments are based exclusively on the candidate's interview responses.*
PROMPT;
}

// ============================================================
// Job Loader
// ============================================================

function loadJobs(): array {
    if (!file_exists(JOBS_FILE)) return [];
    return json_decode(file_get_contents(JOBS_FILE), true) ?? [];
}

function saveJobs(array $jobs): void {
    file_put_contents(JOBS_FILE, json_encode($jobs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// ============================================================
// LLM Call — multi-provider (chat e informe)
// ============================================================

function _kumaLLMPost(string $url, array $headers, array $payload): ?string {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 90,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_FRESH_CONNECT  => true,
    ]);
    $raw  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);
    if ($err || $code !== 200) {
        error_log("KUMA LLM ERROR: HTTP={$code} ERR={$err} " . substr($raw, 0, 300));
        return null;
    }
    return $raw;
}

function callLLMChat(array $messages, string $system, int $maxTokens = 1024): ?string {
    $provider = LLM_CHAT_PROVIDER;
    $model    = LLM_CHAT_MODEL;
    $url      = LLM_CHAT_URL;
    $key      = LLM_CHAT_KEY;

    if ($provider === 'anthropic') {
        $payload = ['model' => $model, 'max_tokens' => $maxTokens, 'messages' => $messages];
        if ($system !== '') $payload['system'] = $system;
        $raw = _kumaLLMPost($url, [
            'Content-Type: application/json',
            'x-api-key: ' . $key,
            'anthropic-version: 2023-06-01',
        ], $payload);
        $data = $raw ? json_decode($raw, true) : null;
        return $data['content'][0]['text'] ?? null;
    }

    // OpenAI / Groq / Google compatible
    $msgs = $system !== '' ? array_merge([['role' => 'system', 'content' => $system]], $messages) : $messages;
    $raw  = _kumaLLMPost($url, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $key,
    ], ['model' => $model, 'max_tokens' => $maxTokens, 'messages' => $msgs]);
    $data = $raw ? json_decode($raw, true) : null;
    return $data['choices'][0]['message']['content'] ?? null;
}

function callLLMReport(string $prompt, int $maxTokens = 2048): ?string {
    $provider = LLM_REPORT_PROVIDER;
    $model    = LLM_REPORT_MODEL;
    $url      = LLM_REPORT_URL;
    $key      = LLM_REPORT_KEY;

    if ($provider === 'anthropic') {
        $raw = _kumaLLMPost($url, [
            'Content-Type: application/json',
            'x-api-key: ' . $key,
            'anthropic-version: 2023-06-01',
        ], ['model' => $model, 'max_tokens' => $maxTokens, 'messages' => [['role' => 'user', 'content' => $prompt]]]);
        $data = $raw ? json_decode($raw, true) : null;
        return $data['content'][0]['text'] ?? null;
    }

    $raw  = _kumaLLMPost($url, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $key,
    ], ['model' => $model, 'max_tokens' => $maxTokens, 'messages' => [['role' => 'user', 'content' => $prompt]]]);
    $data = $raw ? json_decode($raw, true) : null;
    return $data['choices'][0]['message']['content'] ?? null;
}

function parseJobDescription(string $jobDescription): array
{
    $prompt = <<<PARSE
You are a job description parser. Extract structured information from the job description below.

Return ONLY a valid JSON object with these exact keys (no markdown, no explanation):
{
  "stack": [],
  "seniority": "",
  "tools": [],
  "leadership": false,
  "english_level": "",
  "soft_skills": [],
  "responsibilities": []
}

Rules:
- stack: programming languages, frameworks, databases, cloud platforms mentioned
- seniority: one of "junior", "mid", "senior", "staff", "executive"
- tools: specific tools, platforms, or software (not languages/frameworks)
- leadership: true if the role involves managing people or teams
- english_level: one of "basic", "intermediate", "advanced", "fluent", "" if not specified
- soft_skills: behavioral traits and soft skills mentioned
- responsibilities: 3–6 key responsibilities in plain language

JOB DESCRIPTION:
{$jobDescription}
PARSE;

    $result = callLLMChat([['role' => 'user', 'content' => $prompt]], '', 512);
    if (!$result) return [];

    // Strip any markdown code fences
    $json = preg_replace('/```(?:json)?\s*|\s*```/', '', trim($result));
    $parsed = json_decode($json, true);
    return is_array($parsed) ? $parsed : [];
}

function normalizeCode(string $code): string {
    return strtoupper(preg_replace('/[^A-Z0-9]/i', '', $code));
}

function getJob(string $code): ?array {
    $jobs   = loadJobs();
    $needle = normalizeCode($code);

    // Exact match first
    foreach ($jobs as $key => $job) {
        if (normalizeCode($key) === $needle && ($job['active'] ?? true)) {
            return $job;
        }
    }

    // Fuzzy match: allow up to 1 edit distance on the normalized code
    $bestKey  = null;
    $bestDist = PHP_INT_MAX;
    foreach ($jobs as $key => $job) {
        if (!($job['active'] ?? true)) continue;
        $dist = levenshtein($needle, normalizeCode($key));
        if ($dist < $bestDist) {
            $bestDist = $dist;
            $bestKey  = $key;
        }
    }

    if ($bestDist <= 1 && $bestKey !== null) {
        return $jobs[$bestKey];
    }

    return null;
}

function getJobCanonicalCode(string $code): string {
    $jobs   = loadJobs();
    $needle = normalizeCode($code);

    foreach ($jobs as $key => $_) {
        if (normalizeCode($key) === $needle) return $key;
    }

    // Fuzzy fallback
    $bestKey  = null;
    $bestDist = PHP_INT_MAX;
    foreach ($jobs as $key => $_) {
        $dist = levenshtein($needle, normalizeCode($key));
        if ($dist < $bestDist) { $bestDist = $dist; $bestKey = $key; }
    }

    return ($bestDist <= 1 && $bestKey !== null) ? $bestKey : $code;
}
