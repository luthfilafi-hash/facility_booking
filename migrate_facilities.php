<?php
require_once __DIR__ . '/config.php';
try {
    $pdo->exec("ALTER TABLE facilities ADD maintenance_time DATETIME NULL DEFAULT NULL");
    echo "Migration successful\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
