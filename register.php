<?php
$page_title = "Register - Recipe Sharing Platform";
$base_url = "";
include "includes/header.php";

// Add this — was missing
if (is_logged_in()) {
    redirect_user_by_role($_SESSION['role']);
}
?>

<div style="max-width: 560px; margin: 40px auto;">
    <div class="card">
        <h2 style="font-family:'Playfair Display',serif; 
                   color:var(--brown); margin-bottom:6px;">
            Create Account
        </h2>
        <p style="color:var(--muted); font-size:0.875rem; 
                  margin-bottom:24px;">
            Register as a Home Cook to browse recipes, save
            favourites, and plan your meals.
        </p>

        <?php if (isset($_SESSION["error"])): ?>
            <p class="error">
                <?= htmlspecialchars($_SESSION["error"]) ?> <!-- was missing -->
            </p>
            <?php unset($_SESSION["error"]); ?>
        <?php endif; ?>

        <form method="post" action="controllers/register_action.php">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name"
                    placeholder="Enter your full name" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username"
                    placeholder="Choose a username" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"
                    placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password"
                    placeholder="Min. 8 characters" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password"
                    placeholder="Repeat password" required>
            </div>
            <div class="form-group">
                <label>Dietary Preferences
                    <span style="color:var(--muted); 
                                 font-weight:300;">(optional)</span>
                </label>
                <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:6px;">
                    <?php
                    $prefs = [
                        'Vegetarian',
                        'Vegan',
                        'Keto',
                        'Gluten-Free',
                        'Halal'
                    ];
                    foreach ($prefs as $pref): ?>
                        <label style="font-weight:400; 
                                      display:flex; 
                                      align-items:center; gap:6px;">
                            <input type="checkbox"
                                name="dietary_prefs[]"
                                value="<?= $pref ?>"
                                style="width:auto;">
                            <?= $pref ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="submit" name="register" class="btn"
                style="width:100%; margin-top:8px;">
                Create Account
            </button>
        </form>

        <p style="text-align:center; margin-top:20px; 
                  color:var(--muted); font-size:0.875rem;">
            Already have an account?
            <a href="login.php"
                style="color:var(--rust); font-weight:500;">
                Login here
            </a>
        </p>
    </div>
</div>