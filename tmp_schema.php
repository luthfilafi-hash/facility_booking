<?php
require_once __DIR__ . '/config.php';
$stmt = $pdo->query('SHOW COLUMNS FROM users');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
