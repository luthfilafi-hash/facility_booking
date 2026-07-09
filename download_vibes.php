<?php
$prompts = [
    'volleyball' => 'Single%20professional%20indoor%20volleyball%20ball,%20studio%20lighting,%20dark%20background',
    'basketball' => 'Single%20professional%20indoor%20basketball%20ball,%20studio%20lighting,%20dark%20background'
];

foreach ($prompts as $name => $prompt) {
    $url = "https://image.pollinations.ai/prompt/$prompt?width=800&height=600&nologo=true&seed=456";
    
    // Pollinations can be slow, let's just use file_get_contents
    $imgData = file_get_contents($url);
    if ($imgData) {
        $path = "uploads/pollination_vibe_$name.jpg";
        file_put_contents("c:/laragon/www/facility_booking/" . $path, $imgData);
        
        require_once 'config.php';
        if ($name == 'volleyball') {
            $pdo->exec("UPDATE equipments SET image_path = '$path' WHERE id = 6");
        } else if ($name == 'basketball') {
            $pdo->exec("UPDATE equipments SET image_path = '$path' WHERE id = 3");
        }
        echo "Downloaded and updated $name\n";
    } else {
        echo "Failed $name\n";
    }
}
