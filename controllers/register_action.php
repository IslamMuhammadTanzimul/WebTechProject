<?php
session_start();

require_once "../config/db_connect.php";
require_once "../models/UserModel.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (isset($_POST["dietary_prefs"])) {
        $dietary_prefs = $_POST["dietary_prefs"];
    } else {
        $dietary_prefs = array();
    }

    if (empty($name) || empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION["error"] = "All required fields must be filled.";
        header("Location: ../register.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION["error"] = "Invalid email format.";
        header("Location: ../register.php");
        exit();
    }

    if (strlen($password) < 6) {
        $_SESSION["error"] = "Password must be at least 6 characters.";
        header("Location: ../register.php");
        exit();
    }

    if ($password != $confirm_password) {
        $_SESSION["error"] = "Password and confirm password do not match.";
        header("Location: ../register.php");
        exit();
    }

    if (checkUserExists($conn, $username, $email)) {
        $_SESSION["error"] = "Username or email already exists.";
        header("Location: ../register.php");
        exit();
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $dietary_json = json_encode($dietary_prefs);

    $new_user_id = registerUser($conn, $name, $username, $email, $password_hash, $dietary_json);

    if ($new_user_id != false) {
        $_SESSION["user_id"] = $new_user_id;
        $_SESSION["name"] = $name;
        $_SESSION["username"] = $username;
        $_SESSION["role"] = "user";

        header("Location: ../user/dashboard.php");
        exit();
    } else {
        $_SESSION["error"] = "Registration failed. Please try again.";
        header("Location: ../register.php");
        exit();
    }

} else {
    header("Location: ../register.php");
    exit();
}
?>