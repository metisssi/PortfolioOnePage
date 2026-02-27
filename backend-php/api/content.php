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
    $section = $body['section'] ?? '';
    $data    = $body['data'] ?? null;

    if (!$section || !$data) {
        jsonResponse(['message' => 'Chybí section nebo data'], 400);
    }

    $db = getDB();
    $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);

    // upsert
    $stmt = $db->prepare("INSERT INTO content (section_key, data)
                          VALUES (?, ?)
                          ON DUPLICATE KEY UPDATE data = VALUES(data)");
    $stmt->execute([$section, $jsonData]);

    jsonResponse(['message' => 'Uloženo']);
}

jsonResponse(['message' => 'Method not allowed'], 405);