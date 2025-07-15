<?php
include 'includes/db.php';

$users = [
    [
        'username' => 'admin',
        'password' => password_hash('admin123', PASSWORD_DEFAULT),
        'email' => 'admin@example.com',
        'role' => 'admin'
    ],
    [
        'username' => 'customer',
        'password' => password_hash('customer123', PASSWORD_DEFAULT),
        'email' => 'customer@example.com',
        'role' => 'customer'
    ]
];

foreach ($users as $user) {
    $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $user['username'], $user['password'], $user['email'], $user['role']);
    $stmt->execute();
}

echo "✅ Admin and Customer created successfully.";
?>
