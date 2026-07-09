<?php
require 'config.php';

// Sync all quantities
$pdo->exec("UPDATE equipments SET available_quantity = total_quantity");
echo "Quantities synced.\n";

$prompts = [
    'football' => 'Single%20professional%20standard%20black%20and%20white%20soccer%20ball,%20studio%20lighting,%20dark%20background,%20photorealistic',
    'baseball_bat' => 'Single%20professional%20wooden%20baseball%20bat,%20studio%20lighting,%20dark%20background,%20photorealistic',
    'yoga_mat' => 'Single%20premium%20rolled%20up%20yoga%20mat,%20studio%20lighting,%20dark%20background,%20photorealistic'
];

foreach ($prompts as $name => $prompt) {
    $url = "https://image.pollinations.ai/prompt/$prompt?width=800&height=600&nologo=true&seed=777";
    $imgData = file_get_contents($url);
    if ($imgData) {
        $path = "uploads/pollination_$name.jpg";
        file_put_contents("c:/laragon/www/facility_booking/" . $path, $imgData);
        
        if ($name == 'football') {
            // Find football ID
            $pdo->exec("UPDATE equipments SET name = 'Professional Football', image_path = '$path' WHERE name LIKE '%Football%'");
        } else if ($name == 'baseball_bat') {
            $pdo->exec("UPDATE equipments SET image_path = '$path' WHERE name LIKE '%Baseball Bat%'");
        } else if ($name == 'yoga_mat') {
            // Check if it already exists
            $stmt = $pdo->query("SELECT id FROM equipments WHERE name = 'Premium Yoga Mat'");
            if (!$stmt->fetch()) {
                $stmt = $pdo->prepare("INSERT INTO equipments (name, description, total_quantity, available_quantity, image_path, condition_status) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    'Premium Yoga Mat',
                    'High quality non-slip yoga mat for indoor workouts and stretching.',
                    10,
                    10,
                    $path,
                    'Good'
                ]);
            } else {
                $pdo->exec("UPDATE equipments SET image_path = '$path' WHERE name = 'Premium Yoga Mat'");
            }
        }
        echo "Updated $name\n";
    } else {
        echo "Failed $name\n";
    }
}
echo "Done.\n";
