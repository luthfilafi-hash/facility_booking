<?php
require 'config.php';
$bookings = $pdo->query("SELECT * FROM bookings")->fetchAll(PDO::FETCH_ASSOC);
$eq_bookings = $pdo->query("SELECT * FROM equipment_bookings")->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['bookings' => $bookings, 'eq_bookings' => $eq_bookings], JSON_PRETTY_PRINT);
?>
