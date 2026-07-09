<?php
require_once __DIR__ . '/config.php';

$pdo->exec("UPDATE facilities SET image_path = 'images/facility.png' WHERE image_path IS NULL OR image_path = ''");
$pdo->exec("UPDATE equipments SET image_path = 'images/equipment.png' WHERE image_path IS NULL OR image_path = ''");

echo "Updated images in DB.\n";
