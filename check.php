<?php
require 'config.php';
$stmt = $pdo->query('SELECT id, name, status FROM facilities');
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($results);
