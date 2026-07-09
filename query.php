<?php
require 'config.php';
$stmt = $pdo->query("SELECT * FROM facilities WHERE name = 'Badminton Court'");
print_r($stmt->fetch(PDO::FETCH_ASSOC));
