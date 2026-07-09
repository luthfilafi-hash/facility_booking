<?php
require_once __DIR__ . '/config.php';
$slots = $pdo->query("SELECT * FROM availability WHERE facility_id = 3")->fetchAll();
print_r($slots);
