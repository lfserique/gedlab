<?php
require_once __DIR__ . '/functions.php';

function register_user($fullName, $username, $email, $password, $role = 'analyst') {
    $pdo = db();

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users (full_name, username, email, password_hash, role)
        VALUES (?, ?, ?, ?, ?)
    ");
    return $stmt->execute([$fullName, $username, $email, $hash, $role]);
}

function login_user($username, $password): bool {
    $pdo = db();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user) {
        return false;
    }

    if (password_verify($password, $user['password_hash'])) {
        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $upd = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $upd->execute([$newHash, $user['id']]);
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'full_name' => $user['full_name'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        return true;
    }

    return false;
}

function logout_user() {
    session_destroy();
}
?>