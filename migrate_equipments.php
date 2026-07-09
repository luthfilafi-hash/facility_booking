<?php
require_once __DIR__ . '/config.php';

// 1. Create equipment_bookings table
$sql = "
CREATE TABLE IF NOT EXISTS equipment_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    equipment_id INT NOT NULL,
    quantity INT NOT NULL,
    booking_date DATE NOT NULL,
    status VARCHAR(255) DEFAULT 'pending',
    notes TEXT,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT CURRENT_TIMESTAMP
);
";
$pdo->exec($sql);
echo "Table equipment_bookings created/verified.\n";

// 2. Add image_path to equipments
try {
    $pdo->exec("ALTER TABLE equipments ADD COLUMN image_path VARCHAR(255) DEFAULT NULL");
    echo "Added image_path to equipments.\n";
} catch (Exception $e) {
    echo "image_path already exists or another error: " . $e->getMessage() . "\n";
}
