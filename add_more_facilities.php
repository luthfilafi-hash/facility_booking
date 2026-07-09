<?php
require 'config.php';

$facilities = [
    ['name' => 'Indoor Golf Simulator', 'desc' => 'State-of-the-art indoor golf simulator with virtual courses.', 'loc' => 'Recreation Hub', 'img' => 'images/golf.png', 'cap' => 4],
    ['name' => 'Table Tennis Room', 'desc' => 'Dedicated room with professional ping pong tables and paddles.', 'loc' => 'Recreation Hub', 'img' => 'images/table_tennis.png', 'cap' => 8]
];

$stmt = $pdo->prepare("INSERT INTO facilities (name, description, location, image_path, status, capacity, created, modified) VALUES (?, ?, ?, ?, 'available', ?, NOW(), NOW())");

foreach ($facilities as $f) {
    $stmt->execute([$f['name'], $f['desc'], $f['loc'], $f['img'], $f['cap']]);
}

echo "Inserted 2 new facilities into the database!";
