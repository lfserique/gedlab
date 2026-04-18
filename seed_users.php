<?php
require_once 'auth.php';

try {
    register_user('Administrador do Sistema', 'admin', 'admin@gedlab.local', 'admin123', 'admin');
    register_user('Analista de Documentos', 'analista', 'analista@gedlab.local', 'analista123', 'analyst');
    register_user('Auditor Interno', 'auditor', 'auditor@gedlab.local', 'auditor123', 'auditor');
    echo "Usuários criados com sucesso.\n";
} catch (Throwable $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}