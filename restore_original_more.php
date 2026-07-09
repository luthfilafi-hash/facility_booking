<?php
require 'config.php';

// Change Baseball, Volleyball, and Ping Pong Paddle back to the ORIGINAL fancy images
$pdo->exec("UPDATE equipments SET image_path = 'uploads/baseball_eq.png' WHERE name LIKE '%Baseball%' AND name NOT LIKE '%Bat%'");
$pdo->exec("UPDATE equipments SET image_path = 'uploads/volleyball_eq.png' WHERE name LIKE '%Volleyball%'");
$pdo->exec("UPDATE equipments SET image_path = 'uploads/pingpong_eq.png' WHERE name LIKE '%Table Tennis Paddle%'");

echo "Updated back to original fancy photos.\n";
