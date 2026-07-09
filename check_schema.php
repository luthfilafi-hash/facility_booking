<?php
require_once __DIR__ . '/includes/db.php';
$stmt = $pdo->query("SHOW COLUMNS FROM users");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($columns);
