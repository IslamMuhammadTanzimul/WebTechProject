<?php
// chef/collections.php
$page_title = "My Collections - Recipe Sharing Platform";
$base_url = "../";
include "../includes/header.php";
include "../includes/chef_nav.php";
require_once "../config/db_connect.php";
require_role("chef", $base_url);

$chef_id = $_SESSION['user_id'];

$sql = "SELECT col.*, COUNT(cr.recipe_id) AS recipe_count
        FROM collections col
        LEFT JOIN collection_recipes cr ON cr.collection_id = col.id
        WHERE col.chef_id = ?
        GROUP BY col.id
        ORDER BY col.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $chef_id);
$stmt->execute();
$collections = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$success = $_SESSION['success'] ?? null; unset($_SESSION['success']);
$error   = $_SESSION['error'] ?? null;   unset($_SESSION['error']);
?>

<?php if ($success): ?><div class="card" style="background:#d4edda;color:#155724;"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<?php if ($error):   ?><div class="card" style="background:#f8d7da;color:#721c24;"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h2>My Collections</h2>
        <button onclick="document.getElementById('create-collection-form').style.display='block';this.style.display='none';" class="btn-small">+ Create Collection</button>
    </div>

    <div id="create-collection-form" style="display:none;margin-top:1.5rem;border-top:1px solid #eee;padding-top:1rem;">
        <h3>New Collection</h3>
        <form action="../controllers/chef/create_collection_action.php" method="POST" enctype="multipart/form-data">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div>
                    <label>Title *</label><br>
                    <input type="text" name="title" required style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
                </div>
                <div>
                    <label>Visibility</label><br>
                    <select name="is_public" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
                        <option value="1">Public</option>
                        <option value="0">Private</option>
                    </select>
                </div>
                <div style="grid-column:1/-1;">
                    <label>Description</label><br>
                    <textarea name="description" rows="2" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;"></textarea>
                </div>
                <div>
                    <label>Cover Image</label><br>
                    <input type="file" name="cover_image" accept="image/*">
                </div>
            </div>
            <div style="margin-top:1rem;">
                <button type="submit" class="btn-small">Create</button>
                <button type="button" onclick="document.getElementById('create-collection-form').style.display='none';" style="margin-left:1rem;background:none;border:none;cursor:pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.5rem;margin-top:1rem;">
    <?php if (empty($collections)): ?>
        <div class="card"><p>No collections yet. Create your first one above!</p></div>
    <?php else: ?>
        <?php foreach ($collections as $col): ?>
        <div class="card">
            <?php if ($col['cover_image_path']): ?>
                <img src="../<?php echo htmlspecialchars($col['cover_image_path']); ?>" style="width:100%;height:140px;object-fit:cover;border-radius:6px;margin-bottom:0.8rem;">
            <?php endif; ?>
            <h3 style="margin:0;"><?php echo htmlspecialchars($col['title']); ?></h3>
            <p style="font-size:0.85rem;color:#666;"><?php echo $col['recipe_count']; ?> recipes · <?php echo $col['is_public'] ? '🌐 Public' : '🔒 Private'; ?></p>
            <?php if ($col['description']): ?>
                <p style="font-size:0.9rem;"><?php echo htmlspecialchars(substr($col['description'],0,100)); ?><?php echo strlen($col['description'])>100?'...':''; ?></p>
            <?php endif; ?>
            <div style="margin-top:0.8rem;display:flex;gap:0.5rem;flex-wrap:wrap;">
                <a href="collection_recipes.php?id=<?php echo $col['id']; ?>" class="btn-small" style="font-size:0.8rem;">Manage Recipes</a>
                <a href="../controllers/chef/delete_collection_action.php?id=<?php echo $col['id']; ?>" style="font-size:0.8rem;color:#e74c3c;" onclick="return confirm('Delete this collection?');">Delete</a>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>
