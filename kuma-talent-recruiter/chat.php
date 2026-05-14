<?php
session_start();
if (empty($_SESSION['job_title'])) {
    header('Location: index.php?error=session');
    exit;
}
$jobTitle      = htmlspecialchars($_SESSION['job_title'], ENT_QUOTES, 'UTF-8');
$candidateName = htmlspecialchars($_SESSION['candidate_name'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kuma Talent — Interview</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="chat-page"
      data-candidate="<?= $candidateName ?>"
      data-job="<?= $jobTitle ?>">

  <!-- ── Header ── -->
  <header class="chat-header">
    <div class="logo">
      <div class="logo-mark">K</div>
      <span class="logo-name">Kuma Talent</span>
    </div>
    <div class="interview-label">
      <span class="label-dot"></span>
      Screening Interview
    </div>
  </header>

  <!-- ── Chat area ── -->
  <div class="chat-body" id="chatBody">
    <div class="messages" id="messages"></div>

    <!-- Typing indicator -->
    <div class="typing-wrap hidden" id="typingIndicator">
      <div class="avatar">K</div>
      <div class="message ai typing-indicator">
        <span></span><span></span><span></span>
      </div>
    </div>
  </div>

  <!-- ── Input area ── -->
  <div class="chat-input-area" id="inputArea">
    <div class="chat-input-wrap">
      <textarea id="chatInput"
                placeholder="Type your answer here…"
                rows="1"
                maxlength="3000"
                disabled></textarea>
      <button id="sendBtn" class="send-btn" disabled>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="22" y1="2" x2="11" y2="13"></line>
          <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
        </svg>
      </button>
    </div>
    <p class="input-hint" id="inputHint">Interview in progress — please wait…</p>
  </div>

  <!-- ── Report overlay ── -->
  <div class="overlay hidden" id="reportOverlay">
    <div class="overlay-card">
      <div class="overlay-spinner"></div>
      <h3>Generating your report…</h3>
      <p>Just a moment while we process your interview.</p>
    </div>
  </div>

  <!-- ── Completion screen ── -->
  <div class="overlay hidden" id="completionScreen">
    <div class="overlay-card completion">
      <div class="check-icon">✓</div>
      <h2>Interview complete</h2>
      <p>Thank you, <strong id="completionName"></strong>.</p>
      <p class="completion-sub">The Kuma Talent team will review your responses carefully and be in touch with next steps soon.</p>
      <div class="completion-detail" id="completionDetail"></div>
    </div>
  </div>

  <script src="assets/js/app.js"></script>
</body>
</html>
