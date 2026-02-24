<?php
require_once __DIR__ . '/config.php';

if (getMethod() !== 'POST') {
    jsonResponse(['message' => 'Method not allowed'], 405);
}

$body = getRequestBody();
$password = $body['password'] ?? '';

if ($password !== ADMIN_PASSWORD) {
    jsonResponse(['message' => 'Špatné heslo'], 401);
}

$token = jwt_encode(['role' => 'admin']);
jsonResponse(['token' => $token]);
