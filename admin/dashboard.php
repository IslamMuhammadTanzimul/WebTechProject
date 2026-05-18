<?php
require_once "../includes/auth.php";
require_once "../config/db_connect.php";
/** @var mysqli $conn */
require_once "../models/AdminModel.php";

require_admin();

$stats = get_dashboard_stats($conn);

$page_title = "Dashboard";
require_once "../includes/header.php";
?>

<h1>Admin Dashboard</h1>

<div class="stats-grid">
    <div class="stat-box">
        <h3 id="stat-total-users"><?php echo $stats['total_users']; ?></h3>
        <p>Total Users</p>
    </div>
    <div class="stat-box">
        <h3 id="stat-total-recipes"><?php echo $stats['total_recipes']; ?></h3>
        <p>Total Recipes</p>
    </div>
    <div class="stat-box">
        <h3 id="stat-active-chefs"><?php echo $stats['active_chefs']; ?></h3>
        <p>Active Chefs</p>
    </div>
    <div class="stat-box">
        <h3 id="stat-new-this-week"><?php echo $stats['new_this_week']; ?></h3>
        <p>New This Week</p>
    </div>
    <div class="stat-box">
        <h3 id="stat-total-reviews"><?php echo $stats['total_reviews']; ?></h3>
        <p>Total Reviews</p>
    </div>
    <div class="stat-box">
        <h3 id="stat-pending-verifs"><?php echo $stats['pending_verifs']; ?></h3>
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

<script>
    function refreshStats() {
        fetch('/WebTechProject/api/admin/stats.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('stat-total-users').textContent = data.total_users;
                document.getElementById('stat-total-recipes').textContent = data.total_recipes;
                document.getElementById('stat-active-chefs').textContent = data.active_chefs;
                document.getElementById('stat-new-this-week').textContent = data.new_this_week;
                document.getElementById('stat-total-reviews').textContent = data.total_reviews;
                document.getElementById('stat-pending-verifs').textContent = data.pending_verifs;
            })
            .catch(err => console.error('Stats refresh failed:', err));
    }

    // refresh every 30 seconds
    setInterval(refreshStats, 30000);
</script>

<?php require_once "../includes/footer.php"; ?>