<?php
$files = [
    'admin/availability.php',
    'admin/bookings.php',
    'calendar.php',
    'generate_availability.php',
    'includes/sidebar.php',
    'staff/bookings.php',
    'student/book.php',
    'student/index.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "File not found: $file\n";
        continue;
    }
    
    $content = file_get_contents($file);
    
    // Case sensitive replacements
    $content = str_replace('timeslots.php', 'availability.php', $content);
    $content = str_replace('timeslot_id', 'timeslot_id', $content); // Wait, timeslot_id in DB is NOT renamed. Wait!
    // I only renamed the table `timeslots`. Did I rename the `timeslot_id` column in `bookings`? No!
    // Let's hold on. `SELECT t.* FROM availability t ON b.timeslot_id = t.id` works perfectly.
    // So I just need to replace the word `timeslots` with `availability`.
    $content = str_replace('timeslots', 'availability', $content);
    $content = str_replace('Timeslots', 'Availability', $content);
    
    // Exceptions: if any variables were named $timeslots, they are now $availability, which is fine.
    
    file_put_contents($file, $content);
    echo "Updated $file\n";
}
