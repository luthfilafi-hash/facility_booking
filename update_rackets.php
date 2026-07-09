<?php
require 'config.php';

$brainDir = 'C:\Users\USER\.gemini\antigravity-ide\brain\533cfc71-a406-4365-8666-e7dd27e88888\\';
$uploadDir = 'c:/laragon/www/facility_booking/uploads/';

// Badminton Racket
copy($brainDir . 'badminton_racket_fancy_1783258822536.png', $uploadDir . 'badminton_racket_fancy.png');
$pdo->exec("UPDATE equipments SET image_path = 'uploads/badminton_racket_fancy.png' WHERE name LIKE '%Badminton Racket%'");

// Squash Racket
copy($brainDir . 'squash_racket_fancy_1783258838457.png', $uploadDir . 'squash_racket_fancy.png');
$pdo->exec("UPDATE equipments SET image_path = 'uploads/squash_racket_fancy.png' WHERE name LIKE '%Squash Racket%'");

echo "Images updated successfully.\n";
