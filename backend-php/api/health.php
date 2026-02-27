<?php
require_once __DIR__ . '/config.php';

try {
    $db = getDB();
    $db->query("SELECT 1");
    jsonResponse([
        'status' => 'ok',
        'db'     => 'MySQL connected',
        'time'   => date('Y-m-d H:i:s')
    ]);
} catch (Exception $e) {
    jsonResponse([
        'status' => 'error',
        'db'     => 'MySQL connection failed',
        'error'  => $e->getMessage()
    ], 500);
}