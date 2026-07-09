<?php
require 'config.php';

// Restore the user's provided exact photos
$pdo->exec("UPDATE equipments SET image_path = 'uploads/user_football.png' WHERE name = 'Professional Football'");
$pdo->exec("UPDATE equipments SET image_path = 'uploads/user_pingpong_ball.png' WHERE name = 'Table Tennis Balls'");

// For basketball, fetch a real one from Wikimedia
$url = "https://upload.wikimedia.org/wikipedia/commons/thumb/7/7a/Basketball.png/800px-Basketball.png";
$opts = [
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: FacilityBookingBot/1.0 (test@example.com)\r\n"
    ]
];
$context = stream_context_create($opts);
$imgData = file_get_contents($url, false, $context);

if ($imgData) {
    file_put_contents('c:/laragon/www/facility_booking/uploads/real_basketball.png', $imgData);
    $pdo->exec("UPDATE equipments SET image_path = 'uploads/real_basketball.png' WHERE name LIKE '%Basketball%'");
    echo "Real basketball downloaded and updated.\n";
} else {
    echo "Failed to download real basketball.\n";
}

echo "All 3 items updated to exact balls.\n";
