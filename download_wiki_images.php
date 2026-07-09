<?php
function getWikiImage($title) {
    $url = "https://en.wikipedia.org/w/api.php?action=query&titles=" . urlencode($title) . "&prop=pageimages&format=json&pithumbsize=800";
    $opts = [ 'http' => [ 'method' => 'GET', 'header' => "User-Agent: FacilityBookingBot/1.0 (test@example.com)\r\n" ] ];
    $context = stream_context_create($opts);
    $json = file_get_contents($url, false, $context);
    $data = json_decode($json, true);
    if (isset($data['query']['pages'])) {
        foreach ($data['query']['pages'] as $page) {
            if (isset($page['thumbnail']['source'])) {
                return $page['thumbnail']['source'];
            }
        }
    }
    return null;
}

$items = [
    'Indoor Volleyball' => ['query' => 'Volleyball_(ball)', 'id' => 6],
    'Badminton Racket' => ['query' => 'Badminton_racket', 'id' => 1],
    'Baseball' => ['query' => 'Baseball_(ball)', 'id' => 17],
    'Professional Basketball' => ['query' => 'Basketball_(ball)', 'id' => 3],
    'Table Tennis Paddle' => ['query' => 'Table_tennis_racket', 'id' => 12],
    'Table Tennis Balls' => ['query' => 'Table_tennis', 'id' => 19]
];

require 'config.php';
$pdo->beginTransaction();

foreach ($items as $name => $item) {
    $imgUrl = getWikiImage($item['query']);
    if ($imgUrl) {
        // Download the image
        $ext = pathinfo(parse_url($imgUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
        $filename = "wiki_" . $item['id'] . "." . $ext;
        $localPath = "c:/laragon/www/facility_booking/uploads/" . $filename;
        $opts = [ 'http' => [ 'method' => 'GET', 'header' => "User-Agent: FacilityBookingBot/1.0 (test@example.com)\r\n" ] ];
        $context = stream_context_create($opts);
        file_put_contents($localPath, file_get_contents($imgUrl, false, $context));
        
        // Update database
        $pdo->exec("UPDATE equipments SET image_path = 'uploads/$filename' WHERE id = " . $item['id']);
        echo "Updated $name\n";
    } else {
        echo "Failed to find image for $name\n";
    }
}

$pdo->commit();
echo "Done\n";
