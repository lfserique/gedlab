<?php
require_once 'functions.php';
require_once 'crypto.php';
require_once 'blockchain.php';
require_login();

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $isConfidential = isset($_POST['is_confidential']) ? 1 : 0;

        if (!$title || !isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Preencha os campos corretamente e selecione um arquivo.');
        }

        $tmp = $_FILES['document']['tmp_name'];
        $originalName = basename($_FILES['document']['name']);
        $mimeType = 'application/octet-stream';
        if (function_exists('mime_content_type')) {
            $detectedMime = mime_content_type($tmp);
            if (is_string($detectedMime) && $detectedMime !== '') {
                $mimeType = $detectedMime;
            }
        } elseif (class_exists('finfo')) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $detectedMime = $finfo->file($tmp);
            if (is_string($detectedMime) && $detectedMime !== '') {
                $mimeType = $detectedMime;
            }
        } elseif (!empty($_FILES['document']['type'])) {
            $mimeType = (string) $_FILES['document']['type'];
        }
        $content = file_get_contents($tmp);
        $sha256 = hash('sha256', $content);

        $pdo = db();

        if ($isConfidential) {
            $enc = encrypt_document_content($content);

            $stmt = $pdo->prepare("
                INSERT INTO documents
                (title, description, original_filename, mime_type, sha256_hash, is_confidential, encrypted_blob, iv, auth_tag, uploaded_by)
                VALUES (?, ?, ?, ?, ?, 1, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $title,
                $description,
                $originalName,
                $mimeType,
                $sha256,
                $enc['ciphertext'],
                $enc['iv'],
                $enc['tag'],
                current_user()['id']
            ]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO documents
                (title, description, original_filename, mime_type, sha256_hash, is_confidential, plain_blob, uploaded_by)
                VALUES (?, ?, ?, ?, ?, 0, ?, ?)
            ");
            $stmt->execute([
                $title,
                $description,
                $originalName,
                $mimeType,
                $sha256,
                $content,
                current_user()['id']
            ]);
        }

        $docId = $pdo->lastInsertId();

        add_audit_event(current_user()['id'], 'UPLOAD_DOCUMENT', $docId, [
            'title' => $title,
            'confidential' => $isConfidential,
            'filename' => $originalName
        ]);

        $msg = 'Documento enviado com sucesso.';
    } catch (Throwable $e) {
        $err = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Upload - GEDLab</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/app.css">
</head>
<body>
<main class="app-shell stack">
    <section class="topbar">
        <div>
            <h1 class="page-title">Enviar documento</h1>
            <p class="page-subtitle">Envie o arquivo e defina se o conteudo deve ser protegido.</p>
        </div>
        <div class="actions">
            <a class="btn btn-secondary" href="dashboard.php">Voltar ao painel</a>
        </div>
    </section>

    <section class="card">
        <?php if ($msg): ?><p class="alert alert-success"><?= e($msg) ?></p><?php endif; ?>
        <?php if ($err): ?><p class="alert alert-error"><?= e($err) ?></p><?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="stack">
            <div class="field">
                <label for="title">Titulo</label>
                <input id="title" type="text" name="title" required>
            </div>

            <div class="field">
                <label for="description">Descricao</label>
                <textarea id="description" name="description"></textarea>
            </div>

            <div class="field">
                <label for="document">Arquivo</label>
                <input id="document" type="file" name="document" required>
            </div>

            <label class="field-inline">
                <input type="checkbox" name="is_confidential" value="1" style="width:auto;">
                Documento sigiloso (criptografar)
            </label>

            <div>
                <button type="submit">Enviar documento</button>
            </div>
        </form>
    </section>
</main>
</body>
</html>