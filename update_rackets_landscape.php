<?php
require 'config.php';

$brainDir = 'C:\Users\USER\.gemini\antigravity-ide\brain\533cfc71-a406-4365-8666-e7dd27e88888\\';
$uploadDir = 'c:/laragon/www/facility_booking/uploads/';

// Badminton Racket
copy($brainDir . 'badminton_racket_landscape_1783259015762.png', $uploadDir . 'badminton_racket_landscape.png');
$pdo->exec("UPDATE equipments SET image_path = 'uploads/badminton_racket_landscape.png' WHERE name LIKE '%Badminton Racket%'");

// Squash Racket
copy($brainDir . 'squash_racket_landscape_1783259029175.png', $uploadDir . 'squash_racket_landscape.png');
$pdo->exec("UPDATE equipments SET image_path = 'uploads/squash_racket_landscape.png' WHERE name LIKE '%Squash Racket%'");

echo "Images updated successfully to landscape versions.\n";
