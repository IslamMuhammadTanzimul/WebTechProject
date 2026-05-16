<?php
$host   = "localhost";
$db     = "recipe_share_db";
$user   = "root";
$pass   = "";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
