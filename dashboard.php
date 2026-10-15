<?php
require_once 'db.php';
require_once 'remember.php';

if (!isset($_SESSION['user_id'])) {
    redirect('login.php?error=' . urlencode('Please login first.'));
}

$timeout = 300;

if (time() - ($_SESSION['last_activity'] ?? 0) > $timeout) {
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

    redirect('login.php?error=' . urlencode(
        'Session expired. Please sign in again.'
    ));
}

$_SESSION['last_activity'] = time();

$name = $_SESSION['username'];
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ShieldDesk | Overview</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<aside class="sidebar">
    <a href="dashboard.php" class="side-logo">S<span>.</span> ShieldDesk</a>
    <p class="side-label">WORKSPACE</p>
    <a class="side-link active" href="dashboard.php">▦ &nbsp; Overview</a>

    <?php if ($role === 'admin'): ?>
        <a class="side-link" href="admin_dashboard.php">♙ &nbsp; Administration</a>
    <?php else: ?>
        <a class="side-link" href="student_dashboard.php">▤ &nbsp; Student Space</a>
    <?php endif; ?>

    <div class="sidebar-bottom">
        <p class="side-label">SIGNED IN AS</p>
        <strong><?= e($name) ?></strong>
        <span class="role-text"><?= e(ucfirst($role)) ?></span>
        <a class="logout-btn" href="logout.php">↗ &nbsp; Sign out</a>
    </div>
</aside>

<main class="main-content">
    <header class="page-top">
        <div>
            <p class="overline">SECURE WORKSPACE / OVERVIEW</p>
            <h1>Welcome back, <?= e($name) ?></h1>
            <p class="subtext">Your account and session are active.</p>
        </div>
        <span class="status-badge"><span class="live-dot"></span> Protected</span>
    </header>

    <section class="banner">
        <div>
            <p class="overline">SHIELDDESK SECURITY</p>
            <h2>Access granted.</h2>
            <p>Your identity has been verified successfully.</p>
        </div>
        <div class="shield-symbol">⬡</div>
    </section>

    <h2 class="section-heading">Security overview</h2>

    <section class="stats-grid">
        <article class="stat-card">
            <span class="stat-icon">◈</span>
            <p>Account role</p>
            <h3><?= e(ucfirst($role)) ?></h3>
            <span class="stat-foot">Role-based permissions</span>
        </article>

        <article class="stat-card">
            <span class="stat-icon">◷</span>
            <p>Session timeout</p>
            <h3>05:00 min</h3>
            <span class="stat-foot">Inactivity protection</span>
        </article>

        <article class="stat-card">
            <span class="stat-icon">✓</span>
            <p>Authentication</p>
            <h3>Verified</h3>
            <span class="stat-foot">Password validated</span>
        </article>
    </section>

    <section class="details-panel">
        <div>
            <p class="overline">ACCOUNT DETAILS</p>
            <h2>Identity information</h2>
        </div>
        <div class="detail-row">
            <span>Username</span>
            <strong><?= e($name) ?></strong>
        </div>
        <div class="detail-row">
            <span>Email address</span>
            <strong><?= e($_SESSION['email']) ?></strong>
        </div>
        <div class="detail-row">
            <span>Access level</span>
            <strong><?= e(ucfirst($role)) ?></strong>
        </div>
        <div class="detail-row">
            <span>Session status</span>
            <strong class="green-text">Active</strong>
        </div>
    </section>

    <footer class="page-footer">
        ShieldDesk · Secure access management
    </footer>
</main>
</body>
</html>
