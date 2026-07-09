<?php
require 'config.php';
$stmt = $pdo->query("DESCRIBE audit_logs");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($columns);
