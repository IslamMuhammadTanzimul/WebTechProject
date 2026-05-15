<?php
$page_title = "Admin Panel — Recipe Sharing Platform";
$base_url = "";
include "includes/header.php";
?>

<div class="card">
    <h2>Recipe Sharing Platform — Admin Panel</h2>
    <p>Manage users, recipes, featured content, and platform settings.</p>
    <br>

    <?php if (is_logged_in()) { ?>
        <?php if ($_SESSION['role'] == "admin") { ?>
            <a href="admin/dashboard.php" class="btn">Go to Admin Dashboard</a>
        <?php } else { ?>
            <p>Your account role does not have access to this panel.</p>
        <?php } ?>
    <?php } else { ?>
        <a href="login.php" class="btn">Login</a>
    <?php } ?>
</div>

<?php include "includes/footer.php"; ?>
