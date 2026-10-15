
<?php
require_once 'db.php';

$accounts = [
    [
        'Shield Admin',
        'admin@shielddesk.test',
        'Admin@123',
        'admin'
    ],
    [
        'Demo Student',
        'student@shielddesk.test',
        'Student@123',
        'student'
    ]
];

$stmt = $conn->prepare(
    "INSERT INTO users (username, email, password, role)
     VALUES (?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE
     username = VALUES(username),
     password = VALUES(password),
     role = VALUES(role)"
);

foreach ($accounts as $account) {
    [$name, $email, $plainPassword, $role] = $account;
    $hash = password_hash($plainPassword, PASSWORD_DEFAULT);

    $stmt->bind_param(
        'ssss',
        $name,
        $email,
        $hash,
        $role
    );

    $stmt->execute();
}

echo 'Demo accounts created successfully.';
?>
