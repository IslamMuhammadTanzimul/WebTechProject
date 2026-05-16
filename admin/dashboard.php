<?php
require_once "../includes/auth.php";
require_once "../config/db_connect.php";

require_admin();

$page_title = "Dashboard";
require_once "../includes/header.php";
?>

<h1>Admin Dashboard</h1>

<div class="stats-grid">
    <div class="stat-box">
        <h3>-</h3>
        <p>Total Users</p>
    </div>
    <div class="stat-box">
        <h3>-</h3>
        <p>Total Recipes</p>
    </div>
    <div class="stat-box">
        <h3>-</h3>
        <p>Active Chefs</p>
    </div>
    <div class="stat-box">
        <h3>-</h3>
        <p>New This Week</p>
    </div>
    <div class="stat-box">
        <h3>-</h3>
        <p>Total Reviews</p>
    </div>
    <div class="stat-box">
        <h3>-</h3>
        <p>Pending Verifications</p>
    </div>
</div>

<?php require_once "../includes/footer.php"; ?>