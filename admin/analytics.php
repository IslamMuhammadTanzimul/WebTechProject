<?php
$page_title = "Analytics — Recipe Sharing Platform";
$base_url = "../";
include "../includes/header.php";

require_once "../config/db_connect.php";
require_once "../includes/auth.php";
require_role('admin', '../');
require_once "../models/RecipeModel.php";
require_once "../models/UserModel.php";
require_once "../models/BookmarkModel.php";
require_once "../models/ReviewModel.php";

$byCuisine = getRecipeCountByCuisine($conn);
$topChefs = getMostFollowedChefs($conn);

?>
<h2>Platform Analytics</h2>

<div class="detail-grid">
    <div class="card">
        <h3>Recipes by Cuisine</h3>
        <table class="info-table">
            <thead>
                <tr>
                    <th>Cuisine</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody><?php while ($r = $byCuisine->fetch_assoc()): ?><tr>
                        <td><?php echo htmlspecialchars($r['name']); ?></td>
                        <td><?php echo $r['count']; ?></td>
                    </tr><?php endwhile; ?></tbody>
        </table>
    </div>
    <div class="card">
        <h3>Most Active Users</h3>
        <table class="info-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>R</th>
                    <th>Rv</th>
                    <th>Bm</th>
                </tr>
            </thead>
            <tbody><?php while ($u = $mostActive->fetch_assoc()): ?><tr>
                        <td><?php echo htmlspecialchars($u['username']); ?></td>
                        <td><?php echo $u['role']; ?></td>
                        <td><?php echo $u['recipe_count']; ?></td>
                        <td><?php echo $u['review_count']; ?></td>
                        <td><?php echo $u['bookmark_count']; ?></td>
                    </tr><?php endwhile; ?></tbody>
        </table>
    </div>
</div>
<div class="detail-grid">
    <div class="card">
        <h3>Top Chefs</h3>
        <table class="info-table">
            <thead>
                <tr>
                    <th>Chef</th>
                    <th>Followers</th>
                </tr>
            </thead>
            <tbody><?php while ($c = $topChefs->fetch_assoc()): ?><tr>
                        <td><?php echo htmlspecialchars($c['name'] ?? $c['username']); ?></td>
                        <td><?php echo $c['follower_count']; ?></td>
                    </tr><?php endwhile; ?></tbody>
        </table>
    </div>
    <div class="card">
        <h3>Avg Rating</h3>
        <table class="info-table">
            <thead>
                <tr>
                    <th>Cuisine</th>
                    <th>Rating</th>
                </tr>
            </thead>
            <tbody><?php while ($r = $avgRating->fetch_assoc()): ?><tr>
                        <td><?php echo htmlspecialchars($r['name']); ?></td>
                        <td><?php echo number_format($r['avg_rating'], 1); ?>/5</td>
                    </tr><?php endwhile; ?></tbody>
        </table>
    </div>
</div>
<div class="card">
    <h3>Bookmark Trends</h3>
    <table class="info-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Bookmarks</th>
            </tr>
        </thead>
        <tbody><?php while ($b = $bookmarks->fetch_assoc()): ?><tr>
                    <td><?php echo $b['date']; ?></td>
                    <td><?php echo $b['count']; ?></td>
                </tr><?php endwhile; ?></tbody>
    </table>
</div>
<?php include "../includes/footer.php"; ?>