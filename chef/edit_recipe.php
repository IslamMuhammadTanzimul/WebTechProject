<?php
// chef/edit_recipe.php
$page_title = "Edit Recipe - Recipe Sharing Platform";
$base_url = "../";
include "../includes/header.php";
include "../includes/chef_nav.php";
require_once "../config/db_connect.php";
require_role("chef", $base_url);

$chef_id = $_SESSION['user_id'];
$recipe_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Load recipe — only owner can edit
$sql = "SELECT r.*, c.id AS cuisine_sel, d.id AS diet_sel FROM recipes r LEFT JOIN cuisines c ON c.id = r.cuisine_id LEFT JOIN diet_types d ON d.id = r.diet_type_id WHERE r.id = ? AND r.author_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $recipe_id, $chef_id);
$stmt->execute();
$recipe = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$recipe) { header("Location: manage_recipes.php"); exit(); }

$ingredients_res = $conn->query("SELECT * FROM ingredients WHERE recipe_id = $recipe_id ORDER BY order_index");
$ingredients = $ingredients_res->fetch_all(MYSQLI_ASSOC);

$steps_res = $conn->query("SELECT * FROM steps WHERE recipe_id = $recipe_id ORDER BY step_order");
$steps = $steps_res->fetch_all(MYSQLI_ASSOC);

$nut_stmt = $conn->prepare("SELECT * FROM nutrition_info WHERE recipe_id = ?");
$nut_stmt->bind_param("i", $recipe_id);
$nut_stmt->execute();
$nutrition = $nut_stmt->get_result()->fetch_assoc();
$nut_stmt->close();

