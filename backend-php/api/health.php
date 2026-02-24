<?php
require_once __DIR__ . '/config.php';

try {
    $manager = getManager();
    $command = new MongoDB\Driver\Command(['ping' => 1]);
    $manager->executeCommand('admin', $command);
    jsonResponse(['status' => 'ok', 'db' => 'mongodb']);
} catch (Exception $e) {
    jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
}