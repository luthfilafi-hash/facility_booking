<?php
require_once __DIR__ . '/config.php';

$facilities = $pdo->query("SELECT id FROM facilities")->fetchAll(PDO::FETCH_COLUMN);

$days = ['Saturday', 'Sunday'];
$slots = [
    ['08:00:00', '10:00:00'],
    ['10:00:00', '12:00:00'],
    ['12:00:00', '14:00:00'],
    ['14:00:00', '16:00:00'],
    ['16:00:00', '18:00:00']
];

$added = 0;

foreach ($facilities as $fid) {
    foreach ($days as $day) {
        $check = $pdo->prepare("SELECT COUNT(*) FROM availability WHERE facility_id = ? AND day_of_week = ?");
        $check->execute([$fid, $day]);
        if ($check->fetchColumn() == 0) {
            foreach ($slots as $slot) {
                $stmt = $pdo->prepare("INSERT INTO availability (facility_id, start_time, end_time, day_of_week, status) VALUES (?, ?, ?, ?, 'available')");
                $stmt->execute([$fid, $slot[0], $slot[1], $day]);
                $added++;
            }
        }
    }
}

echo "Added $added weekend slots for facilities.\n";
