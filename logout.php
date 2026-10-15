
<?php
require_once 'db.php';

$cookie = $_COOKIE['shield_remember'] ?? '';
$parts = explode(':', $cookie);

if (count($parts) === 2 && ctype_xdigit($parts[0])) {
    $selector = $parts[0];

    $stmt = $conn->prepare(
        'DELETE FROM remember_tokens WHERE selector = ?'
    );
    $stmt->bind_param('s', $selector);
    $stmt->execute();
    $stmt->close();
}

setcookie('shield_remember', '', [
    'expires' => time() - 3600,
    'path' => '/',
    'secure' => !empty($_SERVER['HTTPS'])
        && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax'
]);

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    setcookie(session_name(), '', [
        'expires' => time() - 3600,
        'path' => $params['path'],
        'domain' => $params['domain'],
        'secure' => $params['secure'],
        'httponly' => $params['httponly'],
        'samesite' => 'Lax'
    ]);
}

session_destroy();

redirect('login.php?message=' . urlencode(
    'You have been signed out successfully.'
));
?>
