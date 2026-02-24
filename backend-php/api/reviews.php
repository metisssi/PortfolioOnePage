<?php
require_once __DIR__ . '/config.php';

$db = getDB();
$method = getMethod();
$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

// Helper to format review row
function formatReview(array $row): array {
    return [
        '_id' => (string)$row['id'],
        'jmeno' => $row['jmeno'],
        'prijmeni' => $row['prijmeni'],
        'email' => $row['email'],
        'text' => $row['text'],
        'approved' => (bool)$row['approved'],
        'createdAt' => $row['created_at']
    ];
}

// --- GET /api/reviews (public - approved only) ---
if ($method === 'GET' && $action === null) {
    $stmt = $db->query('SELECT * FROM reviews WHERE approved = 1 ORDER BY created_at DESC');
    jsonResponse(array_map('formatReview', $stmt->fetchAll()));
}

// --- GET /api/reviews/all (admin - all reviews) ---
if ($method === 'GET' && $action === 'all') {
    requireAuth();
    $stmt = $db->query('SELECT * FROM reviews ORDER BY created_at DESC');
    jsonResponse(array_map('formatReview', $stmt->fetchAll()));
}

// --- POST /api/reviews (public - submit review) ---
if ($method === 'POST' && !$id) {
    $body = getRequestBody();
    $jmeno = trim($body['jmeno'] ?? '');
    $prijmeni = trim($body['prijmeni'] ?? '');
    $email = trim($body['email'] ?? '');
    $text = trim($body['text'] ?? '');

    if (!$jmeno || !$prijmeni || !$email || !$text) {
        jsonResponse(['message' => 'Vyplňte všechna pole'], 400);
    }

    $stmt = $db->prepare('INSERT INTO reviews (jmeno, prijmeni, email, text) VALUES (?, ?, ?, ?)');
    $stmt->execute([$jmeno, $prijmeni, $email, $text]);

    jsonResponse(['message' => 'Recenze přijata, čeká na schválení'], 201);
}

// --- PATCH /api/reviews/:id/approve (admin) ---
if ($method === 'PATCH' && $id && $action === 'approve') {
    requireAuth();

    $stmt = $db->prepare('UPDATE reviews SET approved = 1 WHERE id = ?');
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        jsonResponse(['message' => 'Nenalezeno'], 404);
    }

    $stmt = $db->prepare('SELECT * FROM reviews WHERE id = ?');
    $stmt->execute([$id]);
    jsonResponse(formatReview($stmt->fetch()));
}

// --- DELETE /api/reviews/:id (admin) ---
if ($method === 'DELETE' && $id) {
    requireAuth();

    $stmt = $db->prepare('DELETE FROM reviews WHERE id = ?');
    $stmt->execute([$id]);

    jsonResponse(['message' => 'Smazáno']);
}

jsonResponse(['message' => 'Method not allowed'], 405);
