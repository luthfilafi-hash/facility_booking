<?php
$prompts = [
    'baseball' => 'Single%20white%20baseball%20ball%20with%20red%20stitches,%20studio%20lighting,%20dark%20background',
    'pingpong_paddle' => 'Single%20red%20table%20tennis%20paddle,%20studio%20lighting,%20dark%20background',
    'pingpong_ball' => 'White%20table%20tennis%20balls,%20studio%20lighting,%20dark%20background'
];

foreach ($prompts as $name => $prompt) {
    $url = "https://image.pollinations.ai/prompt/$prompt?width=800&height=600&nologo=true&seed=123";
    $imgData = file_get_contents($url);
    if ($imgData) {
        file_put_contents("c:/laragon/www/facility_booking/uploads/pollination_$name.jpg", $imgData);
        // Update DB directly
        require 'config.php';
        if ($name == 'baseball') {
            $pdo->exec("UPDATE equipments SET image_path = 'uploads/pollination_$name.jpg' WHERE id = 17");
        } else if ($name == 'pingpong_paddle') {
            $pdo->exec("UPDATE equipments SET image_path = 'uploads/pollination_$name.jpg' WHERE id = 12");
        } else if ($name == 'pingpong_ball') {
            $pdo->exec("UPDATE equipments SET image_path = 'uploads/pollination_$name.jpg' WHERE id = 19");
        }
        echo "Downloaded $name\n";
    }
}
