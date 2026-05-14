<?php
// user/saved_recipes.php
$page_title = "My Saved Recipes - Recipe Sharing Platform";
$base_url = "../";
include "../includes/header.php";

require_once "../config/db_connect.php";
require_once "../includes/auth.php";
require_once "../models/BookmarkModel.php";

// Protect the route
require_role("user", $base_url);

$user_id = $_SESSION['user_id'];

// Fetch the user's saved recipes
$saved_recipes = getBookmarkedRecipes($conn, $user_id);
?>

<div class="card">
    <h2>❤️ My Saved Recipes</h2>
    <p>Here is your personal collection of bookmarked meals.</p>
</div>

<div id="recipe-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">

    <?php if (empty($saved_recipes)): ?>
        <div class="card" style="grid-column: 1 / -1; text-align: center;">
            <p>You haven't saved any recipes yet.</p>
            <a href="recipes.php" class="btn" style="margin-top: 10px; display: inline-block;">Browse Recipes</a>
        </div>
    <?php else: ?>
        <?php foreach ($saved_recipes as $recipe): ?>
            <div class="card" style="margin-bottom: 0; display: flex; flex-direction: column;">

                <?php if ($recipe['is_chef_pick']): ?>
                    <span
                        style="background: #f1c40f; color: #333; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; align-self: flex-start; margin-bottom: 10px;">
                        Chef's Pick ⭐
                    </span>
                <?php endif; ?>

                <h3 style="margin-bottom: 5px;">
                    <?php echo htmlspecialchars($recipe['title']); ?>
                </h3>

                <p style="color: #666; font-size: 14px; margin-bottom: 10px;">
                    By <strong>
                        <?php echo htmlspecialchars($recipe['author_name']); ?>
                    </strong>
                    <?php if ($recipe['chef_verified'])
                        echo "<span style='color: #2980b9;' title='Verified Chef'>✓</span>"; ?>
                </p>

                <ul style="list-style-type: none; margin-bottom: 15px; font-size: 14px; color: #444;">
                    <li><strong>Cuisine:</strong>
                        <?php echo $recipe['flag_emoji'] . ' ' . htmlspecialchars($recipe['cuisine_name'] ?? 'General'); ?>
                    </li>
                    <li><strong>Difficulty:</strong>
                        <?php echo ucfirst(htmlspecialchars($recipe['difficulty'])); ?>
                    </li>
                    <li><strong>Time:</strong>
                        <?php echo $recipe['prep_time_mins'] + $recipe['cook_time_mins']; ?> mins total
                    </li>
                </ul>

                <div style="margin-top: auto;">
                    <hr style="border: 0; border-top: 1px solid #ddd; margin-bottom: 10px;">
                    <a href="recipe_detail.php?id=<?php echo $recipe['id']; ?>" class="btn"
                        style="display: block; text-align: center;">View Full Recipe</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<?php include "../includes/footer.php"; ?>