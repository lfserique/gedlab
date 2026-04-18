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

try {
    if ($doc['is_confidential']) {
        $content = decrypt_document_content($doc['encrypted_blob'], $doc['iv'], $doc['auth_tag']);
    } else {
        $content = $doc['plain_blob'];
    }

    add_audit_event(current_user()['id'], 'VIEW_DOCUMENT', $doc['id'], [
        'title' => $doc['title']
    ]);

    header('Content-Type: ' . $doc['mime_type']);
    header('Content-Disposition: inline; filename="' . $doc['original_filename'] . '"');
    echo $content;
    exit;
} catch (Throwable $e) {
    die('Erro ao abrir documento: ' . $e->getMessage());
}