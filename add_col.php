<?php
require 'c:/laragon/www/facility_booking/config.php';
$pdo->exec('ALTER TABLE facilities ADD COLUMN maintenance_end_time DATETIME NULL AFTER maintenance_time');
echo 'Column added.';
