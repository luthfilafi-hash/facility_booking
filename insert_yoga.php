<?php
require 'config.php';

$path = 'uploads/pollination_yoga_mat.jpg';

$stmt = $pdo->query("SELECT id FROM equipments WHERE name = 'Premium Yoga Mat'");
if (!$stmt->fetch()) {
    $stmt = $pdo->prepare("INSERT INTO equipments (facility_id, name, total_quantity, available_quantity, image_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        1,
        'Premium Yoga Mat',
        10,
        10,
        $path
    ]);
    echo "Inserted Yoga Mat.\n";
} else {
    $pdo->exec("UPDATE equipments SET image_path = '$path' WHERE name = 'Premium Yoga Mat'");
    echo "Updated Yoga Mat.\n";
}
