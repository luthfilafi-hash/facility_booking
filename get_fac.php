<?php
require 'config.php';
$stmt = $pdo->query('SELECT id, name FROM facilities');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
