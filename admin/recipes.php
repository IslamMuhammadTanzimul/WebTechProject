<?php
require_once "../includes/auth.php";
require_once "../config/db_connect.php";
require_once "../models/AdminModel.php";


// null colascating
$conn = $conn ?? ($pdo ?? null);

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
<div style="margin-bottom: 16px; display: flex; gap: 8px; flex-wrap: wrap;">
    <input type="text" id="filter-author" placeholder="Author" style="width: 150px; margin: 0;">
    <input type="text" id="filter-cuisine" placeholder="Cuisine" style="width: 150px; margin: 0;">
    <input type="text" id="filter-diet" placeholder="Diet" style="width: 150px; margin: 0;">
    <select id="filter-status" style="margin: 0; padding: 8px;">
        <option value="">All Status</option>
        <option value="published">Published</option>
        <option value="draft">Draft</option>
    </select>
    <button onclick="clearFilters()" class="btn btn-warning">Clear</button>
</div>
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
    let filterTimer = null;

    function attachRowHandlers() {
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
    }

    function filterRecipes() {
        const author = document.getElementById('filter-author').value;
        const cuisine = document.getElementById('filter-cuisine').value;
        const diet = document.getElementById('filter-diet').value;
        const status = document.getElementById('filter-status').value;

        const params = new URLSearchParams({
            author,
            cuisine,
            diet,
            status
        });

        fetch(`/WebTechProject/api/admin/recipe_filter.php?${params}`)
            .then(response => response.json())
            .then(recipes => {
                const tbody = document.querySelector('#recipes-table tbody');
                tbody.innerHTML = '';

                if (recipes.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8">No recipes found.</td></tr>';
                    return;
                }

                recipes.forEach(recipe => {
                    const date = new Date(recipe.created_at).toLocaleDateString('en-GB', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });

                    const tr = document.createElement('tr');
                    tr.className = 'recipe-row';
                    tr.dataset.id = recipe.id;
                    tr.dataset.title = recipe.title;

                    tr.innerHTML = `
                        <td>${recipe.id}</td>
                        <td>${recipe.title}</td>
                        <td>${recipe.author_name}</td>
                        <td>${recipe.cuisine_name ?? '-'}</td>
                        <td>${recipe.diet_name ?? '-'}</td>
                        <td>${recipe.status.charAt(0).toUpperCase() + recipe.status.slice(1)}</td>
                        <td>${recipe.view_count}</td>
                        <td>${date}</td>
                    `;
                    tbody.appendChild(tr);
                });

                attachRowHandlers();
                document.getElementById('action-panel').style.display = 'none';
            })
            .catch(err => console.error('Filter failed:', err));
    }

    function clearFilters() {
        document.getElementById('filter-author').value = '';
        document.getElementById('filter-cuisine').value = '';
        document.getElementById('filter-diet').value = '';
        document.getElementById('filter-status').value = '';
        filterRecipes();
    }

    // trigger on input with debounce
    ['filter-author', 'filter-cuisine', 'filter-diet'].forEach(id => {
        document.getElementById(id).addEventListener('input', function() {
            clearTimeout(filterTimer);
            filterTimer = setTimeout(filterRecipes, 300);
        });
    });

    document.getElementById('filter-status').addEventListener('change', filterRecipes);

    attachRowHandlers();
</script>
<?php require_once "../includes/footer.php"; ?>