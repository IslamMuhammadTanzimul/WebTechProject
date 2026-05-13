<?php
session_start();

require_once "../config/db_connect.php";

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

    $check_sql = "SELECT id FROM users WHERE username = ? OR email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $username, $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $_SESSION["error"] = "Username or email already exists.";
        $check_stmt->close();
        header("Location: ../register.php");
        exit();
    }

    $check_stmt->close();

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $dietary_json = json_encode($dietary_prefs);
    $role = "user";
    $is_active = 1;
    $chef_verified = 0;

    $insert_sql = "INSERT INTO users 
        (name, username, email, password_hash, role, dietary_prefs, is_active, chef_verified) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param(
        "ssssssii",
        $name,
        $username,
        $email,
        $password_hash,
        $role,
        $dietary_json,
        $is_active,
        $chef_verified
    );

    if ($insert_stmt->execute()) {
        $_SESSION["user_id"] = $insert_stmt->insert_id;
        $_SESSION["name"] = $name;
        $_SESSION["username"] = $username;
        $_SESSION["role"] = $role;

        $insert_stmt->close();
        header("Location: ../user/dashboard.php");
        exit();
    } else {
        $_SESSION["error"] = "Registration failed. Please try again.";
        $insert_stmt->close();
        header("Location: ../register.php");
        exit();
    }

} else {
    header("Location: ../register.php");
    exit();
}
?>