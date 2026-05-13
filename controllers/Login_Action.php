<?php
// controllers/login_action.php
session_start();

require_once "../config/db_connect.php";
require_once "../models/LoginModel.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = trim($_POST["username"]);
    $password = $_POST["password"];

    if (empty($identifier) || empty($password)) {
        $_SESSION["error"] = "Username/Email and password are required.";
        header("Location: ../login.php");
        exit();
    }

    $user = getUserByUsernameOrEmail($conn, $identifier);

    if ($user && password_verify($password, $user['password_hash'])) {

        if ($user['is_active'] == 0) {
            $_SESSION["error"] = "Your account has been deactivated. Contact Admin.";
            header("Location: ../login.php");
            exit();
        }

        $_SESSION["user_id"] = $user['id'];
        $_SESSION["name"] = $user['name'];
        $_SESSION["username"] = $user['username'];
        $_SESSION["role"] = $user['role'];

        if ($user['role'] == "user") {
            header("Location: ../user/dashboard.php");
        } elseif ($user['role'] == "chef") {
            header("Location: ../chef/dashboard.php");
        } elseif ($user['role'] == "moderator") {
            header("Location: ../moderator/dashboard.php");
        } elseif ($user['role'] == "admin") {
            header("Location: ../admin/dashboard.php");
        }
        exit();

    } else {
        $_SESSION["error"] = "Invalid username/email or password.";
        header("Location: ../login.php");
        exit();
    }

} else {
    header("Location: ../login.php");
    exit();
}
?>