$cuisines = $conn->query("SELECT id, name, flag_emoji FROM cuisines ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$diet_types = $conn->query("SELECT id, name FROM diet_types ORDER BY name")->fetch_all(MYSQLI_ASSOC);

$error = $_SESSION['error'] ?? null; unset($_SESSION['error']);
?>

<?php if ($error): ?><div class="card" style="background:#f8d7da;color:#721c24;"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<div class="card">
    <h2>Edit Recipe: <?php echo htmlspecialchars($recipe['title']); ?></h2>
    <form action="../controllers/chef/edit_recipe_action.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="recipe_id" value="<?php echo $recipe_id; ?>">

        <h3>Basic Information</h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div style="grid-column:1/-1;">
                <label>Recipe Title *</label><br>
                <input type="text" name="title" required value="<?php echo htmlspecialchars($recipe['title']); ?>" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
            </div>
            <div style="grid-column:1/-1;">
                <label>Description *</label><br>
                <textarea name="description" rows="3" required style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;"><?php echo htmlspecialchars($recipe['description']); ?></textarea>
            </div>
            <div>
                <label>Cuisine</label><br>
                <select name="cuisine_id" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
                    <option value="">-- Select Cuisine --</option>
                    <?php foreach ($cuisines as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo $recipe['cuisine_id'] == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['flag_emoji'] . ' ' . $c['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Diet Type</label><br>
                <select name="diet_type_id" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
                    <option value="">-- Select Diet Type --</option>
                    <?php foreach ($diet_types as $d): ?>
                        <option value="<?php echo $d['id']; ?>" <?php echo $recipe['diet_type_id'] == $d['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($d['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Difficulty *</label><br>
                <select name="difficulty" required style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
                    <?php foreach (['easy','medium','hard'] as $d): ?>
                        <option value="<?php echo $d; ?>" <?php echo $recipe['difficulty']==$d?'selected':''; ?>><?php echo ucfirst($d); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Servings *</label><br>
                <input type="number" name="servings" min="1" value="<?php echo $recipe['servings']; ?>" required style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
            </div>
            <div>
                <label>Prep Time (mins) *</label><br>
                <input type="number" name="prep_time_mins" min="0" value="<?php echo $recipe['prep_time_mins']; ?>" required style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
            </div>
            <div>
                <label>Cook Time (mins) *</label><br>
                <input type="number" name="cook_time_mins" min="0" value="<?php echo $recipe['cook_time_mins']; ?>" required style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
            </div>
            <div>
                <label>Featured Image (leave blank to keep current)</label><br>
                <input type="file" name="featured_image" accept="image/*">
                <?php if ($recipe['featured_image_path']): ?>
                    <small>Current: <?php echo htmlspecialchars(basename($recipe['featured_image_path'])); ?></small>
                <?php endif; ?>
            </div>
            <div>
                <label>Status</label><br>
                <select name="status" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
                    <option value="draft" <?php echo $recipe['status']=='draft'?'selected':''; ?>>Draft</option>
                    <option value="published" <?php echo $recipe['status']=='published'?'selected':''; ?>>Published</option>
                </select>
            </div>
        </div>

        <h3 style="margin-top:1.5rem;">Ingredients</h3>
        <div id="ingredients-container">
            <?php foreach ($ingredients as $idx => $ing): ?>
            <div class="ingredient-row" style="display:grid;grid-template-columns:3fr 1fr 1fr auto;gap:0.5rem;margin-bottom:0.5rem;">
                <input type="text" name="ingredients[<?php echo $idx; ?>][name]" value="<?php echo htmlspecialchars($ing['name']); ?>" required style="padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
                <input type="text" name="ingredients[<?php echo $idx; ?>][quantity]" value="<?php echo htmlspecialchars($ing['quantity']); ?>" style="padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
                <input type="text" name="ingredients[<?php echo $idx; ?>][unit]" value="<?php echo htmlspecialchars($ing['unit']); ?>" style="padding:0.5rem;border:1px solid #ddd;border-radius:4px;">
                <button type="button" onclick="removeRow(this)" style="padding:0.5rem;background:#e74c3c;color:#fff;border:none;border-radius:4px;cursor:pointer;">✕</button>
            </div>
            <?php endforeach; ?>
        </div>
        <button type="button" onclick="addIngredient()" class="btn-small">+ Add Ingredient</button>

        <h3 style="margin-top:1.5rem;">Steps</h3>
        <div id="steps-container">
            <?php foreach ($steps as $idx => $step): ?>
            <div class="step-row" style="margin-bottom:1rem;border:1px solid #eee;padding:1rem;border-radius:6px;">
                <label style="font-weight:600;">Step <?php echo $idx+1; ?></label>
                <textarea name="steps[<?php echo $idx; ?>][instruction]" rows="2" required style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;margin-top:0.3rem;"><?php echo htmlspecialchars($step['instruction']); ?></textarea>
                <label style="font-size:0.85rem;color:#666;">New Step Image (replaces existing)</label><br>
                <input type="file" name="step_images[<?php echo $idx; ?>]" accept="image/*">
                <?php if ($step['step_image_path']): ?><small>Current: <?php echo htmlspecialchars(basename($step['step_image_path'])); ?></small><?php endif; ?>
                <button type="button" onclick="removeRow(this.closest('.step-row'))" style="margin-top:0.5rem;padding:4px 10px;background:#e74c3c;color:#fff;border:none;border-radius:4px;cursor:pointer;">Remove Step</button>
            </div>
            <?php endforeach; ?>
        </div>
        <button type="button" onclick="addStep()" class="btn-small">+ Add Step</button>

        <h3 style="margin-top:1.5rem;">Nutrition Information</h3>
        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:1rem;">
            <div><label>Calories</label><br><input type="number" name="calories" value="<?php echo $nutrition['calories'] ?? ''; ?>" min="0" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;"></div>
            <div><label>Protein (g)</label><br><input type="number" name="protein_g" value="<?php echo $nutrition['protein_g'] ?? ''; ?>" min="0" step="0.1" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;"></div>
            <div><label>Carbs (g)</label><br><input type="number" name="carbs_g" value="<?php echo $nutrition['carbs_g'] ?? ''; ?>" min="0" step="0.1" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;"></div>
            <div><label>Fat (g)</label><br><input type="number" name="fat_g" value="<?php echo $nutrition['fat_g'] ?? ''; ?>" min="0" step="0.1" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;"></div>
            <div><label>Fibre (g)</label><br><input type="number" name="fibre_g" value="<?php echo $nutrition['fibre_g'] ?? ''; ?>" min="0" step="0.1" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;"></div>
        </div>

        <div style="margin-top:1.5rem;">
            <button type="submit" class="btn-small">Save Changes</button>
            <a href="manage_recipes.php" style="margin-left:1rem;">Cancel</a>
        </div>
    </form>
</div>

<script>
let ingCount = <?php echo count($ingredients); ?>;
let stepCount = <?php echo count($steps); ?>;
function addIngredient() {
    const i = ingCount++;
    const div = document.createElement('div');
    div.className = 'ingredient-row';
    div.style = 'display:grid;grid-template-columns:3fr 1fr 1fr auto;gap:0.5rem;margin-bottom:0.5rem;';
    div.innerHTML = `<input type="text" name="ingredients[${i}][name]" placeholder="Name *" required style="padding:0.5rem;border:1px solid #ddd;border-radius:4px;"><input type="text" name="ingredients[${i}][quantity]" placeholder="Qty" style="padding:0.5rem;border:1px solid #ddd;border-radius:4px;"><input type="text" name="ingredients[${i}][unit]" placeholder="Unit" style="padding:0.5rem;border:1px solid #ddd;border-radius:4px;"><button type="button" onclick="removeRow(this)" style="padding:0.5rem;background:#e74c3c;color:#fff;border:none;border-radius:4px;cursor:pointer;">✕</button>`;
    document.getElementById('ingredients-container').appendChild(div);
}
function addStep() {
    const i = stepCount++;
    const div = document.createElement('div');
    div.className = 'step-row';
    div.style = 'margin-bottom:1rem;border:1px solid #eee;padding:1rem;border-radius:6px;';
    div.innerHTML = `<label style="font-weight:600;">Step ${i+1}</label><textarea name="steps[${i}][instruction]" rows="2" required style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;margin-top:0.3rem;"></textarea><label style="font-size:0.85rem;color:#666;">Step Image</label><br><input type="file" name="step_images[${i}]" accept="image/*"><button type="button" onclick="removeRow(this.closest('.step-row'))" style="margin-top:0.5rem;padding:4px 10px;background:#e74c3c;color:#fff;border:none;border-radius:4px;cursor:pointer;">Remove Step</button>`;
    document.getElementById('steps-container').appendChild(div);
}
function removeRow(el) { el.remove(); }
</script>

<?php include "../includes/footer.php"; ?>
