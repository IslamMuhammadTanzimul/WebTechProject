<?php
// chef/manage_recipes.php
$page_title = "My Recipes - Recipe Sharing Platform";
$base_url = "../";
include "../includes/header.php";
include "../includes/chef_nav.php";
require_once "../config/db_connect.php";
require_role("chef", $base_url);

$chef_id = $_SESSION['user_id'];

$sql = "SELECT r.id, r.title, r.status, r.is_chef_pick, r.view_count, r.created_at,
               c.name AS cuisine_name,
               (SELECT COUNT(*) FROM bookmarks b WHERE b.recipe_id = r.id) AS bookmark_count,
               (SELECT COUNT(*) FROM reviews rv WHERE rv.recipe_id = r.id) AS review_count,
               (SELECT AVG(rv2.rating) FROM reviews rv2 WHERE rv2.recipe_id = r.id) AS avg_rating
        FROM recipes r
        LEFT JOIN cuisines c ON c.id = r.cuisine_id
        WHERE r.author_id = ?
        ORDER BY r.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $chef_id);
$stmt->execute();
$recipes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Count current chef's picks
$pick_count = 0;
foreach ($recipes as $r) { if ($r['is_chef_pick']) $pick_count++; }

$success = $_SESSION['success'] ?? null; unset($_SESSION['success']);
$error   = $_SESSION['error'] ?? null;   unset($_SESSION['error']);
?>

<?php if ($success): ?><div class="card" style="background:#d4edda;color:#155724;"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<?php if ($error):   ?><div class="card" style="background:#f8d7da;color:#721c24;"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h2>My Recipes</h2>
        <a href="create_recipe.php" class="btn-small">+ Create Recipe</a>
    </div>

    <?php if (empty($recipes)): ?>
        <p>No recipes yet. <a href="create_recipe.php">Create your first one!</a></p>
    <?php else: ?>
    <table style="width:100%;border-collapse:collapse;margin-top:1rem;">
        <thead>
            <tr style="border-bottom:2px solid #eee;">
                <th style="text-align:left;padding:0.6rem;">Title</th>
                <th>Cuisine</th>
                <th>Status</th>
                <th>Views</th>
                <th>Bookmarks</th>
                <th>Reviews</th>
                <th>Avg Rating</th>
                <th>Chef's Pick</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($recipes as $r): ?>
            <tr style="border-bottom:1px solid #f0f0f0;">
                <td style="padding:0.6rem;"><?php echo htmlspecialchars($r['title']); ?></td>
                <td style="text-align:center;"><?php echo htmlspecialchars($r['cuisine_name'] ?? '—'); ?></td>
                <td style="text-align:center;">
                    <span style="padding:2px 8px;border-radius:12px;font-size:0.8rem;background:<?php echo $r['status']=='published'?'#d4edda':'#fff3cd'; ?>;color:<?php echo $r['status']=='published'?'#155724':'#856404'; ?>;">
                        <?php echo ucfirst($r['status']); ?>
                    </span>
                </td>
                <td style="text-align:center;"><?php echo $r['view_count']; ?></td>
                <td style="text-align:center;"><?php echo $r['bookmark_count']; ?></td>
                <td style="text-align:center;"><?php echo $r['review_count']; ?></td>
                <td style="text-align:center;"><?php echo $r['avg_rating'] ? number_format($r['avg_rating'], 1) . ' ⭐' : '—'; ?></td>
                <td style="text-align:center;"><?php echo $r['is_chef_pick'] ? '⭐ Pick' : '—'; ?></td>
                <td style="text-align:center;white-space:nowrap;">
                    <a href="edit_recipe.php?id=<?php echo $r['id']; ?>" style="font-size:0.85rem;margin-right:6px;">Edit</a>

                    <?php if ($r['status'] === 'published'): ?>
                        <a href="../api/chef/toggle_publish.php?id=<?php echo $r['id']; ?>&action=unpublish" style="font-size:0.85rem;margin-right:6px;color:orange;">Unpublish</a>
                    <?php else: ?>
                        <a href="../api/chef/toggle_publish.php?id=<?php echo $r['id']; ?>&action=publish" style="font-size:0.85rem;margin-right:6px;color:green;">Publish</a>
                    <?php endif; ?>

                    <?php if ($r['is_chef_pick']): ?>
                        <a href="../api/chef/toggle_chef_pick.php?id=<?php echo $r['id']; ?>&action=remove" style="font-size:0.85rem;margin-right:6px;color:#888;">Remove Pick</a>
                    <?php elseif ($pick_count < 3): ?>
                        <a href="../api/chef/toggle_chef_pick.php?id=<?php echo $r['id']; ?>&action=add" style="font-size:0.85rem;margin-right:6px;color:goldenrod;">Mark Pick</a>
                    <?php else: ?>
                        <span style="font-size:0.8rem;color:#aaa;" title="Max 3 Chef's Picks">Pick Limit</span>
                    <?php endif; ?>

                    <a href="../api/chef/delete_recipe.php?id=<?php echo $r['id']; ?>" style="font-size:0.85rem;color:#e74c3c;" onclick="return confirm('Delete this recipe?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>
