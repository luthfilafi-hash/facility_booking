<?php
require 'config.php';
$items = [
    'baseball' => [17, 'Baseball'],
    'pingpong_paddle' => [12, 'Table Tennis Paddle'],
    'pingpong_ball' => [19, 'Table Tennis Balls']
];

foreach ($items as $name => $item) {
    $id = $item[0];
    $text = urlencode($item[1]);
    $url = "https://placehold.co/800x600/1e293b/ffffff/png?text=$text";
    
    $opts = [ 'http' => [ 'method' => 'GET', 'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n" ] ];
    $context = stream_context_create($opts);
    
    $imgData = file_get_contents($url, false, $context);
    if ($imgData) {
        $path = "uploads/placehold_$name.png";
        file_put_contents("c:/laragon/www/facility_booking/" . $path, $imgData);
        $pdo->exec("UPDATE equipments SET image_path = '$path' WHERE id = $id");
        echo "Updated $name\n";
    }
}
