<?php
// index.php
session_start();
$page_title = "Home - Recipe Sharing Platform";
$base_url = "";
include "includes/header.php";
?>

<div style="flex: 1 0 auto;" class="container">

    <div class="card text-center" style="padding: 60px 20px;">
        <h1 style="font-size: 36px; margin-bottom: 16px;">Welcome to Recipe Sharing Platform</h1>
        <p style="font-size: 18px; color: var(--text-muted); max-width: 600px; margin: 0 auto;">
            Discover recipes, save your favourites, write reviews, create shopping lists, and plan your weekly meals.
        </p>

        <div class="button-group">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="user/dashboard.php" class="btn">Go to Dashboard</a>
            <?php else: ?>
                <a href="register.php" class="btn">Create Account</a>
                <a href="login.php" class="btn-small" style="padding: 10px 20px; font-size: 14px;">Login</a>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php include "includes/footer.php"; ?>