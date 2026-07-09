<?php
require_once __DIR__ . '/config.php';

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
$slots = [
    ['08:00:00', '10:00:00'],
    ['10:00:00', '12:00:00'],
    ['12:00:00', '14:00:00'],
    ['14:00:00', '16:00:00'],
    ['16:00:00', '18:00:00'],
];

// Clear all availability completely
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
$pdo->exec("TRUNCATE TABLE availability");
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

// Get all facilities
$facilities = $pdo->query("SELECT id FROM facilities")->fetchAll();

$stmt = $pdo->prepare("INSERT INTO availability (facility_id, start_time, end_time, day_of_week, status) VALUES (?, ?, ?, ?, 'available')");

foreach ($facilities as $f) {
    foreach ($days as $day) {
        foreach ($slots as $slot) {
            $stmt->execute([$f['id'], $slot[0], $slot[1], $day]);
        }
    }
}

echo "Availability successfully generated for all facilities!";
