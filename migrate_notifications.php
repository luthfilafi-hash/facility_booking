<?php
require_once __DIR__ . '/config.php';

$sql = "
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created DATETIME DEFAULT CURRENT_TIMESTAMP
);
";
$pdo->exec($sql);
echo "Notifications table created.";
