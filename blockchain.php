<?php
require_once __DIR__ . '/db.php';

function sort_keys_recursively($value) {
    if (!is_array($value)) {
        return $value;
    }

    $isAssoc = array_keys($value) !== range(0, count($value) - 1);
    if ($isAssoc) {
        ksort($value);
    }

    foreach ($value as $k => $v) {
        $value[$k] = sort_keys_recursively($v);
    }

    return $value;
}

function normalize_event_data_for_hash($eventData): ?string {
    if ($eventData === null || $eventData === '') {
        return null;
    }

    if (is_array($eventData)) {
        $sorted = sort_keys_recursively($eventData);
        return json_encode($sorted, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    if (is_object($eventData)) {
        $arr = json_decode(json_encode($eventData), true);
        $sorted = sort_keys_recursively($arr);
        return json_encode($sorted, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    if (is_string($eventData)) {
        $decoded = json_decode($eventData, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $sorted = sort_keys_recursively($decoded);
            return json_encode($sorted, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return trim($eventData) === '' ? null : trim($eventData);
    }

    return (string)$eventData;
}

function get_last_chain_hash(): ?string {
    $pdo = db();
    $stmt = $pdo->query("SELECT current_hash FROM audit_chain ORDER BY id DESC LIMIT 1");
    $row = $stmt->fetch();
    return $row ? $row['current_hash'] : null;
}

function calculate_block_hash($eventTime, $userId, $eventType, $documentId, $eventDataJson, $previousHash, $nonce): string {
    $payload = implode('|', [
        $eventTime,
        $userId ?? '',
        $eventType,
        $documentId ?? '',
        $eventDataJson ?? '',
        $previousHash ?? '',
        $nonce
    ]);

    return hash('sha256', $payload);
}

function add_audit_event($userId, $eventType, $documentId = null, $eventData = null) {
    $pdo = db();
    $previousHash = get_last_chain_hash();
    $eventTime = date('Y-m-d H:i:s');
    $eventDataJson = normalize_event_data_for_hash($eventData);

    $nonce = 0;
    do {
        $currentHash = calculate_block_hash($eventTime, $userId, $eventType, $documentId, $eventDataJson, $previousHash, $nonce);
        $nonce++;
    } while (substr($currentHash, 0, 4) !== '0000');

    $stmt = $pdo->prepare("
        INSERT INTO audit_chain (event_time, user_id, event_type, document_id, event_data, previous_hash, current_hash, nonce)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $eventTime,
        $userId,
        $eventType,
        $documentId,
        $eventDataJson,
        $previousHash,
        $currentHash,
        $nonce - 1
    ]);
}

function verify_audit_chain(): array {
    $pdo = db();
    $rows = $pdo->query("SELECT * FROM audit_chain ORDER BY id ASC")->fetchAll();

    $previous = null;
    foreach ($rows as $row) {
        $eventDataForHash = normalize_event_data_for_hash($row['event_data']);
        $rowPreviousHash = $row['previous_hash'] ?? '';
        $expectedPrevious = $previous ?? '';

        $expected = calculate_block_hash(
            $row['event_time'],
            $row['user_id'],
            $row['event_type'],
            $row['document_id'],
            $eventDataForHash,
            $rowPreviousHash,
            $row['nonce']
        );

        if ($rowPreviousHash !== $expectedPrevious) {
            return ['valid' => false, 'error' => 'Encadeamento inválido no bloco ID ' . $row['id']];
        }

        if ($row['current_hash'] !== $expected) {
            return ['valid' => false, 'error' => 'Hash inválido no bloco ID ' . $row['id']];
        }

        $previous = $row['current_hash'];
    }

    return ['valid' => true, 'count' => count($rows)];
}

function rebuild_audit_chain(): array {
    $pdo = db();
    $rows = $pdo->query("SELECT * FROM audit_chain ORDER BY id ASC")->fetchAll();

    $previous = null;
    $updated = 0;

    $stmt = $pdo->prepare("UPDATE audit_chain SET previous_hash = ?, current_hash = ? WHERE id = ?");

    foreach ($rows as $row) {
        $eventDataForHash = normalize_event_data_for_hash($row['event_data']);
        $normalizedPrevious = $previous ?? '';

        $newCurrent = calculate_block_hash(
            $row['event_time'],
            $row['user_id'],
            $row['event_type'],
            $row['document_id'],
            $eventDataForHash,
            $normalizedPrevious,
            $row['nonce']
        );

        $dbPrevious = $normalizedPrevious === '' ? null : $normalizedPrevious;

        $stmt->execute([$dbPrevious, $newCurrent, $row['id']]);
        $previous = $newCurrent;
        $updated++;
    }

    return ['updated' => $updated, 'last_hash' => $previous];
}
?>