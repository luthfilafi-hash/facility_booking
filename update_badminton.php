<?php
require 'config.php';
$pdo->exec("UPDATE equipments SET image_path = 'uploads/pollination_badminton.jpg' WHERE id = 1");
echo "Updated Badminton Racket\n";
