<?php
// chef/reviews.php
$page_title = "Recipe Reviews - Recipe Sharing Platform";
$base_url = "../";
include "../includes/header.php";
include "../includes/chef_nav.php";
require_once "../config/db_connect.php";
require_role("chef", $base_url);

$chef_id = $_SESSION['user_id'];

$sql = "SELECT rv.id, rv.recipe_id, rv.rating, rv.review_text, rv.chef_reply, rv.created_at,
               u.name AS reviewer_name, r.title AS recipe_title
        FROM reviews rv
        JOIN recipes r ON r.id = rv.recipe_id
        JOIN users u ON u.id = rv.user_id
        WHERE r.author_id = ?
        ORDER BY rv.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $chef_id);
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$success = $_SESSION['success'] ?? null; unset($_SESSION['success']);
$error   = $_SESSION['error'] ?? null;   unset($_SESSION['error']);
?>

<?php if ($success): ?><div class="card" style="background:#d4edda;color:#155724;"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<?php if ($error):   ?><div class="card" style="background:#f8d7da;color:#721c24;"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<div class="card">
    <h2>Recipe Reviews</h2>

    <?php if (empty($reviews)): ?>
        <p>No reviews on your recipes yet.</p>
    <?php else: ?>
        <?php foreach ($reviews as $rv): ?>
        <div style="border:1px solid #eee;border-radius:8px;padding:1rem;margin-bottom:1rem;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                <div>
                    <strong><?php echo htmlspecialchars($rv['reviewer_name']); ?></strong>
                    <span style="color:#f5a623;margin-left:0.5rem;"><?php echo str_repeat('⭐', $rv['rating']); ?></span>
                    <span style="font-size:0.8rem;color:#888;margin-left:0.5rem;"><?php echo date('M j, Y', strtotime($rv['created_at'])); ?></span>
                </div>
                <span style="font-size:0.85rem;color:#666;font-style:italic;">on: <?php echo htmlspecialchars($rv['recipe_title']); ?></span>
            </div>
            <p style="margin:0.6rem 0;"><?php echo nl2br(htmlspecialchars($rv['review_text'])); ?></p>

            <?php if ($rv['chef_reply']): ?>
                <div style="background:#f0f7ff;border-left:3px solid var(--rust);padding:0.8rem;border-radius:4px;margin-top:0.5rem;">
                    <strong>👨‍🍳 Your Reply:</strong>
                    <p style="margin:0.3rem 0 0;"><?php echo nl2br(htmlspecialchars($rv['chef_reply'])); ?></p>
                </div>
            <?php else: ?>
                <form action="../controllers/chef/reply_review_action.php" method="POST" style="margin-top:0.8rem;">
                    <input type="hidden" name="review_id" value="<?php echo $rv['id']; ?>">
                    <textarea name="chef_reply" rows="2" placeholder="Write your official reply..." required style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;"></textarea>
                    <button type="submit" class="btn-small" style="margin-top:0.5rem;">Post Reply</button>
                </form>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>
