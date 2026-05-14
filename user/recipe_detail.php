<?php
// user/recipe_detail.php
$base_url = "../";
require_once "../includes/auth.php";
require_once "../config/db_connect.php";
require_once "../models/RecipeModel.php";
require_once "../models/BookmarkModel.php";
require_once "../models/ReviewModel.php";

// Protect the route
require_role("user", $base_url);

// Validate that an ID was passed in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: recipes.php");
    exit();
}

$recipe_id = (int) $_GET['id'];
$user_id = $_SESSION['user_id'];

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

// Check states for the current user
$is_bookmarked = isBookmarked($conn, $user_id, $recipe_id);
$has_reviewed = hasUserReviewed($conn, $recipe_id, $user_id);

// Fetch all existing reviews
$reviews = getReviewsByRecipe($conn, $recipe_id);

// Dynamically set page title
$page_title = htmlspecialchars($recipe['title']) . " - Recipe Sharing Platform";
include "../includes/header.php";
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <a href="recipes.php" style="color: #e67e22; text-decoration: none;">&larr; Back to Browse</a>

        <div style="display: flex; gap: 10px;">
            <button id="add-to-list-btn" data-recipe-id="<?php echo $recipe_id; ?>"
                style="padding: 8px 15px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; transition: 0.3s; background-color: #3498db; color: white;">
                🛒 Add to Shopping List
            </button>

            <button id="bookmark-btn" data-recipe-id="<?php echo $recipe_id; ?>"
                style="padding: 8px 15px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; transition: 0.3s; 
                    <?php echo $is_bookmarked ? 'background-color: #c0392b; color: white;' : 'background-color: #ecf0f1; color: #333;'; ?>">
                <?php echo $is_bookmarked ? '❤️ Saved' : '🤍 Save Recipe'; ?>
            </button>
        </div>
    </div>
    <br>

    <?php if ($recipe['is_chef_pick']): ?>
        <span style="background: #f1c40f; color: #333; padding: 5px 10px; border-radius: 4px; font-weight: bold;">
            Chef's Pick ⭐
        </span>
    <?php endif; ?>

    <h1 style="margin-top: 15px;"><?php echo htmlspecialchars($recipe['title']); ?></h1>

    <p style="color: #555; font-size: 16px; margin-top: 5px;">
        Created by <strong><?php echo htmlspecialchars($recipe['author_name']); ?></strong>
        <?php if ($recipe['chef_verified'])
            echo "<span style='color: #2980b9;' title='Verified Chef'>✓</span>"; ?>
    </p>

    <div
        style="display: flex; gap: 15px; margin-top: 15px; flex-wrap: wrap; background: #f9f9f9; padding: 15px; border-radius: 6px; border: 1px solid #eee;">
        <div><strong>Cuisine:</strong>
            <?php echo $recipe['flag_emoji'] . ' ' . htmlspecialchars($recipe['cuisine_name'] ?? 'N/A'); ?></div>
        <div><strong>Diet:</strong> <?php echo htmlspecialchars($recipe['diet_name'] ?? 'N/A'); ?></div>
        <div><strong>Difficulty:</strong> <?php echo ucfirst(htmlspecialchars($recipe['difficulty'])); ?></div>
        <div><strong>Prep Time:</strong> <?php echo $recipe['prep_time_mins']; ?> mins</div>
        <div><strong>Cook Time:</strong> <?php echo $recipe['cook_time_mins']; ?> mins</div>
        <div><strong>Servings:</strong> <?php echo $recipe['servings']; ?></div>
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
                        <strong><?php echo floatval($ing['quantity']) . ' ' . htmlspecialchars($ing['unit']); ?></strong>
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
                <strong
                    style="display: block; font-size: 20px; color: #d35400;"><?php echo $nutrition['calories']; ?></strong>
                Calories
            </div>
            <div style="background: #eef2f3; padding: 15px; border-radius: 6px; flex: 1; min-width: 100px;">
                <strong
                    style="display: block; font-size: 20px; color: #2c3e50;"><?php echo floatval($nutrition['protein_g']); ?>g</strong>
                Protein
            </div>
            <div style="background: #eef2f3; padding: 15px; border-radius: 6px; flex: 1; min-width: 100px;">
                <strong
                    style="display: block; font-size: 20px; color: #2c3e50;"><?php echo floatval($nutrition['carbs_g']); ?>g</strong>
                Carbs
            </div>
            <div style="background: #eef2f3; padding: 15px; border-radius: 6px; flex: 1; min-width: 100px;">
                <strong
                    style="display: block; font-size: 20px; color: #2c3e50;"><?php echo floatval($nutrition['fat_g']); ?>g</strong>
                Fat
            </div>
            <div style="background: #eef2f3; padding: 15px; border-radius: 6px; flex: 1; min-width: 100px;">
                <strong
                    style="display: block; font-size: 20px; color: #2c3e50;"><?php echo floatval($nutrition['fibre_g']); ?>g</strong>
                Fibre
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="card">
    <h3>Ratings & Reviews</h3>
    <hr style="margin: 10px 0 20px 0; border: 0; border-top: 1px solid #ddd;">

    <?php if (isset($_SESSION['success'])): ?>
        <p class="success"><?php echo $_SESSION['success'];
        unset($_SESSION['success']); ?></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <p class="error"><?php echo $_SESSION['error'];
        unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <?php if (!$has_reviewed): ?>
        <div style="background: #f9f9f9; padding: 15px; border-radius: 6px; border: 1px solid #eee; margin-bottom: 25px;">
            <h4 style="margin-bottom: 10px;">Leave a Review</h4>
            <form action="../controllers/submit_review_action.php" method="POST">
                <input type="hidden" name="recipe_id" value="<?php echo $recipe_id; ?>">

                <div class="form-group">
                    <label>Rating:</label>
                    <select name="rating" required style="width: auto; padding: 8px;">
                        <option value="5">⭐⭐⭐⭐⭐ (5/5) - Excellent</option>
                        <option value="4">⭐⭐⭐⭐ (4/5) - Very Good</option>
                        <option value="3">⭐⭐⭐ (3/5) - Average</option>
                        <option value="2">⭐⭐ (2/5) - Fair</option>
                        <option value="1">⭐ (1/5) - Poor</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Your Review:</label>
                    <textarea name="review_text" rows="3" placeholder="What did you think of this recipe?" required
                        style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;"></textarea>
                </div>

                <button type="submit" class="btn">Submit Review</button>
            </form>
        </div>
    <?php else: ?>
        <p style="color: green; font-weight: bold; margin-bottom: 25px;">✓ You have already reviewed this recipe.</p>
    <?php endif; ?>

    <?php if (empty($reviews)): ?>
        <p>No reviews yet. Be the first to review this recipe!</p>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php foreach ($reviews as $review): ?>
                <div style="border-bottom: 1px solid #eee; padding-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <strong><?php echo htmlspecialchars($review['reviewer_name']); ?></strong>
                        <span style="color: #f1c40f;">
                            <?php echo str_repeat("⭐", $review['rating']); ?>
                        </span>
                    </div>
                    <span style="font-size: 12px; color: #999; display: block; margin-bottom: 8px;">
                        Posted on <?php echo date("F j, Y", strtotime($review['created_at'])); ?>
                    </span>
                    <p style="margin: 0; line-height: 1.5; color: #444;">
                        <?php echo nl2br(htmlspecialchars($review['review_text'])); ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="../assets/js/bookmark.js"></script>
<script src="../assets/js/shopping_list.js"></script>

<?php include "../includes/footer.php"; ?>