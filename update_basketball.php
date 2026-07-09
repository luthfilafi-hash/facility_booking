<?php
require 'config.php';
$pdo->exec("UPDATE equipments SET image_path = 'uploads/real_basketball.png' WHERE name LIKE '%Basketball%'");
echo "Basketball updated.\n";
