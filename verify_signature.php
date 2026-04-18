<?php
require_once 'functions.php';
require_once 'crypto.php';
require_login();

$id = (int)($_GET['id'] ?? 0);

$stmt = db()->prepare("SELECT * FROM documents WHERE id = ?");
$stmt->execute([$id]);
$doc = $stmt->fetch();
if (!$doc) {
    die('Documento não encontrado.');
}

$sigStmt = db()->prepare("SELECT * FROM document_signatures WHERE document_id = ? ORDER BY id DESC");
$sigStmt->execute([$id]);
$sigs = $sigStmt->fetchAll();

if ($doc['is_confidential']) {
    $content = decrypt_document_content($doc['encrypted_blob'], $doc['iv'], $doc['auth_tag']);
} else {
    $content = $doc['plain_blob'];
}

$hashBin = hash('sha256', $content, true);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificacao de Assinaturas - GEDLab</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/app.css">
</head>
<body>
<main class="app-shell stack">
    <section class="topbar">
        <div>
            <h1 class="page-title">Verificacao de assinaturas</h1>
            <p class="page-subtitle"><strong>Documento:</strong> <?= e($doc['title']) ?></p>
        </div>
        <div class="actions">
            <a class="btn btn-secondary" href="dashboard.php">Voltar ao painel</a>
        </div>
    </section>

    <section class="card">
        <div class="table-wrap">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Assinante</th>
                    <th>Serie</th>
                    <th>Data</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($sigs as $sig): ?>
                <?php
                    $pubKey = openssl_pkey_get_public($sig['cert_pem']);
                    $verify = openssl_verify($hashBin, $sig['signature_blob'], $pubKey, OPENSSL_ALGO_SHA256);
                    $status = $verify === 1 ? 'Valida' : 'Invalida';
                ?>
                <tr>
                    <td><?= e($sig['id']) ?></td>
                    <td><?= e($sig['signer_common_name']) ?></td>
                    <td><?= e($sig['signer_serial']) ?></td>
                    <td><?= e($sig['signed_at']) ?></td>
                    <td>
                        <span class="status-dot <?= $verify === 1 ? 'status-ok' : 'status-bad' ?>"></span>
                        <?= e($status) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </section>
</main>
</body>
</html>