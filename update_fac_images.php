<?php
require 'config.php';
$pdo->exec("UPDATE facilities SET image_path = 'images/basketball.png' WHERE name LIKE '%Basketball%'");
$pdo->exec("UPDATE facilities SET image_path = 'images/badminton.png' WHERE name LIKE '%Badminton%'");
echo "Updated facilities images.";
