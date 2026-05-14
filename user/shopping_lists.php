<?php
// user/shopping_lists.php
$page_title = "My Shopping Lists - Recipe Sharing Platform";
$base_url = "../";
include "../includes/header.php";

require_once "../config/db_connect.php";
require_once "../includes/auth.php";
require_once "../models/ShoppingListModel.php";

// Protect the route
require_role("user", $base_url);

$user_id = $_SESSION['user_id'];

// Fetch all lists for this user
$shopping_lists = getUserShoppingLists($conn, $user_id);
?>

<div class="card">
    <h2>🛒 My Shopping Lists</h2>
    <p>Take these lists to the grocery store! Check off ingredients as you buy them.</p>
</div>

<?php if (empty($shopping_lists)): ?>
    <div class="card" style="text-align: center;">
        <p>You don't have any shopping lists yet.</p>
        <a href="recipes.php" class="btn" style="margin-top: 10px;">Browse Recipes to add items</a>
    </div>
<?php else: ?>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
        <?php foreach ($shopping_lists as $list): ?>
            <?php 
                // Fetch the items for this specific list
                $items = getListItems($conn, $list['id'], $user_id); 
            ?>
            
            <div class="card" style="margin-bottom: 0;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">
                    <div>
                        <h3 style="margin: 0; color: #2c3e50;"><?php echo htmlspecialchars($list['name']); ?></h3>
                        <span style="font-size: 12px; color: #7f8c8d;">
                            Created: <?php echo date("M j, Y", strtotime($list['created_at'])); ?>
                        </span>
                    </div>
                    
                    <button class="delete-list-btn" data-list-id="<?php echo $list['id']; ?>" 
                            style="background: #e74c3c; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                        Delete
                    </button>
                </div>

                <?php if (empty($items)): ?>
                    <p style="color: #999; font-style: italic;">No items in this list.</p>
                <?php else: ?>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <?php foreach ($items as $item): ?>
                            <li style="padding: 8px 0; border-bottom: 1px solid #f9f9f9; display: flex; align-items: center; gap: 10px;">
                                
                                <input type="checkbox" class="item-checkbox" 
                                       data-item-id="<?php echo $item['id']; ?>" 
                                       style="width: 18px; height: 18px; cursor: pointer;"
                                       <?php echo $item['is_checked'] ? 'checked' : ''; ?>>
                                
                                <span class="item-text" style="<?php echo $item['is_checked'] ? 'text-decoration: line-through; color: #aaa;' : ''; ?>">
                                    <strong><?php echo floatval($item['quantity']) . ' ' . htmlspecialchars($item['unit']); ?></strong> 
                                    <?php echo htmlspecialchars($item['ingredient_name']); ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            
        <?php endforeach; ?>
    </div>

<?php endif; ?>

<?php include "../includes/footer.php"; ?>