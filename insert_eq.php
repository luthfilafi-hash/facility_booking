<?php
require 'config.php';

$equipments = [
    [
        'facility_id' => 3, // Badminton Court
        'name' => 'Badminton Racket & Shuttlecock Set',
        'total_quantity' => 10,
        'available_quantity' => 10,
        'image_path' => 'uploads/badminton_eq.png'
    ],
    [
        'facility_id' => 5, // Football Field
        'name' => 'Premium Leather Football',
        'total_quantity' => 5,
        'available_quantity' => 5,
        'image_path' => 'uploads/football_eq.png'
    ],
    [
        'facility_id' => 1, // Main Basketball Court
        'name' => 'Professional Basketball',
        'total_quantity' => 8,
        'available_quantity' => 8,
        'image_path' => 'uploads/basketball_eq.png'
    ]
];

foreach ($equipments as $eq) {
    $stmt = $pdo->prepare("INSERT INTO equipments (facility_id, name, total_quantity, available_quantity, image_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $eq['facility_id'],
        $eq['name'],
        $eq['total_quantity'],
        $eq['available_quantity'],
        $eq['image_path']
    ]);
}

echo "Equipments inserted successfully!\n";
