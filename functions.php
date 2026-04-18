<?php
require_once __DIR__ . '/db.php';

function e($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function require_login() {
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}

function redirect($url) {
    header("Location: $url");
    exit;
}
?>