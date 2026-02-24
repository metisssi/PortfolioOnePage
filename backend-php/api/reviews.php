<?php
require_once __DIR__ . '/config.php';

$method = getMethod();
$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

function formatReview(array $doc): array {
    return [
        '_id'       => $doc['_id'],
        'jmeno'     => $doc['jmeno'] ?? '',
        'prijmeni'  => $doc['prijmeni'] ?? '',
        'email'     => $doc['email'] ?? '',
        'text'      => $doc['text'] ?? '',
        'approved'  => (bool)($doc['approved'] ?? false),
        'createdAt' => $doc['createdAt'] ?? ''
    ];
}

// --- GET /api/reviews (public - approved only) ---
if ($method === 'GET' && $action === null) {
    $items = mongoFind('reviews', ['approved' => true], ['sort' => ['createdAt' => -1]]);
    jsonResponse(array_map('formatReview', $items));
}

// --- GET /api/reviews/all (admin) ---
if ($method === 'GET' && $action === 'all') {
    requireAuth();
    $items = mongoFind('reviews', [], ['sort' => ['createdAt' => -1]]);
    jsonResponse(array_map('formatReview', $items));
}

// --- POST /api/reviews (public) ---
if ($method === 'POST' && !$id) {
    $body = getRequestBody();
    $jmeno    = trim($body['jmeno'] ?? '');
    $prijmeni = trim($body['prijmeni'] ?? '');
    $email    = trim($body['email'] ?? '');
    $text     = trim($body['text'] ?? '');

    if (!$jmeno || !$prijmeni || !$email || !$text) {
        jsonResponse(['message' => 'Vyplňte všechna pole'], 400);
    }

    mongoInsertOne('reviews', [
        'jmeno'     => $jmeno,
        'prijmeni'  => $prijmeni,
        'email'     => $email,
        'text'      => $text,
        'approved'  => false,
        'createdAt' => new MongoDB\BSON\UTCDateTime()
    ]);

    jsonResponse(['message' => 'Recenze přijata, čeká na schválení'], 201);
}

// --- PATCH /api/reviews/:id/approve (admin) ---
if ($method === 'PATCH' && $id && $action === 'approve') {
    requireAuth();

    try {
        $oid = toObjectId($id);
    } catch (Exception $e) {
        jsonResponse(['message' => 'Neplatné ID'], 400);
    }

    $updated = mongoUpdateOne('reviews', ['_id' => $oid], ['$set' => ['approved' => true]]);

    if ($updated === 0) {
        jsonResponse(['message' => 'Nenalezeno'], 404);
    }

    $doc = mongoFindOne('reviews', ['_id' => $oid]);
    jsonResponse(formatReview($doc));
}

// --- DELETE /api/reviews/:id (admin) ---
if ($method === 'DELETE' && $id) {
    requireAuth();

    try {
        $oid = toObjectId($id);
    } catch (Exception $e) {
        jsonResponse(['message' => 'Neplatné ID'], 400);
    }

    mongoDeleteOne('reviews', ['_id' => $oid]);
    jsonResponse(['message' => 'Smazáno']);
}

jsonResponse(['message' => 'Method not allowed'], 405);