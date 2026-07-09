<?php
require 'config.php';
$eq = $pdo->query("SELECT id, name, available_quantity, total_quantity FROM equipments")->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($eq, JSON_PRETTY_PRINT);
?>
