<?php
require 'config.php';
try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->prepare("DELETE FROM bookings WHERE id = 7")->execute();
    echo "Deleted booking 7\n";
} catch (Exception $e) {
    echo "Error deleting booking 7: " . $e->getMessage() . "\n";
}
?>
