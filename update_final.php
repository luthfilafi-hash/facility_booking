<?php
require 'config.php';
$pdo->exec("UPDATE equipments SET image_path = 'uploads/pollination_baseball.jpg' WHERE id = 17");
$pdo->exec("UPDATE equipments SET image_path = 'uploads/pollination_pingpong_paddle.jpg' WHERE id = 12");
$pdo->exec("UPDATE equipments SET image_path = 'uploads/pollination_pingpong_ball.jpg' WHERE id = 19");
echo "Updated DB with Pollinations images\n";
