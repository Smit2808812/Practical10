<?php
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    return;
}

$cookie = $_COOKIE['shield_remember'] ?? '';
$parts = explode(':', $cookie);

if (count($parts) !== 2) {
    return;
}

[$selector, $validator] = $parts;

if (!ctype_xdigit($selector) || !ctype_xdigit($validator)) {
    return;
}

$stmt = $conn->prepare(
    'SELECT rt.user_id, rt.token_hash, u.username,
            u.email, u.role
     FROM remember_tokens rt
     JOIN users u ON u.id = rt.user_id
     WHERE rt.selector = ? AND rt.expires_at > NOW()
     LIMIT 1'
);
$stmt->bind_param('s', $selector);
$stmt->execute();
$record = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$record ||
    !hash_equals(
        $record['token_hash'],
        hash('sha256', $validator)
    )) {

    $delete = $conn->prepare(
        'DELETE FROM remember_tokens WHERE selector = ?'
    );
    $delete->bind_param('s', $selector);
    $delete->execute();
    $delete->close();

    setcookie('shield_remember', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => !empty($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== 'off'
    ]);
    return;
}

session_regenerate_id(true);

$_SESSION['user_id'] = (int)$record['user_id'];
$_SESSION['username'] = $record['username'];
$_SESSION['email'] = $record['email'];
$_SESSION['role'] = $record['role'];
$_SESSION['last_activity'] = time();
?>
