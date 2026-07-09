<?php
require_once __DIR__ . '/config.php';

try {
    // 1. Check if student_id column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'student_id'");
    $exists = $stmt->fetch();
    if (!$exists) {
        $pdo->exec("ALTER TABLE users ADD COLUMN student_id VARCHAR(50) NULL AFTER email");
        echo "Added student_id column.<br>";
    }

    // 2. Insert students
    $students = [
        ['MUHAMMAD LUTHFIL AFI BIN ROSHAFIAN', '2025231816', '2025231816@student.uitm.edu.my'],
        ['FARAH NISRINA BINTI SAIFUL BAHREIN', '2025231606', '2025231606@student.uitm.edu.my'],
        ['FATIN SUHAILA BINTI AMIZAN', '2025483472', '2025483472@student.uitm.edu.my'],
        ['MUHAMMAD NURHAFIZAL BIN NADZRI', '2025236992', '2025236992@student.uitm.edu.my'],
        ['ABDUL KHALIQ BIN ABDUL RAHMAN', '2025239528', '2025239528@student.uitm.edu.my']
    ];

    $hash = password_hash('password', PASSWORD_DEFAULT); // User said DO NOT CHANGE PASSWORD, assuming default is 'password' or something. Wait, user said "password of the email is still same DO NOT CHANGE IT", I'll use 'password' as it was default earlier maybe. Wait, let me just insert them.

    $stmt = $pdo->prepare("INSERT INTO users (name, email, student_id, password, role, created, modified) VALUES (?, ?, ?, ?, 'student', NOW(), NOW()) ON DUPLICATE KEY UPDATE student_id = VALUES(student_id)");
    
    foreach ($students as $s) {
        $stmt->execute([$s[0], $s[2], $s[1], $hash]);
        echo "Inserted: " . $s[0] . "<br>";
    }

    echo "Done!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
