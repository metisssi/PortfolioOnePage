<?php
require_once __DIR__ . '/config.php';

$db = getDB();
$method = getMethod();
$id = $_GET['id'] ?? null;

// --- GET: public - list all ---
if ($method === 'GET') {
    $stmt = $db->query('SELECT * FROM gallery ORDER BY created_at DESC');
    $items = $stmt->fetchAll();

    // Format for frontend compatibility (use 'id' as string like MongoDB _id)
    $result = array_map(function ($item) {
        return [
            '_id' => (string)$item['id'],
            'url' => $item['url'],
            'popis' => $item['popis'],
            'createdAt' => $item['created_at']
        ];
    }, $items);

    jsonResponse($result);
}

// --- POST: admin - add photo ---
if ($method === 'POST') {
    requireAuth();
    $body = getRequestBody();
    $url = trim($body['url'] ?? '');
    $popis = trim($body['popis'] ?? '');

    if (!$url) {
        jsonResponse(['message' => 'URL je povinné'], 400);
    }

    $stmt = $db->prepare('INSERT INTO gallery (url, popis) VALUES (?, ?)');
    $stmt->execute([$url, $popis]);

    $newId = $db->lastInsertId();
    jsonResponse([
        '_id' => (string)$newId,
        'url' => $url,
        'popis' => $popis
    ], 201);
}

// --- DELETE: admin - delete photo ---
if ($method === 'DELETE' && $id) {
    requireAuth();

    $stmt = $db->prepare('DELETE FROM gallery WHERE id = ?');
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        jsonResponse(['message' => 'Nenalezeno'], 404);
    }

    jsonResponse(['message' => 'Smazáno']);
}

jsonResponse(['message' => 'Method not allowed'], 405);
