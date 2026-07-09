<?php
require 'config.php';
$pdo->exec("UPDATE equipments SET image_path = 'uploads/pollination_pingpong_paddle.jpg' WHERE name = 'Table Tennis Paddle'");
$pdo->exec("UPDATE equipments SET image_path = 'uploads/pollination_pingpong_ball.jpg' WHERE name = 'Table Tennis Balls'");
$pdo->exec("UPDATE equipments SET image_path = 'uploads/pollination_football.jpg' WHERE name = 'Professional Football'");
echo "Updated DB with premium pollination images.\n";
