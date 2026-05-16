<?php
require_once "../includes/auth.php";
require_once "../config/db_connect.php";
require_once "../models/AdminModel.php";

require_admin();

$featured_recipes  = get_featured_recipes($conn);
$published_recipes = get_published_recipes($conn);
$featured_count    = count_featured_recipes($conn);
$chef_of_week      = get_chef_of_the_week($conn);
$all_chefs         = get_all_chefs($conn);

$page_title = "Featured Content";
require_once "../includes/header.php";
?>

<h1>Featured Content</h1>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
<?php endif; ?>

<?php if (isset($_GET['err'])): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($_GET['err']); ?></div>
<?php endif; ?>

<!-- Chef of the Week -->
<div class="card">
    <h2>Chef of the Week</h2>

    <?php if ($chef_of_week): ?>
        <p style="margin-bottom: 12px;">Current: <strong><?php echo htmlspecialchars($chef_of_week['name']); ?></strong> (<?php echo htmlspecialchars($chef_of_week['username']); ?>)</p>
    <?php else: ?>
        <p style="margin-bottom: 12px;">No chef of the week selected.</p>
    <?php endif; ?>

    <form method="GET" action="../controllers/admin/FeaturedController.php" style="display: flex; gap: 8px;">
        <input type="hidden" name="action" value="set_chef">
        <select name="chef_id" style="margin: 0; padding: 8px;">
            <option value="">Select a chef</option>
            <?php foreach ($all_chefs as $chef): ?>
                <option value="<?php echo $chef['id']; ?>" <?php echo ($chef_of_week && $chef_of_week['id'] == $chef['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($chef['name']); ?> (<?php echo htmlspecialchars($chef['username']); ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Set Chef of the Week</button>
    </form>
</div>

<!-- Featured Recipes -->
<div class="card">
    <h2>Featured Recipes <span style="font-size: 13px; color: #777;">(<?php echo $featured_count; ?>/5)</span></h2>

    <?php if (empty($featured_recipes)): ?>
        <p style="margin-bottom: 12px;">No featured recipes yet.</p>
    <?php else: ?>
        <table style="margin-bottom: 16px;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Cuisine</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($featured_recipes as $recipe): ?>
                    <tr class="featured-row"
                        data-id="<?php echo $recipe['id']; ?>"
                        data-title="<?php echo htmlspecialchars($recipe['title']); ?>">
                        <td><?php echo $recipe['id']; ?></td>
                        <td><?php echo htmlspecialchars($recipe['title']); ?></td>
                        <td><?php echo htmlspecialchars($recipe['author_name']); ?></td>
                        <td><?php echo htmlspecialchars($recipe['cuisine_name'] ?? '-'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Action Panel for featured -->
    <div id="featured-action-panel" style="display: none; margin-bottom: 16px;">
        <p style="margin-bottom: 8px; font-size: 14px;">Selected: <strong id="featured-selected-title"></strong></p>
        <a id="btn-unfeature" href="#" class="btn btn-warning">Remove from Featured</a>
    </div>
</div>

<!-- Add Featured Recipe -->
<?php if ($featured_count < 5): ?>
    <div class="card">
        <h2>Add Featured Recipe</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($published_recipes)): ?>
                    <tr>
                        <td colspan="3">No more published recipes available.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($published_recipes as $recipe): ?>
                        <tr class="available-row"
                            data-id="<?php echo $recipe['id']; ?>"
                            data-title="<?php echo htmlspecialchars($recipe['title']); ?>">
                            <td><?php echo $recipe['id']; ?></td>
                            <td><?php echo htmlspecialchars($recipe['title']); ?></td>
                            <td><?php echo htmlspecialchars($recipe['author_name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Action Panel for available -->
        <div id="available-action-panel" style="display: none; margin-top: 12px;">
            <p style="margin-bottom: 8px; font-size: 14px;">Selected: <strong id="available-selected-title"></strong></p>
            <a id="btn-feature" href="#" class="btn btn-success">Add to Featured</a>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-error">Maximum 5 featured recipes reached. Remove one to add another.</div>
<?php endif; ?>

<style>
    .featured-row,
    .available-row {
        cursor: pointer;
    }

    .featured-row:hover,
    .available-row:hover {
        background: #f9f9f9;
    }

    .featured-row.selected,
    .available-row.selected {
        background: #eaf3ff;
    }
</style>

<script>
    const base = "/WebTechProject/controllers/admin/FeaturedController.php";

    // featured table
    document.querySelectorAll('.featured-row').forEach(row => {
        row.addEventListener('click', function() {
            document.querySelectorAll('.featured-row').forEach(r => r.classList.remove('selected'));
            this.classList.add('selected');

            const id = this.dataset.id;
            const title = this.dataset.title;

            document.getElementById('featured-selected-title').textContent = title;
            document.getElementById('btn-unfeature').href = `${base}?action=unfeature&id=${id}`;
            document.getElementById('featured-action-panel').style.display = 'block';
        });
    });

    // available table
    document.querySelectorAll('.available-row').forEach(row => {
        row.addEventListener('click', function() {
            document.querySelectorAll('.available-row').forEach(r => r.classList.remove('selected'));
            this.classList.add('selected');

            const id = this.dataset.id;
            const title = this.dataset.title;

            document.getElementById('available-selected-title').textContent = title;
            document.getElementById('btn-feature').href = `${base}?action=feature&id=${id}`;
            document.getElementById('available-action-panel').style.display = 'block';
        });
    });
</script>

<?php require_once "../includes/footer.php"; ?>