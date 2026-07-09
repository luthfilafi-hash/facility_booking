<?php
require 'config.php';

// Helper to get the image path by prefix
function getImg($prefix) {
    $files = glob("c:/laragon/www/facility_booking/uploads/" . $prefix . "_*.png");
    if (!empty($files)) {
        return "uploads/" . basename($files[0]);
    }
    return null;
}

try {
    $pdo->beginTransaction();

    // 1. Badminton
    $racket = getImg("badminton_racket");
    $shuttle = getImg("shuttlecock");
    $pdo->exec("UPDATE equipments SET name = 'Badminton Racket', image_path = '$racket' WHERE id = 1");
    if ($shuttle) {
        $pdo->exec("INSERT INTO equipments (facility_id, name, total_quantity, available_quantity, image_path) VALUES (3, 'Shuttlecock', 10, 10, '$shuttle')");
    }

    // 2. Football
    $football = getImg("football");
    if ($football) $pdo->exec("UPDATE equipments SET image_path = '$football' WHERE id = 2");

    // 3. Basketball
    $basketball = getImg("basketball");
    if ($basketball) $pdo->exec("UPDATE equipments SET image_path = '$basketball' WHERE id = 3");

    // 4. Tennis
    $tennis_racket = getImg("tennis_racket");
    $tennis_balls = getImg("tennis_balls");
    $pdo->exec("UPDATE equipments SET name = 'Tennis Racket', image_path = '$tennis_racket' WHERE id = 4");
    if ($tennis_balls) {
        $pdo->exec("INSERT INTO equipments (facility_id, name, total_quantity, available_quantity, image_path) VALUES (2, 'Tennis Balls', 12, 12, '$tennis_balls')");
    }

    // 5. Track Spikes
    $track = getImg("track_spikes");
    if ($track) $pdo->exec("UPDATE equipments SET image_path = '$track' WHERE id = 5");

    // 6. Volleyball
    $volley = getImg("volleyball");
    if ($volley) $pdo->exec("UPDATE equipments SET image_path = '$volley' WHERE id = 6");

    // 7. Swimming
    $goggles = getImg("swimming_goggles");
    $cap = getImg("swimming_cap");
    $pdo->exec("UPDATE equipments SET name = 'Swimming Goggles', image_path = '$goggles' WHERE id = 7");
    if ($cap) {
        $pdo->exec("INSERT INTO equipments (facility_id, name, total_quantity, available_quantity, image_path) VALUES (7, 'Swimming Cap', 15, 15, '$cap')");
    }

    // 8. Dumbbell
    $dumb = getImg("dumbbell_set");
    if ($dumb) $pdo->exec("UPDATE equipments SET image_path = '$dumb' WHERE id = 8");

    // 9. Squash
    $sq_racket = getImg("squash_racket");
    $sq_ball = getImg("squash_ball");
    $pdo->exec("UPDATE equipments SET name = 'Squash Racket', image_path = '$sq_racket' WHERE id = 9");
    if ($sq_ball) {
        $pdo->exec("INSERT INTO equipments (facility_id, name, total_quantity, available_quantity, image_path) VALUES (9, 'Double Dot Squash Ball', 8, 8, '$sq_ball')");
    }

    // 10. Baseball
    $bat = getImg("baseball_bat");
    $ball = getImg("baseball");
    $pdo->exec("UPDATE equipments SET name = 'Wooden Baseball Bat', image_path = '$bat' WHERE id = 10");
    if ($ball) {
        $pdo->exec("INSERT INTO equipments (facility_id, name, total_quantity, available_quantity, image_path) VALUES (10, 'Baseball', 5, 5, '$ball')");
    }

    // 11. Golf
    $golf_driver = getImg("golf_driver");
    $golf_balls = getImg("golf_balls");
    $pdo->exec("UPDATE equipments SET name = 'Premium Golf Driver', image_path = '$golf_driver' WHERE id = 11");
    if ($golf_balls) {
        $pdo->exec("INSERT INTO equipments (facility_id, name, total_quantity, available_quantity, image_path) VALUES (11, 'Golf Balls', 4, 4, '$golf_balls')");
    }

    // 12. Table Tennis
    $pdo->exec("UPDATE equipments SET name = 'Table Tennis Paddle' WHERE id = 12");
    $pdo->exec("INSERT INTO equipments (facility_id, name, total_quantity, available_quantity, image_path) VALUES (12, 'Table Tennis Balls', 10, 10, 'uploads/pingpong_eq.png')");

    $pdo->commit();
    echo "Done";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
