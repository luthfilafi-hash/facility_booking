<?php
require 'config.php';
$stmt = $pdo->query('SELECT * FROM equipments');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
