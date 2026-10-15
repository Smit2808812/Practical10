
<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.php');
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)
    || $password === '') {
    redirect('login.php?error=' . urlencode(
        'Enter a valid email and password.'
    ));
}

$stmt = $conn->prepare(
    'SELECT id, username, email, password, role
     FROM users WHERE email = ? LIMIT 1'
);
$stmt->bind_param('s', $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user || !password_verify($password, $user['password'])) {
    redirect('login.php?error=' . urlencode(
        'Invalid email or password.'
    ));
}

if (!in_array($user['role'], ['admin', 'student'], true)) {
    redirect('login.php?error=' . urlencode('Invalid role.'));
}

session_regenerate_id(true);

$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['email'] = $user['email'];
$_SESSION['role'] = $user['role'];
$_SESSION['last_activity'] = time();

$update = $conn->prepare(
    'UPDATE users SET last_login = NOW() WHERE id = ?'
);
$update->bind_param('i', $_SESSION['user_id']);
$update->execute();
$update->close();

if (isset($_POST['remember'])) {
    $selector = bin2hex(random_bytes(12));
    $validator = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $validator);
    $expires = time() + (30 * 24 * 60 * 60);
    $expiresSql = date('Y-m-d H:i:s', $expires);
    $uid = $_SESSION['user_id'];

    $save = $conn->prepare(
        'INSERT INTO remember_tokens
         (user_id, selector, token_hash, expires_at)
         VALUES (?, ?, ?, ?)'
    );
    $save->bind_param(
        'isss',
        $uid,
        $selector,
        $tokenHash,
        $expiresSql
    );
    $save->execute();
    $save->close();

    setcookie('shield_remember', $selector . ':' . $validator, [
        'expires' => $expires,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

redirect('dashboard.php');
?>
