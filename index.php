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

    <div class="card text-center" style="padding: 40px 20px;">
        <h3>User / Home Cook Features</h3>
        <p style="color: var(--text-muted); max-width: 700px; margin: 0 auto;">
            This module includes recipe browsing, AJAX filtering, bookmarks, reviews, shopping lists, meal plans, chef
            following, and personal activity stats.
        </p>
    </div>

</div>

<?php include "includes/footer.php"; ?>