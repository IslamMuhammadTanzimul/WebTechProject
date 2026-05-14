<?php
// user/recipe_detail.php
$base_url = "../";
require_once "../includes/auth.php";
require_once "../config/db_connect.php";
require_once "../models/RecipeModel.php";

// Protect the route
require_role("user", $base_url);

// Validate that an ID was passed in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: recipes.php");
    exit();
}

$recipe_id = (int) $_GET['id'];

// Fetch all the details using the Model
$recipe = getRecipeById($conn, $recipe_id);

// If recipe doesn't exist or is not published, stop and show error
if (!$recipe || $recipe['status'] !== 'published') {
    $page_title = "Recipe Not Found";
    include "../includes/header.php";
    echo "<div class='card'><h2>Recipe not found or unavailable.</h2><br><a href='recipes.php' class='btn'>Back to Browse</a></div>";
    include "../includes/footer.php";
    exit();
}

// Fetch the nested lists
$ingredients = getRecipeIngredients($conn, $recipe_id);
$steps = getRecipeSteps($conn, $recipe_id);
$nutrition = getRecipeNutrition($conn, $recipe_id);

// Dynamically set page title
$page_title = htmlspecialchars($recipe['title']) . " - Recipe Sharing Platform";
include "../includes/header.php";
?>

<div class="card">
    <a href="recipes.php" style="color: #e67e22; text-decoration: none;">&larr; Back to Browse</a>
    <br><br>

    <?php if ($recipe['is_chef_pick']): ?>
        <span style="background: #f1c40f; color: #333; padding: 5px 10px; border-radius: 4px; font-weight: bold;">
            Chef's Pick ⭐
        </span>
    <?php endif; ?>

    <h1 style="margin-top: 15px;">
        <?php echo htmlspecialchars($recipe['title']); ?>
    </h1>

    <p style="color: #555; font-size: 16px; margin-top: 5px;">
        Created by <strong>
            <?php echo htmlspecialchars($recipe['author_name']); ?>
        </strong>
        <?php if ($recipe['chef_verified'])
            echo "<span style='color: #2980b9;' title='Verified Chef'>✓</span>"; ?>
    </p>

    <div
        style="display: flex; gap: 15px; margin-top: 15px; flex-wrap: wrap; background: #f9f9f9; padding: 15px; border-radius: 6px; border: 1px solid #eee;">
        <div><strong>Cuisine:</strong>
            <?php echo $recipe['flag_emoji'] . ' ' . htmlspecialchars($recipe['cuisine_name'] ?? 'N/A'); ?>
        </div>
        <div><strong>Diet:</strong>
            <?php echo htmlspecialchars($recipe['diet_name'] ?? 'N/A'); ?>
        </div>
        <div><strong>Difficulty:</strong>
            <?php echo ucfirst(htmlspecialchars($recipe['difficulty'])); ?>
        </div>
        <div><strong>Prep Time:</strong>
            <?php echo $recipe['prep_time_mins']; ?> mins
        </div>
        <div><strong>Cook Time:</strong>
            <?php echo $recipe['cook_time_mins']; ?> mins
        </div>
        <div><strong>Servings:</strong>
            <?php echo $recipe['servings']; ?>
        </div>
    </div>

    <p style="margin-top: 20px; font-size: 16px; line-height: 1.6;">
        <?php echo nl2br(htmlspecialchars($recipe['description'])); ?>
    </p>
</div>

<div style="display: flex; flex-wrap: wrap; gap: 20px;">

    <div class="card" style="flex: 1; min-width: 300px;">
        <h3>Ingredients</h3>
        <hr style="margin: 10px 0; border: 0; border-top: 1px solid #ddd;">

        <?php if (empty($ingredients)): ?>
            <p>No ingredients listed.</p>
        <?php else: ?>
            <ul style="list-style-type: square; margin-left: 20px; line-height: 1.8;">
                <?php foreach ($ingredients as $ing): ?>
                    <li>
                        <strong>
                            <?php echo floatval($ing['quantity']) . ' ' . htmlspecialchars($ing['unit']); ?>
                        </strong>
                        <?php echo htmlspecialchars($ing['name']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="card" style="flex: 2; min-width: 300px;">
        <h3>Preparation Steps</h3>
        <hr style="margin: 10px 0; border: 0; border-top: 1px solid #ddd;">

        <?php if (empty($steps)): ?>
            <p>No steps listed.</p>
        <?php else: ?>
            <ol style="margin-left: 20px; line-height: 1.8;">
                <?php foreach ($steps as $step): ?>
                    <li style="margin-bottom: 15px;">
                        <?php echo nl2br(htmlspecialchars($step['instruction'])); ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
    </div>

</div>

<?php if ($nutrition): ?>
    <div class="card">
        <h3>Nutrition Information (per serving)</h3>
        <div style="display: flex; gap: 20px; margin-top: 15px; flex-wrap: wrap; text-align: center;">
            <div style="background: #eef2f3; padding: 15px; border-radius: 6px; flex: 1; min-width: 100px;">
                <strong style="display: block; font-size: 20px; color: #d35400;">
                    <?php echo $nutrition['calories']; ?>
                </strong>
                Calories
            </div>
            <div style="background: #eef2f3; padding: 15px; border-radius: 6px; flex: 1; min-width: 100px;">
                <strong style="display: block; font-size: 20px; color: #2c3e50;">
                    <?php echo floatval($nutrition['protein_g']); ?>g
                </strong>
                Protein
            </div>
            <div style="background: #eef2f3; padding: 15px; border-radius: 6px; flex: 1; min-width: 100px;">
                <strong style="display: block; font-size: 20px; color: #2c3e50;">
                    <?php echo floatval($nutrition['carbs_g']); ?>g
                </strong>
                Carbs
            </div>
            <div style="background: #eef2f3; padding: 15px; border-radius: 6px; flex: 1; min-width: 100px;">
                <strong style="display: block; font-size: 20px; color: #2c3e50;">
                    <?php echo floatval($nutrition['fat_g']); ?>g
                </strong>
                Fat
            </div>
            <div style="background: #eef2f3; padding: 15px; border-radius: 6px; flex: 1; min-width: 100px;">
                <strong style="display: block; font-size: 20px; color: #2c3e50;">
                    <?php echo floatval($nutrition['fibre_g']); ?>g
                </strong>
                Fibre
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include "../includes/footer.php"; ?>