<?php
require_once __DIR__ . '/config.php';

$method = getMethod();

// --- GET: public ---
if ($method === 'GET') {
    $db = getDB();
    $rows = $db->query("SELECT section_key, data FROM content")->fetchAll();

    $result = [];
    foreach ($rows as $row) {
        $result[$row['section_key']] = json_decode($row['data'], true);
    }

    jsonResponse($result);
}

// --- PUT: admin update section ---
if ($method === 'PUT') {
    requireAuth();
    $body = getRequestBody();

    $db = getDB();
    $stmt = $db->prepare("INSERT INTO content (section_key, data)
                          VALUES (?, ?)
                          ON DUPLICATE KEY UPDATE data = VALUES(data)");

    foreach (['sluzby', 'proc_za_mnou', 'o_mne'] as $key) {
        if (isset($body[$key])) {
            $stmt->execute([$key, json_encode($body[$key], JSON_UNESCAPED_UNICODE)]);
        }
    }

    jsonResponse(['message' => 'Uloženo']);
}

jsonResponse(['message' => 'Method not allowed'], 405);