<?php
// includes/chef_nav.php
// Chef role navigation bar — included in chef pages
// The main header.php does not have chef nav, so this file provides it.
// Include AFTER header.php in chef pages if you want a secondary nav bar.
// (Adding chef nav to header.php directly would modify an existing file.)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'chef') return;
?>
<div style="background:#fff;border-bottom:1px solid #eee;padding:0.5rem 0;margin-bottom:1rem;">
    <div class="container" style="display:flex;gap:1.5rem;flex-wrap:wrap;align-items:center;">
        <strong style="color:var(--rust);">Chef Panel:</strong>
        <a href="<?php echo $base_url; ?>chef/dashboard.php">Dashboard</a>
        <a href="<?php echo $base_url; ?>chef/manage_recipes.php">My Recipes</a>
        <a href="<?php echo $base_url; ?>chef/create_recipe.php">+ New Recipe</a>
        <a href="<?php echo $base_url; ?>chef/collections.php">Collections</a>
        <a href="<?php echo $base_url; ?>chef/analytics.php">Analytics</a>
        <a href="<?php echo $base_url; ?>chef/reviews.php">Reviews</a>
        <a href="<?php echo $base_url; ?>chef/followers.php">Followers</a>
        <a href="<?php echo $base_url; ?>chef/profile.php">Profile</a>
    </div>
</div>
