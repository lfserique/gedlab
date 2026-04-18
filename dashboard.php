<?php
require_once 'functions.php';
require_once 'db.php';
require_once 'blockchain.php';
require_login();

$docs = db()->query("
    SELECT 
        d.*,
        u.username,
        COALESCE(ds.signature_count, 0) AS signature_count
    FROM documents d
    JOIN users u ON u.id = d.uploaded_by
    LEFT JOIN (
        SELECT document_id, COUNT(*) AS signature_count
        FROM document_signatures
        GROUP BY document_id
    ) ds ON ds.document_id = d.id
    ORDER BY d.id DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Painel - GEDLab</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/app.css">
</head>
<body>
<main class="app-shell stack">
    <section class="topbar">
        <div>
            <h1 class="page-title">Painel GEDLab</h1>
            <p class="page-subtitle">Usuario autenticado: <strong><?= e(current_user()['full_name']) ?></strong> (<?= e(current_user()['role']) ?>)</p>
        </div>
        <div class="actions">
            <a class="btn btn-secondary" href="upload.php">Enviar documento</a>
            <a class="btn btn-secondary" href="audit.php">Ver auditoria</a>
            <a class="btn btn-danger" href="logout.php">Sair</a>
        </div>
    </section>

    <section class="card">
        <div class="table-wrap">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Status</th>
                    <th>Titulo</th>
                    <th>Arquivo</th>
                    <th>Sigiloso</th>
                    <th>Assinado</th>
                    <th>Hash SHA-256</th>
                    <th>Autor</th>
                    <th>Acoes</th>
                </tr>
                <?php foreach ($docs as $doc): ?>
                <?php
                    $cadeado = (int)$doc['is_confidential'] === 1 ? '🔒' : '';
                    $caneta = (int)$doc['signature_count'] > 0 ? '🖊️' : '';
                ?>
                <tr>
                    <td><?= e($doc['id']) ?></td>
                    <td><?= trim($cadeado . ' ' . $caneta) ?></td>
                    <td><?= e($doc['title']) ?></td>
                    <td><?= e($doc['original_filename']) ?></td>
                    <td><?= $doc['is_confidential'] ? 'Sim' : 'Nao' ?></td>
                    <td><?= ((int)$doc['signature_count'] > 0) ? 'Sim' : 'Nao' ?></td>
                    <td><code><?= e($doc['sha256_hash']) ?></code></td>
                    <td><?= e($doc['username']) ?></td>
                    <td>
                        <a class="muted-link" href="view_document.php?id=<?= $doc['id'] ?>">Visualizar</a> |
                        <a class="muted-link" href="sign_document.php?id=<?= $doc['id'] ?>">Assinar</a> |
                        <a class="muted-link" href="verify_signature.php?id=<?= $doc['id'] ?>">Verificar assinatura</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </section>
</main>
</body>
</html>