<?php
require 'config.php';
$stmt = $pdo->query('SHOW COLUMNS FROM notifications');
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
