CREATE DATABASE IF NOT EXISTS gedlab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'geduser'@'localhost' IDENTIFIED BY 'Z9#vL2!qR7@pX4$mT8&c';
GRANT ALL PRIVILEGES ON gedlab.* TO 'geduser'@'localhost';
FLUSH PRIVILEGES;

USE gedlab;

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    full_name VARCHAR(150) NOT NULL,
    username VARCHAR(80) NOT NULL,
    email VARCHAR(150) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'analyst', 'auditor') NOT NULL DEFAULT 'analyst',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS documents (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    original_filename VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    sha256_hash CHAR(64) NOT NULL,
    is_confidential TINYINT(1) NOT NULL DEFAULT 0,
    encrypted_blob LONGBLOB NULL,
    plain_blob LONGBLOB NULL,
    iv VARBINARY(16) NULL,
    auth_tag VARBINARY(16) NULL,
    uploaded_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_documents_uploaded_by (uploaded_by),
    KEY idx_documents_sha256_hash (sha256_hash),
    CONSTRAINT fk_documents_uploaded_by
        FOREIGN KEY (uploaded_by) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS document_signatures (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    document_id BIGINT UNSIGNED NOT NULL,
    signed_by_user_id BIGINT UNSIGNED NOT NULL,
    signer_common_name VARCHAR(255) NOT NULL,
    signer_serial VARCHAR(255) NULL,
    signer_subject JSON NULL,
    signer_issuer JSON NULL,
    cert_pem LONGTEXT NOT NULL,
    signature_blob LONGBLOB NOT NULL,
    signed_hash CHAR(64) NOT NULL,
    signed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_signatures_document_id (document_id),
    KEY idx_signatures_user_id (signed_by_user_id),
    CONSTRAINT fk_signatures_document
        FOREIGN KEY (document_id) REFERENCES documents(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_signatures_user
        FOREIGN KEY (signed_by_user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_chain (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    event_time DATETIME NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    event_type VARCHAR(100) NOT NULL,
    document_id BIGINT UNSIGNED NULL,
    event_data JSON NULL,
    previous_hash CHAR(64) NULL,
    current_hash CHAR(64) NOT NULL,
    nonce INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    KEY idx_audit_user_id (user_id),
    KEY idx_audit_document_id (document_id),
    KEY idx_audit_event_time (event_time),
    UNIQUE KEY uq_audit_current_hash (current_hash),
    CONSTRAINT fk_audit_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_audit_document
        FOREIGN KEY (document_id) REFERENCES documents(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
