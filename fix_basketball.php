<?php
require 'config.php';

// Fix the available quantity
$pdo->exec("UPDATE equipments SET available_quantity = total_quantity WHERE available_quantity > total_quantity");
echo "Quantities synced.\n";

// Generate a new basketball image
$prompt = 'Single%20professional%20Spalding%20indoor%20basketball%20ball,%20orange%20with%20standard%20black%20seams,%20studio%20lighting,%20dark%20background,%20photorealistic';
$url = "https://image.pollinations.ai/prompt/$prompt?width=800&height=600&nologo=true&seed=999";
$imgData = file_get_contents($url);

if ($imgData) {
    $path = "uploads/pollination_basketball_fixed.jpg";
    file_put_contents("c:/laragon/www/facility_booking/" . $path, $imgData);
    $pdo->exec("UPDATE equipments SET image_path = '$path' WHERE id = 3");
    echo "Basketball image updated.\n";
} else {
    echo "Failed to generate basketball image.\n";
}
