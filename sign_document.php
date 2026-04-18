<?php
require_once 'functions.php';
require_once 'crypto.php';
require_once 'blockchain.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare("SELECT * FROM documents WHERE id = ?");
$stmt->execute([$id]);
$doc = $stmt->fetch();

if (!$doc) {
    die('Documento não encontrado.');
}

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_FILES['p12file']) || $_FILES['p12file']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Selecione o certificado .p12');
        }

        $allowedExt = ['p12', 'pfx'];
        $originalP12Name = $_FILES['p12file']['name'] ?? '';
        $ext = strtolower(pathinfo($originalP12Name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) {
            throw new Exception('Formato inválido. Envie apenas .p12 ou .pfx');
        }

        if ($_FILES['p12file']['size'] > 5 * 1024 * 1024) {
            throw new Exception('Arquivo .p12 muito grande.');
        }

        $p12Password = $_POST['p12_password'] ?? '';
        if ($p12Password === '') {
            throw new Exception('Informe a senha do certificado.');
        }

        $p12Content = file_get_contents($_FILES['p12file']['tmp_name']);
        if ($p12Content === false) {
            throw new Exception('Falha ao ler o arquivo do certificado.');
        }

        $certs = [];
        if (!openssl_pkcs12_read($p12Content, $certs, $p12Password)) {
            throw new Exception('Certificado inválido ou senha incorreta.');
        }

        if ($doc['is_confidential']) {
            $documentContent = decrypt_document_content($doc['encrypted_blob'], $doc['iv'], $doc['auth_tag']);
        } else {
            $documentContent = $doc['plain_blob'];
        }

        $hashBin = hash('sha256', $documentContent, true);
        $hashHex = hash('sha256', $documentContent);

        $signature = '';
        $ok = openssl_sign($hashBin, $signature, $certs['pkey'], OPENSSL_ALGO_SHA256);
        if (!$ok) {
            throw new Exception('Falha ao assinar o documento.');
        }

        $certInfo = openssl_x509_parse($certs['cert']);
        $signerCN = $certInfo['subject']['CN'] ?? 'N/D';
        $serial = $certInfo['serialNumberHex'] ?? ($certInfo['serialNumber'] ?? 'N/D');
        $subject = json_encode($certInfo['subject'] ?? [], JSON_UNESCAPED_UNICODE);
        $issuer = json_encode($certInfo['issuer'] ?? [], JSON_UNESCAPED_UNICODE);

        $insert = db()->prepare("
            INSERT INTO document_signatures
            (document_id, signed_by_user_id, signer_common_name, signer_serial, signer_subject, signer_issuer, cert_pem, signature_blob, signed_hash)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $insert->execute([
            $doc['id'],
            current_user()['id'],
            $signerCN,
            $serial,
            $subject,
            $issuer,
            $certs['cert'],
            $signature,
            $hashHex
        ]);

        add_audit_event(current_user()['id'], 'SIGN_DOCUMENT', $doc['id'], [
            'signer_cn' => $signerCN,
            'serial' => $serial
        ]);

        unset($p12Content, $p12Password, $certs, $signature, $documentContent, $hashBin);

        $msg = 'Documento assinado com sucesso.';
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
    <title>Assinar documento - GEDLab</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/app.css">
</head>
<body>
<main class="app-shell stack">
    <section class="topbar">
        <div>
            <h1 class="page-title">Assinar documento</h1>
            <p class="page-subtitle"><strong>Documento:</strong> <?= e($doc['title']) ?></p>
        </div>
        <div class="actions">
            <a class="btn btn-secondary" href="dashboard.php">Voltar ao painel</a>
        </div>
    </section>

    <section class="card">
        <?php if ($msg): ?><p class="alert alert-success"><?= e($msg) ?></p><?php endif; ?>
        <?php if ($err): ?><p class="alert alert-error"><?= e($err) ?></p><?php endif; ?>

        <form method="post" enctype="multipart/form-data" autocomplete="off" class="stack">
            <div class="field">
                <label for="p12file">Arquivo do certificado (.p12/.pfx)</label>
                <input id="p12file" type="file" name="p12file" accept=".p12,.pfx" required>
            </div>

            <div class="field">
                <label for="p12_password">Senha do certificado</label>
                <input id="p12_password" type="password" name="p12_password" required>
            </div>

            <div>
                <button type="submit">Assinar documento</button>
            </div>
        </form>
    </section>
</main>
</body>
</html>