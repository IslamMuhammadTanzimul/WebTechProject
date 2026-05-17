<?php
// chef/dashboard.php
$page_title = "Chef Dashboard - Recipe Sharing Platform";
$base_url = "../";
include "../includes/header.php";
include "../includes/chef_nav.php";
require_once "../config/db_connect.php";
require_role("chef", $base_url);

$chef_id = $_SESSION['user_id'];

// Aggregate stats
$stats_sql = "SELECT
    COUNT(r.id) AS total_recipes,
    COALESCE(SUM(r.view_count), 0) AS total_views,
    (SELECT COUNT(*) FROM follows WHERE chef_id = ?) AS follower_count,
    (SELECT COUNT(*) FROM bookmarks b JOIN recipes r2 ON b.recipe_id = r2.id WHERE r2.author_id = ?) AS total_bookmarks
    FROM recipes r
    WHERE r.author_id = ? AND r.status = 'published'";
$stmt = $conn->prepare($stats_sql);
$stmt->bind_param("iii", $chef_id, $chef_id, $chef_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Recent recipes
$recent_sql = "SELECT id, title, status, view_count, is_chef_pick, created_at FROM recipes WHERE author_id = ? ORDER BY created_at DESC LIMIT 5";
$stmt = $conn->prepare($recent_sql);
$stmt->bind_param("i", $chef_id);
$stmt->execute();
$recent_recipes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Most bookmarked recipe
$mbr_sql = "SELECT r.title, COUNT(b.id) AS bcount FROM bookmarks b JOIN recipes r ON b.recipe_id = r.id WHERE r.author_id = ? GROUP BY r.id ORDER BY bcount DESC LIMIT 1";
$stmt = $conn->prepare($mbr_sql);
$stmt->bind_param("i", $chef_id);
$stmt->execute();
$most_bookmarked = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<div class="card">
    <h2>Welcome, Chef <?php echo htmlspecialchars($_SESSION['name']); ?>! 👨‍🍳</h2>
    <p>Manage your recipes, collections, and track your performance from your chef dashboard.</p>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:1.5rem;">
    <div class="card" style="text-align:center;padding:1.2rem;">
        <div style="font-size:2rem;font-weight:700;color:var(--rust);"><?php echo $stats['total_recipes'] ?? 0; ?></div>
        <div>Published Recipes</div>
    </div>
    <div class="card" style="text-align:center;padding:1.2rem;">
        <div style="font-size:2rem;font-weight:700;color:var(--rust);"><?php echo $stats['total_views'] ?? 0; ?></div>
        <div>Total Views</div>
    </div>
    <div class="card" style="text-align:center;padding:1.2rem;">
        <div style="font-size:2rem;font-weight:700;color:var(--rust);"><?php echo $stats['follower_count'] ?? 0; ?></div>
        <div>Followers</div>
    </div>
    <div class="card" style="text-align:center;padding:1.2rem;">
        <div style="font-size:2rem;font-weight:700;color:var(--rust);"><?php echo $stats['total_bookmarks'] ?? 0; ?></div>
        <div>Total Bookmarks</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;">
    <div class="card">
        <h3>Recent Recipes</h3>
        <?php if (empty($recent_recipes)): ?>
            <p>No recipes yet. <a href="create_recipe.php">Create your first recipe!</a></p>
        <?php else: ?>
            <table style="width:100%;border-collapse:collapse;">
                <thead><tr style="border-bottom:2px solid #eee;">
                    <th style="text-align:left;padding:0.5rem;">Title</th>
                    <th>Status</th>
                    <th>Views</th>
                    <th>Pick</th>
                </tr></thead>
                <tbody>
                <?php foreach ($recent_recipes as $r): ?>
                    <tr style="border-bottom:1px solid #f0f0f0;">
                        <td style="padding:0.5rem;"><a href="manage_recipes.php"><?php echo htmlspecialchars($r['title']); ?></a></td>
                        <td style="text-align:center;">
                            <span style="padding:2px 8px;border-radius:12px;font-size:0.8rem;background:<?php echo $r['status']=='published'?'#d4edda':'#fff3cd'; ?>;color:<?php echo $r['status']=='published'?'#155724':'#856404'; ?>;">
                                <?php echo ucfirst($r['status']); ?>
                            </span>
                        </td>
                        <td style="text-align:center;"><?php echo $r['view_count']; ?></td>
                        <td style="text-align:center;"><?php echo $r['is_chef_pick'] ? '⭐' : '—'; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <div style="margin-top:1rem;">
            <a href="create_recipe.php" class="btn-small">+ New Recipe</a>
            <a href="manage_recipes.php" style="margin-left:1rem;">View All Recipes →</a>
        </div>
    </div>

    <div>
        <div class="card">
            <h3>Quick Links</h3>
            <ul style="list-style:none;padding:0;line-height:2.2;">
                <li><a href="create_recipe.php">✏️ Create Recipe</a></li>
                <li><a href="manage_recipes.php">📋 My Recipes</a></li>
                <li><a href="collections.php">📁 Collections</a></li>
                <li><a href="analytics.php">📊 Analytics</a></li>
                <li><a href="reviews.php">💬 Reviews</a></li>
                <li><a href="followers.php">👥 Followers</a></li>
                <li><a href="profile.php">👤 Edit Profile</a></li>
            </ul>
        </div>
        <?php if ($most_bookmarked): ?>
        <div class="card" style="margin-top:1rem;">
            <h3>🔖 Most Bookmarked</h3>
            <p><strong><?php echo htmlspecialchars($most_bookmarked['title']); ?></strong></p>
            <p><?php echo $most_bookmarked['bcount']; ?> bookmarks</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
