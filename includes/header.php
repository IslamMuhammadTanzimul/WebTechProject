<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . " | Recipe Platform" : "Recipe Platform"; ?></title>
    <link rel="stylesheet" href="/WebTechProject/assets/css/admin.css">
</head>

<body>
    <div class="wrapper">
        <nav class="sidebar">
            <div class="sidebar-brand">Recipe Platform</div>
            <ul class="sidebar-nav">
                <li><a href="/WebTechProject/admin/dashboard.php">Dashboard</a></li>
                <li><a href="/WebTechProject/admin/users.php">Users</a></li>
                <li><a href="/WebTechProject/admin/recipes.php">Recipes</a></li>
                <li><a href="/WebTechProject/admin/chef_verification.php">Chef Verification</a></li>
                <li><a href="/WebTechProject/admin/featured.php">Featured Content</a></li>
                <li><a href="/WebTechProject/admin/announcements.php">Announcements</a></li>
                <li><a href="/WebTechProject/admin/settings.php">Settings</a></li>
                <li><a href="/WebTechProject/admin/moderators.php">Moderation Team</a></li>
                <li><a href="/WebTechProject/admin/reports.php">Reports</a></li>
                <li><a href="/WebTechProject/admin/logout.php">Logout</a></li>
            </ul>
        </nav>
        <main class="content">