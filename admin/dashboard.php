<?php
$page_title = "Admin Dashboard — Recipe Sharing Platform";
$base_url = "../";
include "../includes/header.php";

require_once "../config/db_connect.php";
require_once "../includes/auth.php";
require_role('admin', '../');
require_once "../models/StatsModel.php";

$stats = getDashboardStats($conn);
?>

<h2>Admin Dashboard</h2>

<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-number"><?php echo $stats['total_users']; ?></span>
        <span class="stat-label">Total Users</span>
        <div class="stat-breakdown">
            <?php echo $stats['users_by_role']['user']; ?> users <br>
            <?php echo $stats['users_by_role']['chef']; ?> chefs <br>
            <?php echo $stats['users_by_role']['moderator']; ?> mods <br>
            <?php echo $stats['users_by_role']['admin']; ?> admins
        </div>
    </div>
    <div class="stat-card">
        <span class="stat-number"><?php echo $stats['published_recipes']; ?></span>
        <span class="stat-label">Published Recipes</span>
    </div>
    <div class="stat-card">
        <span class="stat-number"><?php echo $stats['active_chefs']; ?></span>
        <span class="stat-label">Active Chefs</span>
    </div>
    <div class="stat-card">
        <span class="stat-number"><?php echo $stats['new_this_week']; ?></span>
        <span class="stat-label">New This Week</span>
    </div>
    <div class="stat-card">
        <span class="stat-number"><?php echo $stats['total_reviews']; ?></span>
        <span class="stat-label">Total Reviews</span>
    </div>
    <div class="stat-card">
        <span class="stat-number"><?php echo $stats['pending_verifications']; ?></span>
        <span class="stat-label">Pending Verifications</span>
    </div>
</div>

<div class="card">
    <h3>Quick Actions</h3>
    <div class="quick-actions">
        <a href="users.php" class="btn">Manage Users</a>
        <a href="recipes.php" class="btn">Manage Recipes</a>
        <a href="featured.php" class="btn">Featured Content</a>
        <a href="announcements.php" class="btn">Post Announcement</a>
    </div>
</div>

<div class="card">
    <h3>Recent Users <small>(via AJAX)</small></h3>
    <div id="recentUsers">
        <p>Loading...</p>
    </div>
</div>

<?php include "../includes/footer.php"; ?>