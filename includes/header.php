<?php
require_once __DIR__ . "/auth.php";

if (!isset($page_title)) {
    $page_title = "Recipe Sharing Platform";
}

if (!isset($base_url)) {
    $base_url = "";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <!-- Add this -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
</head>

<body>

    <header class="main-header">
        <div class="container header-flex">
            <h1 class="logo">
                <a href="<?php echo $base_url; ?>index.php">
                    Recipe<span style="color:var(--rust)">.</span>Platform
                </a>
            </h1>

            <nav class="nav-menu">
                <a href="<?php echo $base_url; ?>index.php">Home</a>

                <?php if (is_logged_in()) { ?>
                    <?php if ($_SESSION['role'] == "user") { ?>
                        <a href="<?php echo $base_url; ?>user/dashboard.php">Dashboard</a>
                        <a href="<?php echo $base_url; ?>user/recipes.php">Recipes</a>
                        <a href="<?php echo $base_url; ?>user/saved.php">Saved</a>
                        <a href="<?php echo $base_url; ?>user/shopping_lists.php">Shopping Lists</a>
                    <?php } elseif ($_SESSION['role'] == "admin") { ?>
                        <a href="<?php echo $base_url; ?>admin/dashboard.php">Dashboard</a>
                        <a href="<?php echo $base_url; ?>admin/users.php">Users</a>
                        <a href="<?php echo $base_url; ?>admin/recipes.php">Recipes</a>
                        <a href="<?php echo $base_url; ?>admin/featured.php">Featured</a>
                    <?php } ?>

                    <a href="<?php echo $base_url; ?>logout.php">Logout</a>
                <?php } else { ?>
                    <a href="<?php echo $base_url; ?>login.php">Login</a>
                    <a href="<?php echo $base_url; ?>register.php" class="btn-small">Register</a>
                <?php } ?>
            </nav>
        </div>
    </header>

    <main class="container">