<?php
require_once __DIR__ . '/config.php';

$method = getMethod();

if ($method === 'POST') {
    $body = getRequestBody();
    $password = $body['password'] ?? '';

    if ($password !== ADMIN_PASSWORD) {
        jsonResponse(['message' => 'Nesprávné heslo'], 401);
    }

    $token = jwt_encode(['role' => 'admin']);
    jsonResponse(['token' => $token]);
}

jsonResponse(['message' => 'Method not allowed'], 405);