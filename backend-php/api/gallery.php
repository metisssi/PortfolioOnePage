<?php
require_once __DIR__ . '/config.php';

$method = getMethod();
$id = $_GET['id'] ?? null;

// --- GET: public ---
if ($method === 'GET') {
    $db = getDB();
    $rows = $db->query("SELECT id, url, nadpis, popis, created_at FROM gallery ORDER BY created_at DESC")->fetchAll();

    $result = array_map(function ($row) {
        return [
            '_id'       => (string)$row['id'],
            'url'       => $row['url'],
            'nadpis'    => $row['nadpis'] ?? '',
            'popis'     => $row['popis'] ?? '',
            'createdAt' => $row['created_at']
        ];
    }, $rows);

    jsonResponse($result);
}

// --- POST: admin ---
if ($method === 'POST') {
    requireAuth();
    $body = getRequestBody();
    $url    = trim($body['url'] ?? '');
    $nadpis = trim($body['nadpis'] ?? '');
    $popis  = trim($body['popis'] ?? '');

    if (!$url) {
        jsonResponse(['message' => 'URL je povinné'], 400);
    }

    $db = getDB();
    $stmt = $db->prepare("INSERT INTO gallery (url, nadpis, popis) VALUES (?, ?, ?)");
    $stmt->execute([$url, $nadpis, $popis]);
    $newId = $db->lastInsertId();

    jsonResponse(['_id' => (string)$newId, 'url' => $url, 'nadpis' => $nadpis, 'popis' => $popis], 201);
}

// --- DELETE: admin ---
if ($method === 'DELETE' && $id) {
    requireAuth();

    $db = getDB();
    $stmt = $db->prepare("DELETE FROM gallery WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        jsonResponse(['message' => 'Nenalezeno'], 404);
    }

    jsonResponse(['message' => 'Smazáno']);
}

jsonResponse(['message' => 'Method not allowed'], 405);