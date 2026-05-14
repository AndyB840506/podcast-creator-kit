<?php
use PHPMailer\PHPMailer\PHPMailer;

require_once 'auth.php';

$autoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoload)) {
    $_SESSION['flash'] = 'Cannot send test email: vendor/autoload.php not found. Run composer install.';
    header('Location: settings.php'); exit;
}
require_once $autoload;

if (!SMTP_USER || !RECRUITER_EMAIL) {
    $_SESSION['flash'] = 'Cannot send test email: SMTP user or recruiter email not configured. Save settings first.';
    header('Location: settings.php'); exit;
}

try {
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
    $mail->addAddress(RECRUITER_EMAIL);
    $mail->Subject = APP_NAME . ' — SMTP Test';
    $mail->isHTML(true);
    $mail->Body = '<p style="font-family:Arial,sans-serif">SMTP is configured correctly.<br>Reports will be delivered to this address.</p>';
    $mail->AltBody = 'SMTP is configured correctly. Reports will be delivered to this address.';
    $mail->send();
    $_SESSION['flash'] = 'Test email sent to ' . RECRUITER_EMAIL . '. Check your inbox.';
} catch (\Exception $e) {
    $_SESSION['flash'] = 'Test email failed: ' . $e->getMessage();
}

header('Location: settings.php'); exit;
