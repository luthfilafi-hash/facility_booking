<?php
require_once __DIR__ . '/config.php';
$facilities = $pdo->query("SELECT id, name, status, maintenance_time FROM facilities WHERE name = 'Badminton Court'")->fetchAll();
$slots = $pdo->query("SELECT * FROM availability WHERE facility_id = 3 AND day_of_week = 'Sunday'")->fetchAll();
print_r($facilities);
print_r($slots);
