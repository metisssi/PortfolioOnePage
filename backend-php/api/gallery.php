<?php
require_once __DIR__ . '/config.php';

$method = getMethod();
$id     = $_GET['id'] ?? null;

// --- GET: public ---
if ($method === 'GET') {
    $db   = getDB();
    $lang = $_GET['lang'] ?? null;

    // Filter by lang: return items matching the lang OR items set to 'all'
    if ($lang && in_array($lang, ['cs', 'en'])) {
        $stmt = $db->prepare(
            "SELECT id, url, nadpis, popis, lang, created_at
             FROM gallery
             WHERE lang = ? OR lang = 'all'
             ORDER BY created_at DESC"
        );
        $stmt->execute([$lang]);
    } else {
        $stmt = $db->query(
            "SELECT id, url, nadpis, popis, lang, created_at
             FROM gallery ORDER BY created_at DESC"
        );
    }

    $rows   = $stmt->fetchAll();
    $result = array_map(function ($row) {
        return [
            '_id'       => (string)$row['id'],
            'url'       => $row['url'],
            'nadpis'    => $row['nadpis'] ?? '',
            'popis'     => $row['popis']  ?? '',
            'lang'      => $row['lang']   ?? 'all',
            'createdAt' => $row['created_at'],
        ];
    }, $rows);

    jsonResponse($result);
}

// --- POST: admin ---
if ($method === 'POST') {
    requireAuth();
    $body   = getRequestBody();
    $url    = trim($body['url']    ?? '');
    $nadpis = trim($body['nadpis'] ?? '');
    $popis  = trim($body['popis']  ?? '');
    $lang   = trim($body['lang']   ?? 'all');

    if (!$url) jsonResponse(['message' => 'URL je povinné'], 400);
    if (!in_array($lang, ['cs', 'en', 'all'])) $lang = 'all';

    $db   = getDB();
    $stmt = $db->prepare(
        "INSERT INTO gallery (url, nadpis, popis, lang) VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$url, $nadpis, $popis, $lang]);
    $newId = $db->lastInsertId();

    jsonResponse(['_id' => (string)$newId, 'url' => $url,
                  'nadpis' => $nadpis, 'popis' => $popis, 'lang' => $lang], 201);
}

// --- DELETE: admin ---
if ($method === 'DELETE' && $id) {
    requireAuth();
    $db   = getDB();
    $stmt = $db->prepare("DELETE FROM gallery WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) jsonResponse(['message' => 'Nenalezeno'], 404);
    jsonResponse(['message' => 'Smazáno']);
}

jsonResponse(['message' => 'Method not allowed'], 405);