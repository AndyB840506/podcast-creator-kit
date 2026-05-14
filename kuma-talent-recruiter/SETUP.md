# Kuma Talent — Smart Recruiter Setup Guide

## Prerequisites
- Hostinger Premium or Cloud account
- Domain/subdomain configured (e.g. `screening.kumatalent.com`)
- SSH access enabled in Hostinger panel
- Anthropic API key ([console.anthropic.com](https://console.anthropic.com))

---

## Step 1 — Upload files

Upload the entire `kuma-talent-recruiter/` folder to your Hostinger hosting.

**Option A — File Manager:**  
Hostinger Panel → File Manager → `public_html/` → Upload

**Option B — FTP (FileZilla):**  
Host: your domain | Port: 21 | Credentials from Hostinger panel

Recommended subdomain setup:  
Create `screening.kumatalent.com` → points to `public_html/screening/`

---

## Step 2 — Install PHP dependencies (via SSH)

```bash
# Connect via SSH (credentials in Hostinger panel)
ssh your-user@your-server-ip

# Navigate to your project folder
cd ~/public_html/screening

# Install PHPMailer
composer install --no-dev --optimize-autoloader
```

If Composer is not installed:
```bash
curl -sS https://getcomposer.org/installer | php
php composer.phar install --no-dev
```

---

## Step 3 — Configure credentials

Edit `config.php` and replace the placeholder values:

```php
// Anthropic API key (from console.anthropic.com → API Keys)
define('ANTHROPIC_API_KEY', 'sk-ant-XXXXXXXX-your-actual-key');

// Your Hostinger email credentials
define('SMTP_USER',       'hello@kumatalent.com');
define('SMTP_PASS',       'your-email-account-password');
define('RECRUITER_EMAIL', 'hello@kumatalent.com');
```

> **Security**: `config.php` is protected from direct browser access via `.htaccess`. Never commit it to a public git repo.

---

## Step 4 — Set directory permissions

Via SSH:
```bash
chmod 775 reports/
chmod 644 config.php
```

Or via Hostinger File Manager → right-click → Permissions.

---

## Step 5 — Test the app

1. Open your domain in a browser → you should see the Kuma Talent form
2. Fill in the form with a test candidate and a sample job description
3. Complete a short interview
4. Verify that:
   - The chat streams responses in real time
   - The report overlay appears after the interview ends
   - You receive an email at `hello@kumatalent.com` with the report
   - A `.md` file is saved in the `reports/` folder

---

## Troubleshooting

**Streaming not working (blank or delayed chat)**
- In Hostinger panel: go to PHP Configuration → set `output_buffering = Off`
- Confirm `X-Accel-Buffering: no` is being sent (check browser DevTools → Network → Headers)

**Email not being received**
- Confirm SMTP credentials are correct (`SMTP_USER` must match the email account exactly)
- Check spam/junk folder
- In Hostinger, verify the email account exists: Emails → Email Accounts

**Session expired error on chat.php**
- PHP session may have been cleared. Make sure the candidate completes the form and proceeds directly to the interview without refreshing.

**Composer not found on server**
- Download PHPMailer manually: [github.com/PHPMailer/PHPMailer](https://github.com/PHPMailer/PHPMailer/releases)
- Copy `src/` folder to `vendor/phpmailer/phpmailer/src/`
- Create `vendor/autoload.php` with the manual require:
```php
<?php
require __DIR__ . '/phpmailer/phpmailer/src/Exception.php';
require __DIR__ . '/phpmailer/phpmailer/src/PHPMailer.php';
require __DIR__ . '/phpmailer/phpmailer/src/SMTP.php';
```

---

## How it works

```
Candidate opens the site
        ↓
Fills the form (name, email, job title, job description, language)
        ↓
api/start.php — saves config to PHP session
        ↓
chat.php loads — JS auto-sends "start" trigger
        ↓
api/message.php — streams Claude's responses in real time
        ↓
        ... 10–14 exchanges ...
        ↓
Claude appends [INTERVIEW_COMPLETE] signal
        ↓
JS detects signal → calls api/report.php
        ↓
api/report.php — generates report via Claude → sends email → saves .md file
        ↓
Candidate sees completion screen
```

---

## Costs

| Item | Estimate |
|---|---|
| Anthropic API per interview | ~$0.03–0.08 USD |
| Hosting (Hostinger Premium) | Already paying |
| Email (Hostinger SMTP) | Included |

---

## Files overview

| File | Purpose |
|---|---|
| `index.php` | Landing page + candidate form |
| `chat.php` | Interview chat interface |
| `config.php` | API keys, SMTP config, prompt builders |
| `api/start.php` | Initialize interview session |
| `api/message.php` | Stream Claude responses (SSE) |
| `api/report.php` | Generate report + send email |
| `assets/css/style.css` | All styles |
| `assets/js/app.js` | All client-side JavaScript |
| `reports/` | Backup markdown reports saved here |
| `.htaccess` | Security + Nginx streaming config |
