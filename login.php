<?php
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    redirect('dashboard.php');
}

$error = $_GET['error'] ?? '';
$message = $_GET['message'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ShieldDesk | Secure Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">

<div class="login-layout">
    <section class="intro-panel">
        <div class="logo">S<span>.</span></div>
        <p class="overline">SHIELDDESK SECURITY</p>
        <h1>Your space.<br>Protected.</h1>
        <p class="intro-text">
            A secure access portal for students
            and administrators.
        </p>
        <div class="security-note">
            <span class="live-dot"></span>
            Session protection enabled
        </div>
    </section>

    <section class="form-panel">
        <p class="overline">ACCOUNT ACCESS</p>
        <h2>Sign in</h2>
        <p class="subtext">Enter your credentials to continue.</p>

        <?php if ($error !== ''): ?>
            <div class="alert error"><?= e($error) ?></div>
        <?php endif; ?>

        <?php if ($message !== ''): ?>
            <div class="alert success"><?= e($message) ?></div>
        <?php endif; ?>

        <form action="authenticate.php" method="POST">
            <label for="email">Email address</label>
            <input id="email" type="email" name="email"
                placeholder="you@example.com" required autocomplete="username">

            <label for="password">Password</label>
            <input id="password" type="password" name="password"
                placeholder="Enter your password" required autocomplete="current-password">

            <label class="remember-option">
                <input type="checkbox" name="remember" value="1">
                <span>Remember me on this device</span>
            </label>

            <button type="submit" class="primary-btn">
                Secure Sign In <span>→</span>
            </button>
        </form>

        <div class="form-bottom">
            <span class="lock-icon">◆</span>
            Protected access · ShieldDesk
        </div>
    </section>
</div>

</body>
</html>
