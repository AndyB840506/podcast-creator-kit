/* ─────────────────────────────────────────────
   Kuma Talent — Smart Recruiter  |  Client JS
   ───────────────────────────────────────────── */

const isFormPage = document.body.classList.contains('form-page');
const isChatPage = document.body.classList.contains('chat-page');

// ══════════════════════════════════════════════
// FORM PAGE
// ══════════════════════════════════════════════
if (isFormPage) {
  const form       = document.getElementById('interviewForm');
  const submitBtn  = document.getElementById('submitBtn');
  const btnText    = document.getElementById('btnText');
  const btnSpinner = document.getElementById('btnSpinner');
  const btnArrow   = document.getElementById('btnArrow');
  const formError  = document.getElementById('formError');
  const codeInput  = document.getElementById('job_code');
  const jobPreview = document.getElementById('jobPreview');

  let validCode     = false;
  let resolvedTitle = '';
  let debounceTimer = null;

  // ── Uppercase + debounced lookup on code input ───────────────
  codeInput.addEventListener('input', () => {
    codeInput.value = codeInput.value.toUpperCase();
    clearError('err-code');
    clearTimeout(debounceTimer);
    const val = codeInput.value.trim();
    if (!val) {
      hidePreview();
      setSubmitEnabled(false);
      return;
    }
    debounceTimer = setTimeout(() => lookupCode(val), 400);
  });

  async function lookupCode(code) {
    try {
      const res  = await fetch('api/lookup.php?code=' + encodeURIComponent(code));
      const json = await res.json();
      if (json.found) {
        validCode     = true;
        resolvedTitle = json.title;
        showPreviewValid(json.code, json.title);
        clearError('err-code');
        setSubmitEnabled(true);
      } else {
        validCode     = false;
        resolvedTitle = '';
        showPreviewInvalid();
        setSubmitEnabled(false);
      }
    } catch (err) {
      validCode = false;
      hidePreview();
      setSubmitEnabled(false);
    }
  }

  function showPreviewValid(code, title) {
    jobPreview.style.display     = 'block';
    jobPreview.style.borderLeft  = '3px solid #16a34a';
    jobPreview.style.background  = '#f0fdf4';
    const badge = document.getElementById('jobPreviewBadge');
    const titleEl = document.getElementById('jobPreviewTitle');
    badge.textContent  = code;
    badge.style.background = '#1e3a5f';
    badge.style.color      = '#fff';
    titleEl.textContent = '✓ ' + title;
    titleEl.style.color = '#15803d';
  }

  function showPreviewInvalid() {
    jobPreview.style.display     = 'block';
    jobPreview.style.borderLeft  = '3px solid #dc2626';
    jobPreview.style.background  = '#fef2f2';
    const badge = document.getElementById('jobPreviewBadge');
    const titleEl = document.getElementById('jobPreviewTitle');
    badge.textContent  = '';
    titleEl.textContent = 'Code not found';
    titleEl.style.color = '#dc2626';
  }

  function hidePreview() {
    jobPreview.style.display = 'none';
  }

  function setSubmitEnabled(on) {
    submitBtn.disabled = !on;
  }

  // ── Form submit ──────────────────────────────
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!validateForm()) return;

    setLoading(true);
    clearFormError();

    const data = {
      candidate_name:  field('candidate_name'),
      candidate_email: field('candidate_email'),
      job_code:        codeInput.value.trim(),
    };

    try {
      const res  = await fetch('api/start.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify(data),
      });
      const json = await res.json();

      if (json.success) {
        window.location.href = 'chat.php';
      } else {
        showFormError(json.error || 'Something went wrong. Please try again.');
        setLoading(false);
      }
    } catch (err) {
      showFormError('Connection error. Please check your internet and try again.');
      setLoading(false);
    }
  });

  function validateForm() {
    let valid = true;
    const nameEl = document.getElementById('candidate_name');

    nameEl.classList.remove('invalid');
    codeInput.classList.remove('invalid');
    clearError('err-name');
    clearError('err-code');

    if (!nameEl.value.trim()) {
      setError('err-name', 'Please enter your full name.');
      nameEl.classList.add('invalid');
      valid = false;
    }
    if (!validCode) {
      setError('err-code', 'Please enter a valid job code.');
      codeInput.classList.add('invalid');
      valid = false;
    }
    return valid;
  }

  function setLoading(on) {
    submitBtn.disabled  = on;
    btnText.textContent = on ? 'Starting…' : 'Start Interview';
    btnSpinner.classList.toggle('hidden', !on);
    btnArrow.classList.toggle('hidden', on);
  }

  function field(id)       { return document.getElementById(id)?.value.trim() || ''; }
  function setError(id, m) { const el = document.getElementById(id); if (el) el.textContent = m; }
  function clearError(id)  { const el = document.getElementById(id); if (el) el.textContent = ''; }
  function showFormError(m){ formError.textContent = m; formError.classList.remove('hidden'); }
  function clearFormError(){ formError.classList.add('hidden'); }

  // ── URL param pre-fill ───────────────────────
  (function checkUrlParam() {
    const params = new URLSearchParams(window.location.search);
    const code   = params.get('code');
    if (code) {
      codeInput.value = code.toUpperCase();
      lookupCode(codeInput.value.trim());
    }
  })();
}

