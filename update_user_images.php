<?php
require 'config.php';

$brainDir = 'C:\Users\USER\.gemini\antigravity-ide\brain\533cfc71-a406-4365-8666-e7dd27e88888\\';
$uploadDir = 'c:/laragon/www/facility_booking/uploads/';

// 3rd attachment -> Table Tennis Balls
copy($brainDir . 'media__1783233705530.png', $uploadDir . 'user_pingpong_ball.png');
$pdo->exec("UPDATE equipments SET image_path = 'uploads/user_pingpong_ball.png' WHERE name = 'Table Tennis Balls'");

// 4th attachment -> Professional Football
copy($brainDir . 'media__1783233779364.png', $uploadDir . 'user_football.png');
$pdo->exec("UPDATE equipments SET image_path = 'uploads/user_football.png' WHERE name = 'Professional Football'");

// 5th attachment -> Table Tennis Paddle
copy($brainDir . 'media__1783233931997.png', $uploadDir . 'user_pingpong_paddle.png');
$pdo->exec("UPDATE equipments SET image_path = 'uploads/user_pingpong_paddle.png' WHERE name = 'Table Tennis Paddle'");

echo "Images updated successfully.\n";
