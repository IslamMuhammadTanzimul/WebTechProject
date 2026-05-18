<?php
// chef/collection_recipes.php
$page_title = "Manage Collection Recipes - Recipe Sharing Platform";
$base_url = "../";
include "../includes/header.php";
include "../includes/chef_nav.php";
require_once "../config/db_connect.php";
require_role("chef", $base_url);

$chef_id = $_SESSION['user_id'];
$collection_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Verify ownership
$stmt = $conn->prepare("SELECT * FROM collections WHERE id = ? AND chef_id = ?");
$stmt->bind_param("ii", $collection_id, $chef_id);
$stmt->execute();
$collection = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$collection) { header("Location: collections.php"); exit(); }

// Recipes in collection
$in_sql = "SELECT r.id, r.title, cr.display_order FROM collection_recipes cr JOIN recipes r ON r.id = cr.recipe_id WHERE cr.collection_id = ? ORDER BY cr.display_order";
$stmt = $conn->prepare($in_sql);
$stmt->bind_param("i", $collection_id);
$stmt->execute();
$in_recipes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Own published recipes not yet in collection
$in_ids = array_column($in_recipes, 'id');
$exclude = count($in_ids) ? implode(',', $in_ids) : '0';
$all_sql = "SELECT id, title FROM recipes WHERE author_id = ? AND status = 'published' AND id NOT IN ($exclude) ORDER BY title";
$stmt = $conn->prepare($all_sql);
$stmt->bind_param("i", $chef_id);
$stmt->execute();
$available = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$success = $_SESSION['success'] ?? null; unset($_SESSION['success']);
$error   = $_SESSION['error'] ?? null;   unset($_SESSION['error']);
?>

<?php if ($success): ?><div class="card" style="background:#d4edda;color:#155724;"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<?php if ($error):   ?><div class="card" style="background:#f8d7da;color:#721c24;"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <a href="collections.php">← Back to Collections</a>
            <h2 style="margin:0.5rem 0;"><?php echo htmlspecialchars($collection['title']); ?></h2>
        </div>
    </div>

    <h3>Recipes in this Collection</h3>
    <?php if (empty($in_recipes)): ?>
        <p>No recipes added yet.</p>
    <?php else: ?>
        <table style="width:100%;border-collapse:collapse;">
            <thead><tr style="border-bottom:2px solid #eee;">
                <th style="text-align:left;padding:0.5rem;">Recipe</th>
                <th>Display Order</th>
                <th>Actions</th>
            </tr></thead>
            <tbody>
            <?php foreach ($in_recipes as $r): ?>
                <tr style="border-bottom:1px solid #f0f0f0;">
                    <td style="padding:0.5rem;"><?php echo htmlspecialchars($r['title']); ?></td>
                    <td style="text-align:center;">
                        <form action="../controllers/chef/update_collection_order_action.php" method="POST" style="display:inline;">
                            <input type="hidden" name="collection_id" value="<?php echo $collection_id; ?>">
                            <input type="hidden" name="recipe_id" value="<?php echo $r['id']; ?>">
                            <input type="number" name="display_order" value="<?php echo $r['display_order']; ?>" style="width:60px;padding:3px;border:1px solid #ddd;border-radius:4px;text-align:center;">
                            <button type="submit" style="padding:3px 8px;background:var(--rust);color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:0.8rem;">Set</button>
                        </form>
                    </td>
                    <td style="text-align:center;">
                        <a href="../controllers/chef/remove_collection_recipe_action.php?collection_id=<?php echo $collection_id; ?>&recipe_id=<?php echo $r['id']; ?>" style="color:#e74c3c;font-size:0.85rem;" onclick="return confirm('Remove from collection?');">Remove</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h3 style="margin-top:1.5rem;">Add Recipe to Collection</h3>
    <?php if (empty($available)): ?>
        <p>No more recipes available to add (only published recipes can be added).</p>
    <?php else: ?>
        <form action="../controllers/chef/add_collection_recipe_action.php" method="POST">
            <input type="hidden" name="collection_id" value="<?php echo $collection_id; ?>">
            <select name="recipe_id" required style="padding:0.5rem;border:1px solid #ddd;border-radius:4px;margin-right:0.5rem;">
                <option value="">-- Select Recipe --</option>
                <?php foreach ($available as $r): ?>
                    <option value="<?php echo $r['id']; ?>"><?php echo htmlspecialchars($r['title']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-small">Add</button>
        </form>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>
