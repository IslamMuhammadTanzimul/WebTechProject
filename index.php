<?php
$page_title = "Home - Recipe Sharing Platform";
$base_url = "";
include "includes/header.php";

if (is_logged_in()) {
    $role = $_SESSION['role'];
    $allowed = ['user', 'chef', 'moderator', 'admin'];
    if (in_array($role, $allowed)) {
        $dashboard_url = htmlspecialchars($role . '/dashboard.php');
    } else {
        session_destroy();
        header('Location: /login.php');
        exit;
    }
}
?>

<!-- HERO -->
<div class="card" style="display:grid; grid-template-columns:1fr 1fr; 
                          gap:40px; align-items:center; padding:48px;">
    <div>
        <span style="display:inline-block; background:var(--sage-light); 
                     color:var(--sage); font-size:0.75rem; font-weight:500;
                     letter-spacing:0.08em; text-transform:uppercase;
                     padding:5px 14px; border-radius:100px; margin-bottom:20px;">
            ✦ Community Recipe Platform
        </span>
        <h1 style="font-family:'Playfair Display',serif; font-size:2.4rem;
                   color:var(--brown); line-height:1.2; margin-bottom:16px;">
            Cook, Share &<br>
            <em style="color:var(--rust);">Inspire</em> Others
        </h1>
        <p style="color:var(--muted); font-weight:300; 
                  margin-bottom:32px; max-width:400px;">
            Discover recipes from verified chefs and home cooks.
            Save favourites, plan meals, and build your shopping
            list — all in one place.
        </p>

        <?php if (is_logged_in()): ?>
            <a href="<?= $dashboard_url ?>" class="btn">
                → Go to Dashboard
            </a>
        <?php else: ?>
            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                <a href="register.php" class="btn">→ Create Account</a>
                <a href="login.php" class="btn btn-secondary">Login</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recipe preview cards -->
    <div style="border-radius:var(--radius-lg); overflow:hidden; 
            height:380px; box-shadow:var(--shadow-md);">
        <img src="assets/uploads/hero.jpg"
            alt="Delicious recipes"
            style="width:100%; height:100%; object-fit:cover;">
    </div>
</div>

<!-- FEATURES -->
<div style="margin-bottom:20px;">
    <p style="font-size:0.75rem; font-weight:500; letter-spacing:0.1em;
              text-transform:uppercase; color:var(--rust); margin-bottom:8px;">
        What you get
    </p>
    <h2 style="font-family:'Playfair Display',serif; font-size:1.6rem;
               color:var(--brown); margin-bottom:24px;">
        Everything a home cook needs
    </h2>

    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px;">
        <?php
        $features = [
            ['🔍', 'Smart Search', 'Filter by cuisine, diet, and ingredients — results update instantly.'],
            ['🔖', 'Bookmarks', 'Save any recipe to your personal collection instantly.'],
            ['🛒', 'Shopping Lists', 'Add recipe ingredients to your list in one click.'],
            ['📅', 'Meal Planner', 'Drag recipes into your weekly calendar.'],
            ['⭐', 'Reviews', 'Rate and review recipes you have tried.'],
            ['👨‍🍳', 'Follow Chefs', 'Get new recipes from your favourite chefs in your feed.'],
        ];
        foreach ($features as $f): ?>
            <div class="card" style="margin-bottom:0; padding:22px 20px;">
                <div style="width:40px; height:40px; background:var(--sage-light);
                            border-radius:10px; display:flex; align-items:center;
                            justify-content:center; font-size:1.2rem;
                            margin-bottom:12px;">
                    <?= $f[0] ?>
                </div>
                <div style="font-family:'Playfair Display',serif; font-size:0.95rem;
                            font-weight:700; color:var(--brown); margin-bottom:6px;">
                    <?= $f[1] ?>
                </div>
                <p style="font-size:0.82rem; color:var(--muted); 
                          font-weight:300; line-height:1.5;">
                    <?= $f[2] ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- CTA -->
<div style="background:var(--rust); border-radius:var(--radius-lg);
            padding:48px 40px; display:flex; align-items:center;
            justify-content:space-between; gap:24px; margin-bottom:20px;">
    <div>
        <h2 style="font-family:'Playfair Display',serif; color:white;
                   font-size:1.6rem; margin-bottom:8px;">
            Ready to start cooking?
        </h2>
        <p style="color:rgba(255,255,255,0.7); font-weight:300; font-size:0.9rem;">
            Free to register, always.
        </p>
    </div>
    <?php if (!is_logged_in()): ?>
        <a href="register.php"
            style="background:white; color:var(--rust); padding:14px 28px;
                  border-radius:100px; font-weight:500; font-size:0.9rem;
                  text-decoration:none; white-space:nowrap; flex-shrink:0;
                  transition:transform 0.15s;">
            Create Free Account →
        </a>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>