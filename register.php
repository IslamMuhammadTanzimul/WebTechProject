<?php
$page_title = "Register - Recipe Sharing Platform";
$base_url = "";
include "includes/header.php";
?>

<div class="card">
    <h2>Create User Account</h2>
    <p>Register as a Home Cook to browse recipes, save favourites, write reviews, create shopping lists, and plan meals.
    </p>
</div>

<div class="card">

    <?php if (isset($_SESSION["error"])) { ?>
        <p class="error"><?php echo $_SESSION["error"]; ?></p>
        <?php unset($_SESSION["error"]); ?>
    <?php } ?>

    <form method="post" action="controllers/register_action.php">

        <div class="form-group">
            <label>Full Name:</label>
            <input type="text" name="name" placeholder="Enter your full name">
        </div>

        <div class="form-group">
            <label>Username:</label>
            <input type="text" name="username" placeholder="Choose a username">
        </div>

        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" placeholder="Enter your email address">
        </div>

        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" placeholder="Enter password">
        </div>

        <div class="form-group">
            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" placeholder="Confirm password">
        </div>

        <div class="form-group">
            <label>Dietary Preferences:</label>

            <input type="checkbox" name="dietary_prefs[]" value="Vegetarian"> Vegetarian<br>
            <input type="checkbox" name="dietary_prefs[]" value="Vegan"> Vegan<br>
            <input type="checkbox" name="dietary_prefs[]" value="Keto"> Keto<br>
            <input type="checkbox" name="dietary_prefs[]" value="Gluten-Free"> Gluten-Free<br>
            <input type="checkbox" name="dietary_prefs[]" value="Halal"> Halal<br>
        </div>

        <input type="submit" name="register" value="Register" class="btn">

    </form>

    <br>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>

<?php include "includes/footer.php"; ?>