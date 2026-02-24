<?php
require_once __DIR__ . '/config.php';

$method = getMethod();

// --- GET: public ---
if ($method === 'GET') {
    $doc = mongoFindOne('content');

    if (!$doc) {
        jsonResponse([
            'sluzby' => ['nadpis' => 'Léčba bolestí zad', 'text' => ''],
            'proc_za_mnou' => ['nadpis' => 'Proč za mnou?', 'body' => []],
            'o_mne' => ['nadpis' => 'O mně', 'text' => '', 'body' => [], 'foto' => '']
        ]);
    }

    jsonResponse([
        'sluzby' => $doc['sluzby'] ?? ['nadpis' => '', 'text' => ''],
        'proc_za_mnou' => $doc['proc_za_mnou'] ?? ['nadpis' => '', 'body' => []],
        'o_mne' => $doc['o_mne'] ?? ['nadpis' => '', 'text' => '', 'body' => [], 'foto' => '']
    ]);
}

// --- PUT: admin only ---
if ($method === 'PUT') {
    requireAuth();
    $body = getRequestBody();

    $update = [
        'sluzby' => [
            'nadpis' => $body['sluzby']['nadpis'] ?? '',
            'text'   => $body['sluzby']['text'] ?? ''
        ],
        'proc_za_mnou' => [
            'nadpis' => $body['proc_za_mnou']['nadpis'] ?? '',
            'body'   => $body['proc_za_mnou']['body'] ?? []
        ],
        'o_mne' => [
            'nadpis' => $body['o_mne']['nadpis'] ?? '',
            'text'   => $body['o_mne']['text'] ?? '',
            'body'   => $body['o_mne']['body'] ?? [],
            'foto'   => $body['o_mne']['foto'] ?? ''
        ]
    ];

    mongoUpdateOne('content', [], ['$set' => $update], true);

    $doc = mongoFindOne('content');
    jsonResponse([
        'sluzby' => $doc['sluzby'],
        'proc_za_mnou' => $doc['proc_za_mnou'],
        'o_mne' => $doc['o_mne']
    ]);
}

jsonResponse(['message' => 'Method not allowed'], 405);