<?php
require_once 'blockchain.php';
require_once 'auth.php';

if (isset($_SESSION['user']['id'])) {
    add_audit_event($_SESSION['user']['id'], 'LOGOUT', null, []);
}
logout_user();
header('Location: login.php');
exit;