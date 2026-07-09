<?php
require 'config.php';
try {
    $pdo->exec("ALTER TABLE maintenance CHANGE reason description TEXT NULL DEFAULT NULL");
} catch(Exception $e) { echo $e->getMessage() . "\n"; }

try {
    $pdo->exec("ALTER TABLE maintenance MODIFY end_date DATE NULL DEFAULT NULL");
} catch(Exception $e) { echo $e->getMessage() . "\n"; }

try {
    $pdo->exec("ALTER TABLE maintenance ADD status VARCHAR(255) NOT NULL DEFAULT 'scheduled'");
} catch(Exception $e) { echo $e->getMessage() . "\n"; }

try {
    $pdo->exec("ALTER TABLE maintenance ADD created DATETIME NULL DEFAULT CURRENT_TIMESTAMP");
} catch(Exception $e) { echo $e->getMessage() . "\n"; }

try {
    $pdo->exec("ALTER TABLE maintenance ADD modified DATETIME NULL DEFAULT CURRENT_TIMESTAMP");
} catch(Exception $e) { echo $e->getMessage() . "\n"; }

echo "Maintenance table schema fixed.\n";
