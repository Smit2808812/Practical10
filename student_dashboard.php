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

if ($_SESSION['role'] !== 'student') {
    http_response_code(403);
    exit('403 Forbidden: Students only.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ShieldDesk | Student Space</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="simple-page">
    <a class="back-link" href="dashboard.php">← Overview</a>
    <p class="overline">STUDENT WORKSPACE</p>
    <h1>Student Space</h1>
    <p class="subtext">
        Welcome, <?= e($_SESSION['username']) ?>.
    </p>

    <section class="stats-grid">
        <article class="stat-card">
            <span class="stat-icon">▤</span>
            <p>My learning</p>
            <h3>Student</h3>
            <span class="stat-foot">Student account verified</span>
        </article>
        <article class="stat-card">
            <span class="stat-icon">◷</span>
            <p>Session</p>
            <h3>Active</h3>
            <span class="stat-foot">Inactivity timeout enabled</span>
        </article>
        <article class="stat-card">
            <span class="stat-icon">✓</span>
            <p>Account access</p>
            <h3>Verified</h3>
            <span class="stat-foot">Student permissions</span>
        </article>
    </section>

    <section class="details-panel">
        <h2>Student account</h2>
        <div class="detail-row">
            <span>Name</span>
            <strong><?= e($_SESSION['username']) ?></strong>
        </div>
        <div class="detail-row">
            <span>Email</span>
            <strong><?= e($_SESSION['email']) ?></strong>
        </div>
        <div class="detail-row">
            <span>Role</span>
            <strong>Student</strong>
        </div>
        <p>Your account has access to the student workspace only.</p>
    </section>

    <a class="logout-btn" href="logout.php">Sign out ↗</a>
</main>
</body>
</html>
