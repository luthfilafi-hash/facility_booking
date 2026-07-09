<?php
require_once __DIR__ . '/config.php';
$pdo->exec("ALTER TABLE timeslots ADD facility_id INT NULL AFTER id");
$pdo->exec("ALTER TABLE timeslots ADD day_of_week VARCHAR(20) NULL AFTER end_time");
echo "Done.";
