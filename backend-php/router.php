<?php
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

if (preg_match('#^/api/(.*)#', $path, $m)) {
    $route = $m[1];
    chdir(__DIR__ . '/api');
    
    if ($route === 'auth/login')                             { require 'auth.php'; exit; }
    if ($route === 'content')                                { require 'content.php'; exit; }
    if ($route === 'gallery')                                { require 'gallery.php'; exit; }
    if (preg_match('#^gallery/(.+)$#', $route, $r))         { $_GET['id'] = $r[1]; require 'gallery.php'; exit; }
    if ($route === 'reviews')                                { require 'reviews.php'; exit; }
    if ($route === 'reviews/all')                            { $_GET['action'] = 'all'; require 'reviews.php'; exit; }
    if (preg_match('#^reviews/(.+)/approve$#', $route, $r)) { $_GET['id'] = $r[1]; $_GET['action'] = 'approve'; require 'reviews.php'; exit; }
    if (preg_match('#^reviews/(.+)$#', $route, $r))         { $_GET['id'] = $r[1]; require 'reviews.php'; exit; }
    if ($route === 'health')                                 { require 'health.php'; exit; }
    if ($route === 'install')                                { require 'install.php'; exit; }
    
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Not found']);
    exit;
}

return false;