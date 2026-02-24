<?php
require_once __DIR__ . '/config.php';

$method = getMethod();
$id = $_GET['id'] ?? null;

// --- GET: public ---
if ($method === 'GET') {
    $items = mongoFind('gallery', [], ['sort' => ['createdAt' => -1]]);

    $result = array_map(function ($item) {
        return [
            '_id'       => $item['_id'],
            'url'       => $item['url'] ?? '',
            'popis'     => $item['popis'] ?? '',
            'createdAt' => $item['createdAt'] ?? ''
        ];
    }, $items);

    jsonResponse($result);
}

// --- POST: admin ---
if ($method === 'POST') {
    requireAuth();
    $body = getRequestBody();
    $url   = trim($body['url'] ?? '');
    $popis = trim($body['popis'] ?? '');

    if (!$url) {
        jsonResponse(['message' => 'URL je povinné'], 400);
    }

    $newId = mongoInsertOne('gallery', [
        'url'       => $url,
        'popis'     => $popis,
        'createdAt' => new MongoDB\BSON\UTCDateTime()
    ]);

    jsonResponse(['_id' => $newId, 'url' => $url, 'popis' => $popis], 201);
}

// --- DELETE: admin ---
if ($method === 'DELETE' && $id) {
    requireAuth();

    try {
        $deleted = mongoDeleteOne('gallery', ['_id' => toObjectId($id)]);
    } catch (Exception $e) {
        jsonResponse(['message' => 'Neplatné ID'], 400);
    }

    if ($deleted === 0) {
        jsonResponse(['message' => 'Nenalezeno'], 404);
    }

    jsonResponse(['message' => 'Smazáno']);
}

jsonResponse(['message' => 'Method not allowed'], 405);