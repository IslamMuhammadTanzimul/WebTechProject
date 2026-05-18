<?php
// chef/create_recipe.php
$page_title = "Create Recipe - Recipe Sharing Platform";
$base_url = "../";
include "../includes/header.php";
include "../includes/chef_nav.php";
require_once "../config/db_connect.php";
require_role("chef", $base_url);

// Fetch cuisines & diet types for dropdowns
$cuisines = $conn->query("SELECT id, name, flag_emoji FROM cuisines ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$diet_types = $conn->query("SELECT id, name FROM diet_types ORDER BY name")->fetch_all(MYSQLI_ASSOC);

$success = $_SESSION['success'] ?? null; unset($_SESSION['success']);
$error   = $_SESSION['error'] ?? null;   unset($_SESSION['error']);
?>

<?php if ($error): ?><div class="card" style="background:#f8d7da;color:#721c24;"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<div class="card">
    <h2>Create New Recipe</h2>
    <form action="../controllers/chef/create_recipe_action.php" method="POST" enctype="multipart/form-data">

        <h3>Basic Information</h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div style="grid-column:1/-1;">
                <label>Recipe Title *</label><br>
                <input type="text" name="title" required style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
            </div>
            <div style="grid-column:1/-1;">
                <label>Description *</label><br>
                <textarea name="description" rows="3" required style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;"></textarea>
            </div>
            <div>
                <label>Cuisine</label><br>
                <select name="cuisine_id" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
                    <option value="">-- Select Cuisine --</option>
                    <?php foreach ($cuisines as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['flag_emoji'] . ' ' . $c['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Diet Type</label><br>
                <select name="diet_type_id" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
                    <option value="">-- Select Diet Type --</option>
                    <?php foreach ($diet_types as $d): ?>
                        <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Difficulty *</label><br>
                <select name="difficulty" required style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
                    <option value="easy">Easy</option>
                    <option value="medium">Medium</option>
                    <option value="hard">Hard</option>
                </select>
            </div>
            <div>
                <label>Servings *</label><br>
                <input type="number" name="servings" min="1" value="4" required style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
            </div>
            <div>
                <label>Prep Time (minutes) *</label><br>
                <input type="number" name="prep_time_mins" min="0" value="0" required style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
            </div>
            <div>
                <label>Cook Time (minutes) *</label><br>
                <input type="number" name="cook_time_mins" min="0" value="0" required style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
            </div>
            <div>
                <label>Featured Image</label><br>
                <input type="file" name="featured_image" accept="image/*" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
            </div>
            <div>
                <label>Save as</label><br>
                <select name="status" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>
        </div>

        <h3 style="margin-top:1.5rem;">Ingredients</h3>
        <div id="ingredients-container">
            <div class="ingredient-row" style="display:grid;grid-template-columns:3fr 1fr 1fr auto;gap:0.5rem;margin-bottom:0.5rem;">
                <input type="text" name="ingredients[0][name]" placeholder="Ingredient name *" required style="padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
                <input type="text" name="ingredients[0][quantity]" placeholder="Quantity" style="padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
                <input type="text" name="ingredients[0][unit]" placeholder="Unit" style="padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
                <button type="button" onclick="removeRow(this)" style="padding:0.5rem;background:#e74c3c;color:#fff;border:none;border-radius:4px;cursor:pointer;">✕</button>
            </div>
        </div>
        <button type="button" onclick="addIngredient()" class="btn-small" style="margin-top:0.5rem;">+ Add Ingredient</button>

        <h3 style="margin-top:1.5rem;">Steps</h3>
        <div id="steps-container">
            <div class="step-row" style="margin-bottom:1rem;border:1px solid #eee;padding:1rem;border-radius:6px;">
                <label style="font-weight:600;">Step 1</label>
                <textarea name="steps[0][instruction]" rows="2" placeholder="Step instruction *" required style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;margin-top:0.3rem;"></textarea>
                <label style="font-size:0.85rem;color:#666;">Step Image (optional)</label><br>
                <input type="file" name="step_images[0]" accept="image/*" style="margin-top:0.3rem;">
                <button type="button" onclick="removeRow(this.closest('.step-row'))" style="margin-top:0.5rem;padding:4px 10px;background:#e74c3c;color:#fff;border:none;border-radius:4px;cursor:pointer;">Remove Step</button>
            </div>
        </div>
        <button type="button" onclick="addStep()" class="btn-small" style="margin-top:0.5rem;">+ Add Step</button>

        <h3 style="margin-top:1.5rem;">Nutrition Information (optional)</h3>
        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:1rem;">
            <div><label>Calories</label><br><input type="number" name="calories" min="0" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;"></div>
            <div><label>Protein (g)</label><br><input type="number" name="protein_g" min="0" step="0.1" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;"></div>
            <div><label>Carbs (g)</label><br><input type="number" name="carbs_g" min="0" step="0.1" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;"></div>
            <div><label>Fat (g)</label><br><input type="number" name="fat_g" min="0" step="0.1" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;"></div>
            <div><label>Fibre (g)</label><br><input type="number" name="fibre_g" min="0" step="0.1" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;"></div>
        </div>

        <div style="margin-top:1.5rem;">
            <button type="submit" class="btn-small">Save Recipe</button>
            <a href="manage_recipes.php" style="margin-left:1rem;">Cancel</a>
        </div>
    </form>
</div>

<script>
let ingCount = 1;
let stepCount = 1;

function addIngredient() {
    const i = ingCount++;
    const div = document.createElement('div');
    div.className = 'ingredient-row';
    div.style = 'display:grid;grid-template-columns:3fr 1fr 1fr auto;gap:0.5rem;margin-bottom:0.5rem;';
    div.innerHTML = `
        <input type="text" name="ingredients[${i}][name]" placeholder="Ingredient name *" required style="padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
        <input type="text" name="ingredients[${i}][quantity]" placeholder="Quantity" style="padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
        <input type="text" name="ingredients[${i}][unit]" placeholder="Unit" style="padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
        <button type="button" onclick="removeRow(this)" style="padding:0.5rem;background:#e74c3c;color:#fff;border:none;border-radius:4px;cursor:pointer;">✕</button>
    `;
    document.getElementById('ingredients-container').appendChild(div);
}

function addStep() {
    const i = stepCount++;
    const div = document.createElement('div');
    div.className = 'step-row';
    div.style = 'margin-bottom:1rem;border:1px solid #eee;padding:1rem;border-radius:6px;';
    div.innerHTML = `
        <label style="font-weight:600;">Step ${i + 1}</label>
        <textarea name="steps[${i}][instruction]" rows="2" placeholder="Step instruction *" required style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;margin-top:0.3rem;"></textarea>
        <label style="font-size:0.85rem;color:#666;">Step Image (optional)</label><br>
        <input type="file" name="step_images[${i}]" accept="image/*" style="margin-top:0.3rem;">
        <button type="button" onclick="removeRow(this.closest('.step-row'))" style="margin-top:0.5rem;padding:4px 10px;background:#e74c3c;color:#fff;border:none;border-radius:4px;cursor:pointer;">Remove Step</button>
    `;
    document.getElementById('steps-container').appendChild(div);
}

function removeRow(el) {
    el.remove();
}
</script>

<?php include "../includes/footer.php"; ?>
