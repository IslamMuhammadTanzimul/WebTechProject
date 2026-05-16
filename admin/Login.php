<?php
session_start();
require_once "../config/db_connect.php";
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    header("Location: /WebTechProject/admin/dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $sql    = "SELECT id, username, password_hash, role FROM users WHERE username = ?";
    $stmt   = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user   = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password_hash']) && $user['role'] === 'admin') {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];

        header("Location: /WebTechProject/admin/dashboard.php");
        exit();
    } else {
        $error = "Invalid credentials or not an admin.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Login | Recipe Platform</title>
    <link rel="stylesheet" href="/WebTechProject/assets/css/admin.css">
    <style>
        .login-box {
            width: 340px;
            margin: 100px auto;
            background: #fff;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .login-box h2 {
            margin-bottom: 20px;
        }

        .login-box input[type="submit"] {
            width: 100%;
            padding: 10px;
            background: #4a90d9;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <h2>Admin Login</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post">
            <label>Username</label>
            <input type="text" name="username" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <input type="submit" value="Login">
        </form>
    </div>
</body>

</html>