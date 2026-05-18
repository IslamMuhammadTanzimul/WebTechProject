<?php
// chef/analytics.php
$page_title = "Chef Analytics - Recipe Sharing Platform";
$base_url = "../";
include "../includes/header.php";
include "../includes/chef_nav.php";
require_once "../config/db_connect.php";
require_role("chef", $base_url);

$chef_id = $_SESSION['user_id'];

// Aggregate stats
$agg_sql = "SELECT
    COUNT(id) AS total_published,
    COALESCE(SUM(view_count),0) AS total_views
    FROM recipes WHERE author_id = ? AND status='published'";
$stmt = $conn->prepare($agg_sql);
$stmt->bind_param("i", $chef_id);
$stmt->execute();
$agg = $stmt->get_result()->fetch_assoc();
$stmt->close();

$follower_count = $conn->query("SELECT COUNT(*) AS c FROM follows WHERE chef_id = $chef_id")->fetch_assoc()['c'];

// Per-recipe analytics
$recipe_sql = "SELECT r.id, r.title, r.view_count, r.is_chef_pick,
    (SELECT COUNT(*) FROM bookmarks b WHERE b.recipe_id=r.id) AS bookmark_count,
    (SELECT COUNT(*) FROM reviews rv WHERE rv.recipe_id=r.id) AS review_count,
    (SELECT AVG(rv2.rating) FROM reviews rv2 WHERE rv2.recipe_id=r.id) AS avg_rating
    FROM recipes r WHERE r.author_id=? AND r.status='published'
    ORDER BY r.view_count DESC";
$stmt = $conn->prepare($recipe_sql);
$stmt->bind_param("i", $chef_id);
$stmt->execute();
$recipes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Top 5 by views for chart
$top5 = array_slice($recipes, 0, 5);

// Most bookmarked
usort($recipes, fn($a,$b) => $b['bookmark_count'] - $a['bookmark_count']);
$most_bookmarked = $recipes[0] ?? null;

// Most reviewed
usort($recipes, fn($a,$b) => $b['review_count'] - $a['review_count']);
$most_reviewed = $recipes[0] ?? null;

// Restore order
usort($recipes, fn($a,$b) => $b['view_count'] - $a['view_count']);

// Follower growth trend (monthly, last 6 months)
$trend_sql = "SELECT DATE_FORMAT(created_at,'%Y-%m') AS month, COUNT(*) AS new_followers FROM follows WHERE chef_id=? AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY month ORDER BY month";
$stmt = $conn->prepare($trend_sql);
$stmt->bind_param("i", $chef_id);
$stmt->execute();
$trend = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="card">
    <h2>📊 Chef Analytics</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin-top:1rem;">
        <div style="text-align:center;padding:1rem;background:#f8f8f8;border-radius:8px;">
            <div style="font-size:2rem;font-weight:700;color:var(--rust);"><?php echo $agg['total_published']; ?></div>
            <div style="font-size:0.9rem;">Published Recipes</div>
        </div>
        <div style="text-align:center;padding:1rem;background:#f8f8f8;border-radius:8px;">
            <div style="font-size:2rem;font-weight:700;color:var(--rust);"><?php echo $agg['total_views']; ?></div>
            <div style="font-size:0.9rem;">Total Views</div>
        </div>
        <div style="text-align:center;padding:1rem;background:#f8f8f8;border-radius:8px;">
            <div style="font-size:2rem;font-weight:700;color:var(--rust);"><?php echo $follower_count; ?></div>
            <div style="font-size:0.9rem;">Followers</div>
        </div>
        <?php if ($most_bookmarked): ?>
        <div style="text-align:center;padding:1rem;background:#f8f8f8;border-radius:8px;">
            <div style="font-size:1.1rem;font-weight:700;color:var(--rust);">🔖 <?php echo $most_bookmarked['bookmark_count']; ?></div>
            <div style="font-size:0.8rem;">Most Bookmarked</div>
            <div style="font-size:0.85rem;color:#555;"><?php echo htmlspecialchars(substr($most_bookmarked['title'],0,25)); ?></div>
        </div>
        <?php endif; ?>
        <?php if ($most_reviewed): ?>
        <div style="text-align:center;padding:1rem;background:#f8f8f8;border-radius:8px;">
            <div style="font-size:1.1rem;font-weight:700;color:var(--rust);">💬 <?php echo $most_reviewed['review_count']; ?></div>
            <div style="font-size:0.8rem;">Most Reviewed</div>
            <div style="font-size:0.85rem;color:#555;"><?php echo htmlspecialchars(substr($most_reviewed['title'],0,25)); ?></div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($top5)): ?>
<div class="card" style="margin-top:1.5rem;">
    <h3>Top 5 Recipes by Views</h3>
    <canvas id="topRecipesChart" style="max-height:300px;"></canvas>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('topRecipesChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [<?php echo implode(',', array_map(fn($r) => '"'.addslashes(substr($r['title'],0,20)).'"', $top5)); ?>],
        datasets: [{
            label: 'Views',
            data: [<?php echo implode(',', array_column($top5, 'view_count')); ?>],
            backgroundColor: 'rgba(184,80,47,0.7)',
            borderColor: 'rgba(184,80,47,1)',
            borderWidth: 1
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
</script>
<?php endif; ?>

<div class="card" style="margin-top:1.5rem;">
    <h3>Per-Recipe Analytics</h3>
    <?php if (empty($recipes)): ?>
        <p>No published recipes yet.</p>
    <?php else: ?>
    <table style="width:100%;border-collapse:collapse;">
        <thead><tr style="border-bottom:2px solid #eee;">
            <th style="text-align:left;padding:0.6rem;">Recipe</th>
            <th>Views</th>
            <th>Bookmarks</th>
            <th>Reviews</th>
            <th>Avg Rating</th>
            <th>Chef's Pick</th>
        </tr></thead>
        <tbody>
        <?php foreach ($recipes as $r): ?>
            <tr style="border-bottom:1px solid #f0f0f0;">
                <td style="padding:0.6rem;"><?php echo htmlspecialchars($r['title']); ?></td>
                <td style="text-align:center;"><?php echo $r['view_count']; ?></td>
                <td style="text-align:center;"><?php echo $r['bookmark_count']; ?></td>
                <td style="text-align:center;"><?php echo $r['review_count']; ?></td>
                <td style="text-align:center;"><?php echo $r['avg_rating'] ? number_format($r['avg_rating'],1).' ⭐' : '—'; ?></td>
                <td style="text-align:center;"><?php echo $r['is_chef_pick'] ? '⭐' : '—'; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php if (!empty($trend)): ?>
<div class="card" style="margin-top:1.5rem;">
    <h3>Follower Growth (Last 6 Months)</h3>
    <canvas id="followerChart" style="max-height:250px;"></canvas>
    <script>
    const ctx2 = document.getElementById('followerChart').getContext('2d');
    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: [<?php echo implode(',', array_map(fn($t) => '"'.$t['month'].'"', $trend)); ?>],
            datasets: [{
                label: 'New Followers',
                data: [<?php echo implode(',', array_column($trend, 'new_followers')); ?>],
                fill: true,
                borderColor: 'rgba(184,80,47,1)',
                backgroundColor: 'rgba(184,80,47,0.1)',
                tension: 0.3
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
    </script>
</div>
<?php endif; ?>

<?php include "../includes/footer.php"; ?>
