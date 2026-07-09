<?php
$prompts = [
    'badminton' => 'Single%20professional%20badminton%20racket%20standing%20alone,%20studio%20lighting,%20dark%20background',
    'baseball' => 'Single%20professional%20white%20baseball%20ball%20with%20red%20stitches,%20studio%20lighting,%20dark%20background',
    'pingpong_paddle' => 'Single%20professional%20table%20tennis%20paddle,%20studio%20lighting,%20dark%20background',
    'pingpong_ball' => 'White%20table%20tennis%20balls,%20studio%20lighting,%20dark%20background'
];

foreach ($prompts as $name => $prompt) {
    $url = "https://image.pollinations.ai/prompt/$prompt?width=800&height=600&nologo=true";
    $imgData = file_get_contents($url);
    if ($imgData) {
        file_put_contents("c:/laragon/www/facility_booking/uploads/pollination_$name.jpg", $imgData);
        echo "Downloaded $name\n";
    } else {
        echo "Failed $name\n";
    }
}
