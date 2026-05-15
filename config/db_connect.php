<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "recipe_share_db";
// notOO oriented
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
