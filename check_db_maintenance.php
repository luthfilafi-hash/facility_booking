<?php
require_once __DIR__ . '/config.php';
$fac = $pdo->query("SELECT * FROM facilities WHERE name = 'Badminton Court'")->fetch();
print_r($fac);
