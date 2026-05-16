<?php
require_once "../includes/auth.php";
require_once "../config/db_connect.php";
require_once "../models/AdminModel.php";

require_admin();

$filters = [
    'author'  => isset($_GET['author'])  ? trim($_GET['author'])  : "",
    'cuisine' => isset($_GET['cuisine']) ? trim($_GET['cuisine']) : "",
    'diet'    => isset($_GET['diet'])    ? trim($_GET['diet'])    : "",
    'status'  => isset($_GET['status'])  ? trim($_GET['status'])  : "",
];

$recipes = get_all_recipes($conn, $filters);

$page_title = "Recipe Management";
require_once "../includes/header.php";
?>

<h1>Recipe Management</h1>

<!-- Filters -->
<form method="GET" style="margin-bottom: 16px; display: flex; gap: 8px; flex-wrap: wrap;">
    <input type="text" name="author" placeholder="Author" value="<?php echo htmlspecialchars($filters['author']);  ?>" style="width: 160px; margin: 0;">
    <input type="text" name="cuisine" placeholder="Cuisine" value="<?php echo htmlspecialchars($filters['cuisine']); ?>" style="width: 160px; margin: 0;">
    <input type="text" name="diet" placeholder="Diet" value="<?php echo htmlspecialchars($filters['diet']);    ?>" style="width: 160px; margin: 0;">
    <select name="status" style="margin: 0; padding: 8px;">
        <option value="">All Status</option>
        <option value="published" <?php echo $filters['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
        <option value="draft" <?php echo $filters['status'] === 'draft'     ? 'selected' : ''; ?>>Draft</option>
    </select>
    <button type="submit" class="btn btn-primary">Filter</button>
    <a href="recipes.php" class="btn btn-warning">Clear</a>
</form>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
<?php endif; ?>

<?php if (isset($_GET['err'])): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($_GET['err']); ?></div>
<?php endif; ?>

<div class="card">
    <table id="recipes-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Cuisine</th>
                <th>Diet</th>
                <th>Status</th>
                <th>Views</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($recipes)): ?>
                <tr>
                    <td colspan="8">No recipes found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($recipes as $recipe): ?>
                    <tr class="recipe-row"
                        data-id="<?php echo $recipe['id']; ?>"
                        data-title="<?php echo htmlspecialchars($recipe['title']); ?>">
                        <td><?php echo $recipe['id']; ?></td>
                        <td><?php echo htmlspecialchars($recipe['title']); ?></td>
                        <td><?php echo htmlspecialchars($recipe['author_name']); ?></td>
                        <td><?php echo htmlspecialchars($recipe['cuisine_name'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($recipe['diet_name'] ?? '-'); ?></td>
                        <td><?php echo ucfirst($recipe['status']); ?></td>
                        <td><?php echo $recipe['view_count']; ?></td>
                        <td><?php echo date('d M Y', strtotime($recipe['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Action Panel -->
<div class="card" id="action-panel" style="display: none;">
    <p style="margin-bottom: 12px; font-size: 14px;">Selected: <strong id="selected-title"></strong></p>
    <div style="display: flex; gap: 8px;">
        <a id="btn-delete" href="#" class="btn btn-danger">Delete Recipe</a>
    </div>
</div>

<style>
    .recipe-row {
        cursor: pointer;
    }

    .recipe-row:hover {
        background: #f9f9f9;
    }

    .recipe-row.selected {
        background: #eaf3ff;
    }
</style>

<script>
    const base = "/WebTechProject/controllers/admin/RecipeController.php";

    document.querySelectorAll('.recipe-row').forEach(row => {
        row.addEventListener('click', function() {
            document.querySelectorAll('.recipe-row').forEach(r => r.classList.remove('selected'));
            this.classList.add('selected');

            const id = this.dataset.id;
            const title = this.dataset.title;

            document.getElementById('selected-title').textContent = title;

            document.getElementById('btn-delete').href = `${base}?action=delete&id=${id}`;

            document.getElementById('action-panel').style.display = 'block';
        });
    });
</script>

<?php require_once "../includes/footer.php"; ?>