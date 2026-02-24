<?php
require_once __DIR__ . '/config.php';

$db = getDB();
$method = getMethod();

// --- GET: public ---
if ($method === 'GET') {
    $stmt = $db->query('SELECT * FROM content LIMIT 1');
    $row = $stmt->fetch();

    if (!$row) {
        // Return default empty content
        jsonResponse([
            'sluzby' => ['nadpis' => 'Léčba bolestí zad', 'text' => ''],
            'proc_za_mnou' => ['nadpis' => 'Proč za mnou?', 'body' => []],
            'o_mne' => ['nadpis' => 'O mně', 'text' => '', 'body' => [], 'foto' => '']
        ]);
    }

    jsonResponse([
        'sluzby' => [
            'nadpis' => $row['sluzby_nadpis'],
            'text' => $row['sluzby_text']
        ],
        'proc_za_mnou' => [
            'nadpis' => $row['proc_nadpis'],
            'body' => json_decode($row['proc_body'], true) ?: []
        ],
        'o_mne' => [
            'nadpis' => $row['omne_nadpis'],
            'text' => $row['omne_text'],
            'body' => json_decode($row['omne_body'], true) ?: [],
            'foto' => $row['omne_foto']
        ]
    ]);
}

// --- PUT: admin only ---
if ($method === 'PUT') {
    requireAuth();
    $body = getRequestBody();

    $sluzby = $body['sluzby'] ?? [];
    $proc = $body['proc_za_mnou'] ?? [];
    $omne = $body['o_mne'] ?? [];

    // Check if row exists
    $stmt = $db->query('SELECT id FROM content LIMIT 1');
    $exists = $stmt->fetch();

    if ($exists) {
        $stmt = $db->prepare('UPDATE content SET
            sluzby_nadpis = ?, sluzby_text = ?,
            proc_nadpis = ?, proc_body = ?,
            omne_nadpis = ?, omne_text = ?, omne_body = ?, omne_foto = ?
            WHERE id = ?');
        $stmt->execute([
            $sluzby['nadpis'] ?? '',
            $sluzby['text'] ?? '',
            $proc['nadpis'] ?? '',
            json_encode($proc['body'] ?? [], JSON_UNESCAPED_UNICODE),
            $omne['nadpis'] ?? '',
            $omne['text'] ?? '',
            json_encode($omne['body'] ?? [], JSON_UNESCAPED_UNICODE),
            $omne['foto'] ?? '',
            $exists['id']
        ]);
    } else {
        $stmt = $db->prepare('INSERT INTO content
            (sluzby_nadpis, sluzby_text, proc_nadpis, proc_body, omne_nadpis, omne_text, omne_body, omne_foto)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $sluzby['nadpis'] ?? '',
            $sluzby['text'] ?? '',
            $proc['nadpis'] ?? '',
            json_encode($proc['body'] ?? [], JSON_UNESCAPED_UNICODE),
            $omne['nadpis'] ?? '',
            $omne['text'] ?? '',
            json_encode($omne['body'] ?? [], JSON_UNESCAPED_UNICODE),
            $omne['foto'] ?? ''
        ]);
    }

    // Return updated content
    $stmt = $db->query('SELECT * FROM content LIMIT 1');
    $row = $stmt->fetch();

    jsonResponse([
        'sluzby' => ['nadpis' => $row['sluzby_nadpis'], 'text' => $row['sluzby_text']],
        'proc_za_mnou' => ['nadpis' => $row['proc_nadpis'], 'body' => json_decode($row['proc_body'], true) ?: []],
        'o_mne' => ['nadpis' => $row['omne_nadpis'], 'text' => $row['omne_text'], 'body' => json_decode($row['omne_body'], true) ?: [], 'foto' => $row['omne_foto']]
    ]);
}

jsonResponse(['message' => 'Method not allowed'], 405);
