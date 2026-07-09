<?php
$video_url = 'https://cdn.pixabay.com/video/2016/09/21/5323-183786489_tiny.mp4';
$target_dir = __DIR__ . '/videos';
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}
$target_file = $target_dir . '/sports.mp4';
$ch = curl_init($video_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$video_data = curl_exec($ch);
if ($video_data !== false) {
    file_put_contents($target_file, $video_data);
    echo "Video downloaded successfully.";
} else {
    echo "Failed to download video. Error: " . curl_error($ch);
}
curl_close($ch);
?>
