<?php
require 'config.php';

$brainDir = 'C:\Users\USER\.gemini\antigravity-ide\brain\533cfc71-a406-4365-8666-e7dd27e88888\\';
$uploadDir = 'c:/laragon/www/facility_booking/uploads/';

// Table Tennis Balls
copy($brainDir . 'pingpong_balls_fancy_1783258678679.png', $uploadDir . 'pingpong_balls_fancy.png');
$pdo->exec("UPDATE equipments SET image_path = 'uploads/pingpong_balls_fancy.png' WHERE name = 'Table Tennis Balls'");

// Baseball (only ball)
copy($brainDir . 'baseball_ball_fancy_1783258661721.png', $uploadDir . 'baseball_only_fancy.png');
$pdo->exec("UPDATE equipments SET image_path = 'uploads/baseball_only_fancy.png' WHERE name LIKE '%Baseball%' AND name NOT LIKE '%Bat%'");

echo "Images updated successfully.\n";
