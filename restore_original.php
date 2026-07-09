<?php
require 'config.php';

// Change back to the ORIGINAL fancy images
$pdo->exec("UPDATE equipments SET image_path = 'uploads/basketball_eq.png' WHERE name LIKE '%Basketball%'");
$pdo->exec("UPDATE equipments SET image_path = 'uploads/football_eq.png' WHERE name LIKE '%Football%'");

echo "Updated back to original fancy photos.\n";
