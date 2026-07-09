<?php
require 'config.php';
$pdo->exec("ALTER TABLE bookings MODIFY notes TEXT NULL DEFAULT NULL");
echo "Fixed bookings table.\n";
