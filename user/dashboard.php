<?php
// user/dashboard.php
$page_title = "Home Cook Dashboard - Recipe Sharing Platform";
$base_url = "../";
include "../includes/header.php";

require_once "../config/db_connect.php";
require_once "../includes/auth.php";
require_once "../models/BookmarkModel.php";
require_once "../models/ShoppingListModel.php";
require_once "../models/FollowModel.php";

// Protect the route
require_role("user", $base_url);

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'];

// Fetch all dynamic data
$saved_recipes = getBookmarkedRecipes($conn, $user_id);
$total_saved = count($saved_recipes);

$shopping_lists = getUserShoppingLists($conn, $user_id);
$total_lists = count($shopping_lists);

$followed_chefs = getFollowedChefs($conn, $user_id);
$total_chefs = count($followed_chefs);
?>

<div class="card"
    style="background: #f9f9f9; color: #111; border-radius: 8px; padding: 30px; border: 1px solid #eaeaea;">
    <h1 style="margin-top: 0; color: #111;">Welcome back, <?php echo htmlspecialchars($user_name); ?>! 🍳</h1>
    <p style="font-size: 18px; margin-bottom: 0; color: #555;">What are we cooking this week? Check out your stats and
        jump right back into your kitchen.</p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">

    <div class="card" style="text-align: center; border-top: 3px solid #111;">
        <h3 style="color: #555; margin-bottom: 10px;">❤️ Saved Recipes</h3>
        <p style="font-size: 36px; font-weight: bold; color: #111; margin: 0;"><?php echo $total_saved; ?></p>
        <a href="saved.php" class="btn" style="display: block; margin-top: 15px;">View Collection</a>
    </div>

    <div class="card" style="text-align: center; border-top: 3px solid #111;">
        <h3 style="color: #555; margin-bottom: 10px;">🛒 Shopping Lists</h3>
        <p style="font-size: 36px; font-weight: bold; color: #111; margin: 0;"><?php echo $total_lists; ?></p>
        <a href="shopping_lists.php" class="btn" style="display: block; margin-top: 15px;">Go to Store</a>
    </div>

    <div class="card" style="text-align: center; border-top: 3px solid #111;">
        <h3 style="color: #555; margin-bottom: 10px;">👨‍🍳 Followed Chefs</h3>
        <p style="font-size: 36px; font-weight: bold; color: #111; margin: 0;"><?php echo $total_chefs; ?></p>
        <a href="recipes.php" class="btn" style="display: block; margin-top: 15px;">Find More Chefs</a>
    </div>

</div>

<div style="display: flex; flex-wrap: wrap; gap: 20px; margin-top: 20px;">

    <div class="card" style="flex: 2; min-width: 300px;">
        <h2>📅 Your Weekly Meal Plan</h2>
        <p style="color: #666; margin-bottom: 20px;">Organize your week by assigning your saved recipes to specific
            days. Don't let your ingredients go to waste!</p>
        <a href="meal_plan.php" class="btn" style="font-size: 16px; padding: 12px 24px;">Open Meal Planner</a>
    </div>

    <div class="card" style="flex: 1; min-width: 300px; background-color: #f9f9f9;">
        <h2>🔍 Need Inspiration?</h2>
        <p style="color: #666; margin-bottom: 20px;">Browse our database of community and chef-verified recipes to find
            your next favorite dish.</p>
        <a href="recipes.php" class="btn"
            style="font-size: 16px; padding: 12px 24px; background-color: transparent; border: 1px solid #111; color: #111 !important;">Browse
            All Recipes</a>
    </div>

</div>

<?php include "../includes/footer.php"; ?>