<?php
require_once 'functions.php';
require_once 'blockchain.php';
require_login();

$rows = db()->query("
    SELECT
        a.*,
        u.full_name AS user_full_name,
        u.username AS user_username
    FROM audit_chain a
    LEFT JOIN users u ON u.id = a.user_id
    ORDER BY a.id ASC
")->fetchAll();
$check = verify_audit_chain();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Auditoria - GEDLab</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/app.css">
</head>
<body>
<main class="app-shell stack">
    <section class="topbar">
        <div>
            <h1 class="page-title">Auditoria em blockchain</h1>
            <p class="page-subtitle">Rastreabilidade de eventos e verificacao da cadeia de blocos.</p>
        </div>
        <div class="actions">
            <a class="btn btn-secondary" href="dashboard.php">Voltar ao painel</a>
        </div>
    </section>

    <section class="card stack">
        <p class="chip">
            <span class="status-dot <?= $check['valid'] ? 'status-ok' : 'status-bad' ?>"></span>
            Status da cadeia: <strong><?= $check['valid'] ? 'INTEGRA' : 'CORROMPIDA' ?></strong>
        </p>

        <?php if (!$check['valid'] && !empty($check['error'])): ?>
        <p class="alert alert-error"><strong>Detalhe:</strong> <?= e($check['error']) ?></p>
        <?php endif; ?>

        <div class="table-wrap">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Data/Hora</th>
                    <th>User</th>
                    <th>Nome</th>
                    <th>Evento</th>
                    <th>Doc</th>
                    <th>Prev Hash</th>
                    <th>Hash Atual</th>
                    <th>Nonce</th>
                </tr>
                <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= e($r['id']) ?></td>
                    <td><?= e($r['event_time']) ?></td>
                    <td><?= e($r['user_id']) ?></td>
                    <td><?= e($r['user_full_name'] ?: ($r['user_username'] ?: 'Sistema')) ?></td>
                    <td><?= e($r['event_type']) ?></td>
                    <td><?= e($r['document_id']) ?></td>
                    <td><code><?= e($r['previous_hash']) ?></code></td>
                    <td><code><?= e($r['current_hash']) ?></code></td>
                    <td><?= e($r['nonce']) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </section>
</main>
</body>
</html>