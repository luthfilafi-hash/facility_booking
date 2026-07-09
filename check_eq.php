<?php
require 'config.php';
$stmt = $pdo->query('SHOW COLUMNS FROM equipments');
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
