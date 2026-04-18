<?php
require_once 'auth.php';
require_once 'blockchain.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (login_user($username, $password)) {
        add_audit_event($_SESSION['user']['id'], 'LOGIN', null, ['username' => $username]);
        redirect('dashboard.php');
    } else {
        $error = 'Usuário ou senha inválidos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login - GEDLab</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/app.css">
</head>
<body>
<main class="login-shell">
    <section class="card login-card stack">
        <header>
            <h1 class="page-title">GEDLab</h1>
            <p class="page-subtitle">Acesse o ambiente seguro de gestao eletronica de documentos.</p>
        </header>

        <?php if ($error): ?><p class="alert alert-error"><?= e($error) ?></p><?php endif; ?>

        <form method="post" class="stack">
            <div class="field">
                <label for="username">Usuario</label>
                <input id="username" type="text" name="username" required>
            </div>

            <div class="field">
                <label for="password">Senha</label>
                <input id="password" type="password" name="password" required>
            </div>

            <button type="submit">Entrar no painel</button>
        </form>
    </section>
</main>
</body>
</html>