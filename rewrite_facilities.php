<?php
require 'config.php';

$facilities = [
    1 => ['name' => 'Main Basketball Court', 'desc' => 'Professional full-size indoor basketball court with glossy wood flooring.', 'loc' => 'Sports Complex', 'img' => 'images/basketball.png', 'cap' => 20],
    2 => ['name' => 'Tennis Court', 'desc' => 'Modern outdoor hard surface tennis court.', 'loc' => 'Outdoor Arena', 'img' => 'images/tennis.png', 'cap' => 4],
    3 => ['name' => 'Badminton Court', 'desc' => 'Standard indoor badminton court with professional nets.', 'loc' => 'Sports Hall', 'img' => 'images/badminton.png', 'cap' => 4],
    4 => ['name' => 'Running Track', 'desc' => 'Outdoor professional running track with stadium seating.', 'loc' => 'Outdoor Arena', 'img' => 'images/track.png', 'cap' => 100],
    5 => ['name' => 'Football Field', 'desc' => 'Outdoor grass football field with bright floodlights.', 'loc' => 'Outdoor Arena', 'img' => 'images/football.png', 'cap' => 22],
    6 => ['name' => 'Volleyball Court', 'desc' => 'Modern indoor volleyball court with high ceilings.', 'loc' => 'Sports Hall', 'img' => 'images/volleyball.png', 'cap' => 12],
    7 => ['name' => 'Swimming Pool', 'desc' => 'Olympic size indoor swimming pool with clear racing lanes.', 'loc' => 'Aquatics Center', 'img' => 'images/swimming.png', 'cap' => 50],
    8 => ['name' => 'Gymnasium', 'desc' => 'Modern indoor fitness center with premium workout equipment.', 'loc' => 'Sports Complex', 'img' => 'images/gym.png', 'cap' => 40],
    9 => ['name' => 'Squash Court', 'desc' => 'Indoor squash court with bright white walls and red boundary lines.', 'loc' => 'Sports Hall', 'img' => 'images/squash.png', 'cap' => 2],
    11 => ['name' => 'Baseball Field', 'desc' => 'Professional outdoor baseball field with manicured grass.', 'loc' => 'Outdoor Arena', 'img' => 'images/baseball.png', 'cap' => 36],
];

// First, check if capacity column exists, if not, create it.
try {
    $pdo->exec("ALTER TABLE facilities ADD COLUMN capacity INT DEFAULT NULL");
} catch (Exception $e) {
    // Column might already exist, ignore.
}

foreach ($facilities as $id => $f) {
    $stmt = $pdo->prepare("UPDATE facilities SET name = ?, description = ?, location = ?, image_path = ?, capacity = ? WHERE id = ?");
    $stmt->execute([$f['name'], $f['desc'], $f['loc'], $f['img'], $f['cap'], $id]);
}

echo "Database successfully updated with 10 distinct facilities!";
