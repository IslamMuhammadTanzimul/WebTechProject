<?php
require_once "../includes/auth.php";
require_once "../config/db_connect.php";
require_once "../models/AdminModel.php";

require_admin();

$stats = get_dashboard_stats($conn);

$page_title = "Dashboard";
require_once "../includes/header.php";
?>

<h1>Admin Dashboard</h1>

<div class="stats-grid">
    <div class="stat-box">
        <h3><?php echo $stats['total_users']; ?></h3>
        <p>Total Users</p>
    </div>
    <div class="stat-box">
        <h3><?php echo $stats['total_recipes']; ?></h3>
        <p>Total Recipes</p>
    </div>
    <div class="stat-box">
        <h3><?php echo $stats['active_chefs']; ?></h3>
        <p>Active Chefs</p>
    </div>
    <div class="stat-box">
        <h3><?php echo $stats['new_this_week']; ?></h3>
        <p>New This Week</p>
    </div>
    <div class="stat-box">
        <h3><?php echo $stats['total_reviews']; ?></h3>
        <p>Total Reviews</p>
    </div>
    <div class="stat-box">
        <h3><?php echo $stats['pending_verifs']; ?></h3>
        <p>Pending Verifications</p>
    </div>
</div>

<div class="card">
    <h2>Users by Role</h2>
    <table>
        <thead>
            <tr>
                <th>Role</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stats['users_by_role'] as $role => $count): ?>
                <tr>
                    <td><?php echo ucfirst($role); ?></td>
                    <td><?php echo $count; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once "../includes/footer.php"; ?>