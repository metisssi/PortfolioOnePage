<?php
require_once __DIR__ . '/config.php';

header('Content-Type: text/html; charset=utf-8');
echo '<h2>🔧 Instalace (MySQL)</h2><pre>';

try {
    $db = getDB();
    echo "✅ Připojení k MySQL úspěšné\n";

    // --- Table: content ---
    $db->exec("CREATE TABLE IF NOT EXISTS content (
        id INT AUTO_INCREMENT PRIMARY KEY,
        section_key VARCHAR(50) NOT NULL UNIQUE,
        data JSON NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✅ Tabulka 'content' vytvořena\n";

    // --- Table: gallery ---
    $db->exec("CREATE TABLE IF NOT EXISTS gallery (
        id INT AUTO_INCREMENT PRIMARY KEY,
        url VARCHAR(500) NOT NULL,
        nadpis VARCHAR(255) DEFAULT '',
        popis VARCHAR(500) DEFAULT '',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✅ Tabulka 'gallery' vytvořena\n";

    // --- Table: reviews ---
    $db->exec("CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        jmeno VARCHAR(100) NOT NULL,
        prijmeni VARCHAR(100) NOT NULL,
        email VARCHAR(200) NOT NULL,
        text TEXT NOT NULL,
        approved TINYINT(1) DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✅ Tabulka 'reviews' vytvořena\n";

    // --- Seed content ---
    $check = $db->query("SELECT COUNT(*) FROM content")->fetchColumn();
    if ($check == 0) {
        $stmt = $db->prepare("INSERT INTO content (section_key, data) VALUES (?, ?)");

        $stmt->execute(['sluzby', json_encode([
            'nadpis' => 'Léčba bolestí zad',
            'text'   => "1. Výhřezy plotének\n2. Bechtěrevova nemoc\n3. Skolióza\n4. Skřípnutý nerv v zádech"
        ], JSON_UNESCAPED_UNICODE)]);

        $stmt->execute(['proc_za_mnou', json_encode([
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
        ], JSON_UNESCAPED_UNICODE)]);

        $stmt->execute(['o_mne', json_encode([
            'nadpis' => 'O mně',
            'text'   => '',
            'body'   => [],
            'foto'   => ''
        ], JSON_UNESCAPED_UNICODE)]);

        echo "✅ Počáteční data vložena\n";
    } else {
        echo "ℹ️ Data již existují, přeskočeno\n";
    }

    echo "\n🎉 Instalace dokončena!\n";
    echo "\n⚠️ Smažte install.php!\n";

} catch (Exception $e) {
    echo "❌ Chyba: " . $e->getMessage() . "\n";
    echo "\nZkontrolujte DB_HOST, DB_NAME, DB_USER, DB_PASS v config.php\n";
}

echo '</pre>';