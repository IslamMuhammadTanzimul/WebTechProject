<?php
// login.php
$page_title = "Login - Recipe Sharing Platform";
$base_url = "";
include "includes/header.php";

if (is_logged_in()) {
    redirect_user_by_role($_SESSION['role']);
}
?>

<div class="card" style="max-width: 400px; margin: 0 auto;">
    <h2 style="text-align: center;">Login</h2>

    <?php if (isset($_SESSION["error"])) { ?>
        <p class="error" style="text-align: center;">
            <?php echo $_SESSION["error"]; ?>
        </p>
        <?php unset($_SESSION["error"]); ?>
    <?php } ?>

    <?php if (isset($_SESSION["success"])) { ?>
        <p class="success" style="text-align: center;">
            <?php echo $_SESSION["success"]; ?>
        </p>
        <?php unset($_SESSION["success"]); ?>
    <?php } ?>

    <form method="post" action="controllers/login_action.php">
        <div class="form-group">
            <label>Username or Email:</label>
            <input type="text" name="username" placeholder="Enter username or email" required>
        </div>

        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" placeholder="Enter password" required>
        </div>

        <input type="submit" value="Login" class="btn" style="width: 100%;">
    </form>

    <br>
    <p style="text-align: center;">Don't have an account? <a href="register.php">Register here</a></p>
</div>

<?php include "includes/footer.php"; ?>