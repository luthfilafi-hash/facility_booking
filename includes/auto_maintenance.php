<?php
// Smart Maintenance Automation
// Runs on every page load to automatically release facilities from maintenance

$expired_stmt = $pdo->query("SELECT id, name FROM facilities WHERE status IN ('maintenance', 'unavailable') AND maintenance_end_time IS NOT NULL AND maintenance_end_time <= NOW()");
$expired_facilities = $expired_stmt->fetchAll();

if (!empty($expired_facilities)) {
    // 1. Reset facility status
    $pdo->query("UPDATE facilities SET status = 'available', maintenance_time = NULL, maintenance_end_time = NULL WHERE status IN ('maintenance', 'unavailable') AND maintenance_end_time IS NOT NULL AND maintenance_end_time <= NOW()");

    // 2. Broadcast notification to all students
    $students = $pdo->query("SELECT id FROM users WHERE role = 'student'")->fetchAll();

    foreach ($expired_facilities as $ef) {
        $title = "Facility Available!";
        $message = "Maintenance for {$ef['name']} is complete. It is now open for bookings.";
        
        $notif = $pdo->prepare("INSERT INTO notifications (user_id, title, message, is_read, created) VALUES (?, ?, ?, 0, NOW())");
        foreach ($students as $student) {
            $notif->execute([$student['id'], $title, $message]);
        }
    }
}

// 3. Auto-Return Equipment from Expired Bookings
// Find all approved equipment bookings where the timeslot has ended
$expired_eq_stmt = $pdo->query("
    SELECT eb.id, eb.equipment_id, eb.quantity, b.user_id, e.name as eq_name 
    FROM equipment_bookings eb 
    JOIN bookings b ON eb.booking_id = b.id 
    JOIN availability a ON b.timeslot_id = a.id 
    JOIN equipments e ON eb.equipment_id = e.id
    WHERE eb.status = 'approved' 
    AND STR_TO_DATE(CONCAT(b.booking_date, ' ', a.end_time), '%Y-%m-%d %H:%i:%s') <= NOW()
");
$expired_eq_bookings = $expired_eq_stmt->fetchAll();

if (!empty($expired_eq_bookings)) {
    $complete_eb = $pdo->prepare("UPDATE equipment_bookings SET status = 'completed', modified = NOW() WHERE id = ?");
    $restore_eq = $pdo->prepare("UPDATE equipments SET available_quantity = available_quantity + ? WHERE id = ?");
    $notif = $pdo->prepare("INSERT INTO notifications (user_id, title, message, is_read, created) VALUES (?, ?, ?, 0, NOW())");
    
    foreach ($expired_eq_bookings as $eb) {
        // Complete the equipment booking
        $complete_eb->execute([$eb['id']]);
        
        // Restore inventory
        $restore_eq->execute([$eb['quantity'], $eb['equipment_id']]);
        
        // Notify user
        $title = "Equipment Automatically Returned";
        $message = "Your booking time has ended. The {$eb['quantity']}x {$eb['eq_name']} you borrowed has been automatically marked as returned.";
        $notif->execute([$eb['user_id'], $title, $message]);
    }
}
