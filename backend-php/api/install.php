<?php
/**
 * ============================================================
 *  install.php — Creates MySQL tables + seeds initial data
 *  
 *  1. Upload all files to your hosting
 *  2. Edit api/config.php with your DB credentials
 *  3. Open https://your-domain.cz/api/install.php in browser
 *  4. DELETE this file after successful installation!
 * ============================================================
 */

require_once __DIR__ . '/config.php';

header('Content-Type: text/html; charset=utf-8');

echo '<h2>🔧 Instalace databáze</h2><pre>';

try {
    $db = getDB();

    // --- Create tables ---

    $db->exec("
        CREATE TABLE IF NOT EXISTS content (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sluzby_nadpis VARCHAR(255) DEFAULT 'Léčba bolestí zad',
            sluzby_text TEXT,
            proc_nadpis VARCHAR(255) DEFAULT 'Proč za mnou?',
            proc_body JSON,
            omne_nadpis VARCHAR(255) DEFAULT 'O mně',
            omne_text TEXT,
            omne_body JSON,
            omne_foto VARCHAR(500) DEFAULT ''
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Tabulka 'content' vytvořena\n";

    $db->exec("
        CREATE TABLE IF NOT EXISTS gallery (
            id INT AUTO_INCREMENT PRIMARY KEY,
            url VARCHAR(500) NOT NULL,
            popis VARCHAR(255) DEFAULT '',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Tabulka 'gallery' vytvořena\n";

    $db->exec("
        CREATE TABLE IF NOT EXISTS reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            jmeno VARCHAR(100) NOT NULL,
            prijmeni VARCHAR(100) NOT NULL,
            email VARCHAR(200) NOT NULL,
            text TEXT NOT NULL,
            approved TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Tabulka 'reviews' vytvořena\n";

    // --- Seed content if empty ---
    $stmt = $db->query('SELECT COUNT(*) as cnt FROM content');
    if ($stmt->fetch()['cnt'] == 0) {
        $procBody = json_encode([
            'Metoda nemá vedlejší účinky',
            'Rychlé výsledky',
            'Odstranění příčiny',
            'Záruka',
            'Bezoperační způsob léčení',
            'Léčení bez léků',
            'Léčení bez fyzického namáhání',
            'Relaxační terapie',
            'Terapie na dálku'
        ], JSON_UNESCAPED_UNICODE);

        $stmt = $db->prepare("INSERT INTO content 
            (sluzby_nadpis, sluzby_text, proc_nadpis, proc_body, omne_nadpis, omne_text, omne_body, omne_foto) 
            VALUES (?, ?, ?, ?, ?, ?, '[]', '')");
        $stmt->execute([
            'Léčba bolestí zad',
            "1. Výhřezy plotének\n2. Bechtěrevova nemoc\n3. Skolióza\n4. Skřípnutý nerv v zádech",
            'Proč za mnou?',
            $procBody,
            'O mně',
            ''
        ]);
        echo "✅ Počáteční data vložena\n";
    } else {
        echo "ℹ️ Tabulka 'content' již obsahuje data, přeskočeno\n";
    }

    echo "\n🎉 Instalace dokončena!\n";
    echo "\n⚠️  DŮLEŽITÉ: Smažte tento soubor (install.php) z hostingu!\n";

} catch (PDOException $e) {
    echo "❌ Chyba: " . $e->getMessage() . "\n";
    echo "\nZkontrolujte přihlašovací údaje v api/config.php\n";
}

echo '</pre>';
