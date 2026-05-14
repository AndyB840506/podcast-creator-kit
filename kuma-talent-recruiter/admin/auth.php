<?php
require_once __DIR__ . '/../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_auth'])) {
    header('Location: login.php');
    exit;
}
