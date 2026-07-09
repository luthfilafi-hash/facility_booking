<?php
require 'config.php';
$stmt = $pdo->query("DESCRIBE equipments");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($cols);
