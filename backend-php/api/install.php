<?php
require_once __DIR__ . '/config.php';

header('Content-Type: text/html; charset=utf-8');
echo '<h2>🔧 Instalace (MongoDB)</h2><pre>';

try {
    $manager = getManager();
    $command = new MongoDB\Driver\Command(['ping' => 1]);
    $manager->executeCommand('admin', $command);
    echo "✅ Připojení k MongoDB úspěšné\n";

    // Seed content if empty
    $count = mongoCount('content');
    if ($count === 0) {
        mongoInsertOne('content', [
            'sluzby' => [
                'nadpis' => 'Léčba bolestí zad',
                'text'   => "1. Výhřezy plotének\n2. Bechtěrevova nemoc\n3. Skolióza\n4. Skřípnutý nerv v zádech"
            ],
            'proc_za_mnou' => [
                'nadpis' => 'Proč za mnou?',
                'body'   => [
                    'Metoda nemá vedlejší účinky',
                    'Rychlé výsledky',
                    'Odstranění příčiny',
                    'Záruka',
                    'Bezoperační způsob léčení',
                    'Léčení bez léků',
                    'Léčení bez fyzického namáhání',
                    'Relaxační terapie',
                    'Terapie na dálku'
                ]
            ],
            'o_mne' => [
                'nadpis' => 'O mně',
                'text'   => '',
                'body'   => [],
                'foto'   => ''
            ]
        ]);
        echo "✅ Počáteční data vložena\n";
    } else {
        echo "ℹ️ Data již existují, přeskočeno\n";
    }

    echo "\n🎉 Instalace dokončena!\n";
    echo "\n⚠️ Smažte install.php!\n";

} catch (Exception $e) {
    echo "❌ Chyba: " . $e->getMessage() . "\n";
    echo "\nZkontrolujte MONGO_URI v config.php\n";
}

echo '</pre>';