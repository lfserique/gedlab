<?php
require_once __DIR__ . '/config.php';

function encrypt_document_content(string $plaintext): array {
    $iv = random_bytes(12);
    $tag = '';
    $ciphertext = openssl_encrypt(
        $plaintext,
        'aes-256-gcm',
        DOC_ENCRYPTION_KEY,
        OPENSSL_RAW_DATA,
        $iv,
        $tag
    );

    if ($ciphertext === false) {
        throw new Exception('Falha ao criptografar documento.');
    }

    return [
        'ciphertext' => $ciphertext,
        'iv' => $iv,
        'tag' => $tag
    ];
}

function decrypt_document_content(string $ciphertext, string $iv, string $tag): string {
    $plaintext = openssl_decrypt(
        $ciphertext,
        'aes-256-gcm',
        DOC_ENCRYPTION_KEY,
        OPENSSL_RAW_DATA,
        $iv,
        $tag
    );

    if ($plaintext === false) {
        throw new Exception('Falha ao descriptografar documento.');
    }

    return $plaintext;
}
?>