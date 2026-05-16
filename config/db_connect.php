<?php
$host   = "localhost";
$db     = "p11_recipe_platform";
$user   = "root";
$pass   = "";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
