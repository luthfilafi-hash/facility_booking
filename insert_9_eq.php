<?php
require 'config.php';

$equipments = [
    [
        'facility_id' => 2, // Tennis Court
        'name' => 'Tennis Racket & Balls',
        'total_quantity' => 12,
        'available_quantity' => 12,
        'image_path' => 'uploads/tennis_eq.png'
    ],
    [
        'facility_id' => 4, // Running Track
        'name' => 'Track Spikes (Size 7-12)',
        'total_quantity' => 20,
        'available_quantity' => 20,
        'image_path' => 'uploads/track_eq.png'
    ],
    [
        'facility_id' => 6, // Volleyball Court
        'name' => 'Indoor Volleyball',
        'total_quantity' => 6,
        'available_quantity' => 6,
        'image_path' => 'uploads/volleyball_eq.png'
    ],
    [
        'facility_id' => 7, // Swimming Pool
        'name' => 'Swimming Goggles & Cap',
        'total_quantity' => 15,
        'available_quantity' => 15,
        'image_path' => 'uploads/swimming_eq.png'
    ],
    [
        'facility_id' => 8, // Gymnasium
        'name' => 'Hex Dumbbell Set (10-50lbs)',
        'total_quantity' => 10,
        'available_quantity' => 10,
        'image_path' => 'uploads/gym_eq.png'
    ],
    [
        'facility_id' => 9, // Squash Court
        'name' => 'Squash Racket & Double Dot Ball',
        'total_quantity' => 8,
        'available_quantity' => 8,
        'image_path' => 'uploads/squash_eq.png'
    ],
    [
        'facility_id' => 10, // Baseball Field
        'name' => 'Wooden Bat & Baseball',
        'total_quantity' => 5,
        'available_quantity' => 5,
        'image_path' => 'uploads/baseball_eq.png'
    ],
    [
        'facility_id' => 11, // Indoor Golf Simulator
        'name' => 'Premium Golf Driver & Balls',
        'total_quantity' => 4,
        'available_quantity' => 4,
        'image_path' => 'uploads/golf_eq.png'
    ],
    [
        'facility_id' => 12, // Table Tennis Room
        'name' => 'Table Tennis Paddle & Balls',
        'total_quantity' => 10,
        'available_quantity' => 10,
        'image_path' => 'uploads/pingpong_eq.png'
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

echo "9 additional equipments inserted successfully!\n";
