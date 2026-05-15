<?php
// login.php
$page_title = "Login - Recipe Sharing Platform";
$base_url = "";
include "includes/header.php";

if (is_logged_in()) {
    redirect_user_by_role($_SESSION['role']);
}
?>

<div style="max-width: 420px; margin: 40px auto;">
    <div class="card">
        <h2 style="font-family:'Playfair Display',serif; 
                   color:var(--brown); 
                   text-align:center; 
                   margin-bottom:20px;">
            Welcome back
        </h2>

        <?php if (isset($_SESSION["error"])): ?>
            <p class="error">
                <?= htmlspecialchars($_SESSION["error"]) ?>
            </p>
            <?php unset($_SESSION["error"]); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION["success"])): ?>
            <p class="success">
                <?= htmlspecialchars($_SESSION["success"]) ?>
            </p>
            <?php unset($_SESSION["success"]); ?>
        <?php endif; ?>

        <form method="post" action="controllers/login_action.php">
            <div class="form-group">
                <label>Username or Email</label>
                <input type="text" name="username"
                    placeholder="Enter username or email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password"
                    placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn"
                style="width:100%; margin-top:8px;">
                Login
            </button>
        </form>

        <p style="text-align:center; margin-top:20px; 
                  color:var(--muted); font-size:0.875rem;">
            Don't have an account?
            <a href="register.php"
                style="color:var(--rust); font-weight:500;">
                Register here
            </a>
        </p>
    </div>
</div>

<?php include "includes/footer.php"; ?>