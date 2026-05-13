<?php
// user/dashboard.php
$page_title = "User Dashboard - Recipe Sharing Platform";
$base_url = "../";
include "../includes/header.php";

require_role("user", $base_url);
?>

<div class="card">
    <h2>Welcome back,
        <?php echo htmlspecialchars($_SESSION["name"]); ?>! 🍳
    </h2>
    <p>This is your personal dashboard. From here, you can browse recipes, manage your meal plans, and view your
        activity.</p>
</div>

<div class="card">
    <h3>Your Quick Stats</h3>
    <ul>
        <li><strong>Recipes Bookmarked:</strong> 0 (Coming Soon)</li>
        <li><strong>Reviews Written:</strong> 0 (Coming Soon)</li>
        <li><strong>Meal Plans Created:</strong> 0 (Coming Soon)</li>
    </ul>
</div>

<?php include "../includes/footer.php"; ?>