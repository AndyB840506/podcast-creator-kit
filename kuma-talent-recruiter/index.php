<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kuma Talent — Screening Interview</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="form-page">

  <!-- Header -->
  <header class="form-header">
    <div class="logo">
      <div class="logo-mark">K</div>
      <span class="logo-name">Kuma Talent</span>
    </div>
  </header>

  <!-- Hero -->
  <main class="form-main">
    <div class="hero">
      <h1>Your application starts here</h1>
      <p>This AI-powered screening takes about 15 minutes. It's a friendly conversation — answer honestly, no trick questions.</p>
    </div>

    <!-- Form card -->
    <div class="form-card">
      <form id="interviewForm" novalidate>

        <div class="form-row two-col">
          <div class="form-group">
            <label for="candidate_name">Full name <span class="required">*</span></label>
            <input type="text" id="candidate_name" name="candidate_name"
                   placeholder="Your full name" autocomplete="name" required>
            <span class="field-error" id="err-name"></span>
          </div>
          <div class="form-group">
            <label for="candidate_email">
              Email
              <span class="optional">optional — receive a copy</span>
            </label>
            <input type="email" id="candidate_email" name="candidate_email"
                   placeholder="you@email.com" autocomplete="email">
            <span class="field-error" id="err-email"></span>
          </div>
        </div>

        <div class="form-group">
          <label for="job_code">Job code <span class="required">*</span></label>
          <input type="text" id="job_code" name="job_code"
                 placeholder="e.g. KT-001" autocomplete="off"
                 style="text-transform:uppercase;letter-spacing:0.05em;" required>
          <span class="field-error" id="err-code"></span>

          <!-- Job preview card (hidden by default) -->
          <div id="jobPreview" style="display:none;margin-top:10px;padding:12px 16px;border-radius:8px;border-left:3px solid #16a34a;background:#f0fdf4;">
            <div style="display:flex;align-items:center;gap:10px;">
              <span id="jobPreviewBadge" style="font-size:11px;font-weight:600;letter-spacing:0.07em;padding:2px 8px;border-radius:4px;background:#1e3a5f;color:#fff;"></span>
              <span id="jobPreviewTitle" style="font-size:14px;font-weight:500;color:#1e3a5f;"></span>
            </div>
          </div>
        </div>

        <div id="formError" class="form-error hidden"></div>

        <button type="submit" class="btn-primary" id="submitBtn" disabled>
          <span id="btnText">Start Interview</span>
          <span id="btnSpinner" class="spinner hidden"></span>
          <span id="btnArrow">→</span>
        </button>

      </form>
    </div>

    <p class="privacy-note">Your responses are used solely for this screening. Kuma Talent handles all data in accordance with applicable privacy regulations.</p>
  </main>

  <script src="assets/js/app.js"></script>
</body>
</html>