// ══════════════════════════════════════════════
// CHAT PAGE
// ══════════════════════════════════════════════
if (isChatPage) {
  const messagesEl     = document.getElementById('messages');
  const chatBody       = document.getElementById('chatBody');
  const chatInput      = document.getElementById('chatInput');
  const sendBtn        = document.getElementById('sendBtn');
  const typingWrap     = document.getElementById('typingIndicator');
  const inputHint      = document.getElementById('inputHint');
  const reportOverlay  = document.getElementById('reportOverlay');
  const completionEl   = document.getElementById('completionScreen');
  const completionName = document.getElementById('completionName');
  const completionDetail = document.getElementById('completionDetail');

  const candidateName = document.body.dataset.candidate || 'there';
  let interviewStarted = false;

  // ── Auto-resize textarea ─────────────────────
  chatInput.addEventListener('input', () => {
    chatInput.style.height = 'auto';
    chatInput.style.height = Math.min(chatInput.scrollHeight, 150) + 'px';
  });

  // ── Send on Enter ────────────────────────────
  chatInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      if (!sendBtn.disabled) handleSend();
    }
  });

  sendBtn.addEventListener('click', handleSend);

  // ── Start interview on load ──────────────────
  window.addEventListener('load', () => startInterview());

  async function startInterview() {
    if (interviewStarted) return;
    interviewStarted = true;
    showTyping();
    try {
      await sendMessage(null, true);
    } catch (err) {
      hideTyping();
      appendMessage('Sorry, there was an error starting the interview. Please refresh the page.', 'ai');
    }
  }

  async function handleSend() {
    const text = chatInput.value.trim();
    if (!text) return;

    chatInput.value = '';
    chatInput.style.height = 'auto';
    appendMessage(text, 'user');
    setInputEnabled(false);
    showTyping();

    try {
      await sendMessage(text, false);
    } catch (err) {
      hideTyping();
      // Auto-recover: session is preserved, reload restores last state
      inputHint.textContent = 'Reconnecting…';
      await sleep(1500);
      window.location.reload();
    }
  }

  // ── Core message function — with auto-retry ───────────────────────────────
  async function sendMessage(text, isStart, attempt = 0) {
    const payload  = isStart ? { start: true } : { message: text };

    let response;
    try {
      response = await fetch('api/message.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify(payload),
      });
    } catch (networkErr) {
      if (attempt < 2) {
        inputHint.textContent = `Reconnecting… (attempt ${attempt + 2}/3)`;
        await sleep(1500 * (attempt + 1));
        return sendMessage(text, isStart, attempt + 1);
      }
      throw networkErr;
    }

    if (!response.ok) throw new Error('HTTP ' + response.status);

    const data = await response.json();
    hideTyping();

    if (data.error) {
      appendMessage(data.error, 'ai');
      setInputEnabled(true);
      return;
    }

    // Strip the completion marker before displaying
    const cleanText = data.message.replace('[INTERVIEW_COMPLETE]', '').trimEnd();
    await typeMessage(cleanText);

    if (data.interview_done) {
      showCountdownAndReport();
    } else {
      setInputEnabled(true);
      chatInput.focus();
    }
  }

  // ── Countdown then auto-generate report ──────────────────────────
  async function showCountdownAndReport() {
    setInputEnabled(false);

    const row = document.createElement('div');
    row.className = 'message-row';
    row.style.cssText = 'justify-content:center;padding:20px 0 12px;';
    const inner = document.createElement('p');
    inner.style.cssText = 'color:#64748b;font-size:13px;text-align:center;margin:0;';
    row.appendChild(inner);
    messagesEl.appendChild(row);
    scrollBottom();

    for (let i = 8; i >= 1; i--) {
      inner.textContent = `Interview complete — generating your report in ${i} second${i !== 1 ? 's' : ''}…`;
      await sleep(1000);
    }
    inner.textContent = 'Generating your report…';

    await triggerReport();
  }

  // ── Typewriter effect ────────────────────────
  async function typeMessage(text) {
    const msgEl = createMessageEl('ai');
    let displayed = '';
    const chars = [...text]; // handle multi-byte chars correctly

    for (let i = 0; i < chars.length; i++) {
      displayed += chars[i];
      updateMessageEl(msgEl, displayed);
      if (i % 3 === 0) {
        scrollBottom();
        await sleep(12);
      }
    }
    scrollBottom();
  }

  // ── Report generation (with retry for connection resets) ────────
  async function triggerReport(attempt = 0) {
    setInputEnabled(false);
    reportOverlay.classList.remove('hidden');

    try {
      const res  = await fetch('api/report.php', { method: 'POST' });
      const data = await res.json();
      reportOverlay.classList.add('hidden');

      if (data.success) {
        showCompletion(data.email_sent);
      } else {
        appendMessage('There was an issue generating your report. Your responses have been recorded.', 'ai');
      }
    } catch (err) {
      if (attempt < 4) {
        // Connection reset — wait and retry (same pattern as sendMessage)
        await sleep(1500 * (attempt + 1));
        return triggerReport(attempt + 1);
      }
      reportOverlay.classList.add('hidden');
      appendMessage('There was a connection error. Your interview responses have been saved.', 'ai');
    }
  }

  // ── Completion screen ────────────────────────
  function showCompletion(emailSent) {
    completionName.textContent = candidateName;
    completionDetail.textContent = emailSent
      ? '📧 A report has been sent to the Kuma Talent team.'
      : '✓ Your responses have been recorded.';
    completionEl.classList.remove('hidden');
  }

  // ── UI helpers ───────────────────────────────
  function appendMessage(text, role) {
    const el = createMessageEl(role);
    updateMessageEl(el, text);
    scrollBottom();
    return el;
  }

  function createMessageEl(role) {
    const row = document.createElement('div');
    row.className = `message-row ${role}`;
    if (role === 'ai') {
      const avatar = document.createElement('div');
      avatar.className = 'avatar';
      avatar.textContent = 'K';
      row.appendChild(avatar);
    }
    const bubble = document.createElement('div');
    bubble.className = `message ${role}`;
    row.appendChild(bubble);
    messagesEl.appendChild(row);
    return bubble;
  }

  function updateMessageEl(el, text) {
    el.innerHTML = escapeHtml(text)
      .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
      .replace(/\n/g, '<br>');
  }

  function escapeHtml(str) {
    return str
      .replace(/&/g, '&amp;').replace(/</g, '&lt;')
      .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  function showTyping()  { typingWrap.classList.remove('hidden'); scrollBottom(); }
  function hideTyping()  { typingWrap.classList.add('hidden'); }

  function setInputEnabled(on) {
    chatInput.disabled = !on;
    sendBtn.disabled   = !on;
    inputHint.textContent = on
      ? 'Press Enter to send  ·  Shift+Enter for new line'
      : 'Please wait…';
  }

  function scrollBottom() {
    requestAnimationFrame(() => { chatBody.scrollTop = chatBody.scrollHeight; });
  }

  function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }
}
