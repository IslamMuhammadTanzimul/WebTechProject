<?php
$page_title = "Home - Recipe Sharing Platform";
$base_url = "";
include "includes/header.php";
?>

<div class="card">
    <h2>Welcome to Recipe Sharing Platform</h2>
    <p>
        Discover recipes, save your favourites, write reviews, create shopping lists,
        and plan your weekly meals.
    </p>
    <br>

    <?php if (is_logged_in()) { ?>
        <?php if ($_SESSION['role'] == "user") { ?>
            <a href="user/dashboard.php" class="btn">Go to Dashboard</a>
        <?php } elseif ($_SESSION['role'] == "chef") { ?>
            <a href="chef/dashboard.php" class="btn">Go to Chef Dashboard</a>
        <?php } elseif ($_SESSION['role'] == "moderator") { ?>
            <a href="moderator/dashboard.php" class="btn">Go to Moderator Dashboard</a>
        <?php } elseif ($_SESSION['role'] == "admin") { ?>
            <a href="admin/dashboard.php" class="btn">Go to Admin Dashboard</a>
        <?php } ?>
    <?php } else { ?>
        <a href="register.php" class="btn">Create Account</a>
        <a href="login.php" class="btn">Login</a>
    <?php } ?>
</div>

<div class="card">
    <h3>User/Home Cook Features</h3>
    <p>
        This module will include recipe browsing, AJAX filtering, bookmarks,
        reviews, shopping lists, meal plans, chef following, and personal activity stats.
    </p>
</div>

<?php include "includes/footer.php"; ?>