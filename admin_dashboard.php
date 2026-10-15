<?php
require_once 'db.php';
require_once 'remember.php';

if (!isset($_SESSION['user_id'])) {
    redirect('login.php?error=' . urlencode('Please login first.'));
}

if (time() - ($_SESSION['last_activity'] ?? 0) > 300) {
    $_SESSION = [];
    session_destroy();
    redirect('login.php?error=' . urlencode('Session expired.'));
}

$_SESSION['last_activity'] = time();

if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('403 Forbidden: Administrators only.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ShieldDesk | Administration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="simple-page">
    <a class="back-link" href="dashboard.php">← Overview</a>
    <p class="overline">PRIVILEGED ACCESS</p>
    <h1>Administration Center</h1>
    <p class="subtext">
        Welcome, <?= e($_SESSION['username']) ?>.
    </p>

    <section class="stats-grid">
        <article class="stat-card">
            <span class="stat-icon">♙</span>
            <p>Account management</p>
            <h3>Administrator</h3>
            <span class="stat-foot">Restricted access</span>
        </article>
        <article class="stat-card">
            <span class="stat-icon">◈</span>
            <p>Access control</p>
            <h3>Enabled</h3>
            <span class="stat-foot">Role verified</span>
        </article>
        <article class="stat-card">
            <span class="stat-icon">✓</span>
            <p>Session security</p>
            <h3>Active</h3>
            <span class="stat-foot">Timeout protection</span>
        </article>
    </section>

    <section class="details-panel">
        <h2>Administrator permissions</h2>
        <p>You have successfully passed the admin role check.</p>
        <p>Only authenticated administrators can access this page.</p>
    </section>

    <a class="logout-btn" href="logout.php">Sign out ↗</a>
</main>
</body>
</html>
