<?php
require 'config.php';

try {
    // Check if the table exists first
    $result = $pdo->query("SHOW TABLES LIKE 'timeslots'")->fetch();
    if ($result) {
        $pdo->exec("RENAME TABLE timeslots TO availability");
        echo "Table renamed successfully!\n";
    } else {
        echo "Table 'timeslots' does not exist. Maybe already renamed?\n";
    }
} catch (PDOException $e) {
    echo "Error renaming table: " . $e->getMessage();
}
