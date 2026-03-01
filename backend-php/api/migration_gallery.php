<?php
// Run this ONCE to add the lang column to your existing gallery table.
// Visit: http://localhost:8000/api/migrate_gallery
// Then DELETE this file!
require_once __DIR__ . '/config.php';

header('Content-Type: text/html; charset=utf-8');
echo '<h2>🔧 Migration: add lang column to gallery</h2><pre>';

try {
    $db = getDB();

    // Check if column already exists
    $cols = $db->query("SHOW COLUMNS FROM gallery LIKE 'lang'")->fetchAll();
    if (count($cols) > 0) {
        echo "ℹ️ Column 'lang' already exists — nothing to do.\n";
    } else {
        $db->exec("ALTER TABLE gallery ADD COLUMN lang ENUM('cs','en','all') NOT NULL DEFAULT 'all' AFTER popis");
        echo "✅ Column 'lang' added successfully!\n";
    }

    echo "\n🎉 Done! Delete this file now (migrate_gallery.php).\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
echo '</pre>';