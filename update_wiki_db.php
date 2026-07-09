<?php
require 'config.php';
$pdo->exec("UPDATE equipments SET image_path = 'uploads/wiki_volleyball.jpg' WHERE id = 6");
$pdo->exec("UPDATE equipments SET image_path = 'uploads/wiki_basketball.png' WHERE id = 3");
echo "Updated Volleyball and Basketball\n";
