<?php
require_once __DIR__ . '/config.php';
$slots = $pdo->query("SELECT * FROM availability WHERE facility_id = 3 AND day_of_week = 'Monday'")->fetchAll();
print_r($slots);
