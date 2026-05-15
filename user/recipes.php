<?php
// user/recipes.php
$page_title = "Browse Recipes - Recipe Sharing Platform";
$base_url = "../";
include "../includes/header.php";

require_once "../config/db_connect.php";
require_once "../models/RecipeModel.php";

// Protect the route
require_role("user", $base_url);

// Fetch initial data from the database
$recipes = getPublishedRecipes($conn);
$cuisines = getCuisines($conn);
$diet_types = getDietTypes($conn);
?>

<div class="card">
    <h2>Browse Recipes</h2>
    <p>Discover new meals, filter by your preferences, and find your next favorite dish.</p>

    <form id="filter-form" style="display: flex; gap: 10px; margin-top: 15px; flex-wrap: wrap;">
        <input type="text" name="search" placeholder="Search recipes or ingredients..."
            style="flex: 1; min-width: 200px;">

        <select name="cuisine" style="width: auto;">
            <option value="">All Cuisines</option>
            <?php foreach ($cuisines as $c): ?>
                <option value="<?php echo $c['id']; ?>">
                    <?php echo $c['flag_emoji'] . ' ' . htmlspecialchars($c['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="diet" style="width: auto;">
            <option value="">All Diets</option>
            <?php foreach ($diet_types as $d): ?>
                <option value="<?php echo $d['id']; ?>">
                    <?php echo htmlspecialchars($d['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="difficulty" style="width: auto;">
            <option value="">Any Difficulty</option>
            <option value="easy">Easy</option>
            <option value="medium">Medium</option>
            <option value="hard">Hard</option>
        </select>
    </form>
</div>

<div id="recipe-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">

    <?php if (empty($recipes)): ?>
        <div class="card" style="grid-column: 1 / -1; text-align: center;">
            <p>No recipes published yet. Check back soon!</p>
        </div>
    <?php else: ?>
        <?php foreach ($recipes as $recipe): ?>
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

<script src="../assets/js/recipes.js"></script>

<?php include "../includes/footer.php"; ?>