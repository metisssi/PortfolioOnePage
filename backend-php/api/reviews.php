<?php
require_once __DIR__ . '/config.php';

$method = getMethod();
$id     = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

// --- GET /reviews (public: only approved) ---
if ($method === 'GET' && !$action) {
    $db = getDB();
    $rows = $db->query("SELECT id, jmeno, prijmeni, text, created_at FROM reviews WHERE approved = 1 ORDER BY created_at DESC")->fetchAll();

    $result = array_map(function ($row) {
        return [
            '_id'       => (string)$row['id'],
            'jmeno'     => $row['jmeno'],
            'prijmeni'  => $row['prijmeni'],
            'text'      => $row['text'],
            'createdAt' => $row['created_at']
        ];
    }, $rows);

    jsonResponse($result);
}

// --- GET /reviews/all (admin: all reviews) ---
if ($method === 'GET' && $action === 'all') {
    requireAuth();
    $db = getDB();
    $rows = $db->query("SELECT id, jmeno, prijmeni, email, text, approved, created_at FROM reviews ORDER BY created_at DESC")->fetchAll();

    $result = array_map(function ($row) {
        return [
            '_id'       => (string)$row['id'],
            'jmeno'     => $row['jmeno'],
            'prijmeni'  => $row['prijmeni'],
            'email'     => $row['email'],
            'text'      => $row['text'],
            'approved'  => (bool)$row['approved'],
            'createdAt' => $row['created_at']
        ];
    }, $rows);

    jsonResponse($result);
}

// --- POST /reviews (public: submit review) ---
if ($method === 'POST') {
    $body = getRequestBody();
    $jmeno   = trim($body['jmeno'] ?? '');
    $prijmeni = trim($body['prijmeni'] ?? '');
    $email   = trim($body['email'] ?? '');
    $text    = trim($body['text'] ?? '');

    if (!$jmeno || !$prijmeni || !$email || !$text) {
        jsonResponse(['message' => 'Všechna pole jsou povinná'], 400);
    }

    $db = getDB();
    $stmt = $db->prepare("INSERT INTO reviews (jmeno, prijmeni, email, text, approved) VALUES (?, ?, ?, ?, 0)");
    $stmt->execute([$jmeno, $prijmeni, $email, $text]);

    jsonResponse(['message' => 'Recenze odeslána ke schválení'], 201);
}

// --- PATCH /reviews/:id/approve (admin) ---
if ($method === 'PATCH' && $id && $action === 'approve') {
    requireAuth();
    $db = getDB();
    $stmt = $db->prepare("UPDATE reviews SET approved = 1 WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        jsonResponse(['message' => 'Nenalezeno'], 404);
    }

    jsonResponse(['message' => 'Schváleno']);
}

// --- DELETE /reviews/:id (admin) ---
if ($method === 'DELETE' && $id) {
    requireAuth();
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        jsonResponse(['message' => 'Nenalezeno'], 404);
    }

    jsonResponse(['message' => 'Smazáno']);
}

jsonResponse(['message' => 'Method not allowed'], 405);