<?php
require 'config.php';
$tables = ['bookings', 'equipment_bookings', 'users'];
foreach ($tables as $table) {
    echo "--- $table ---\n";
    $stmt = $pdo->query("DESCRIBE $table");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
}
