<?php
session_start();

// redirect if already logged in
if (isset($_SESSION['user_id'])) {
    redirect_by_role($_SESSION['role']);
}

require_once "config/db_connect.php";

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

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];

        redirect_by_role($user['role']);
    } else {
        $error = "Invalid username or password.";
    }
}

function redirect_by_role($role) {
    switch ($role) {
        case 'admin':
            header("Location: /WebTechProject/admin/dashboard.php");
            break;
        case 'chef':
            header("Location: /WebTechProject/chef/dashboard.php");
            break;
        case 'moderator':
            header("Location: /WebTechProject/moderator/dashboard.php");
            break;
        default:
            header("Location: /WebTechProject/user/dashboard.php");
            break;
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Recipe Platform</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; color: #333; }
        .login-box {
            width: 360px;
            margin: 100px auto;
            background: #fff;
            padding: 32px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        h2 { margin-bottom: 24px; font-size: 20px; }
        label { font-size: 13px; font-weight: bold; display: block; margin-bottom: 4px; }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 3px;
            margin-bottom: 16px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background: #4a90d9;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
        }
        input[type="submit"]:hover { background: #3a7bc8; }
        .error {
            background: #f2dede;
            color: #a94442;
            padding: 10px;
            border-radius: 3px;
            font-size: 13px;
            margin-bottom: 16px;
        }
        .register-link {
            display: block;
            text-align: center;
            margin-top: 16px;
            font-size: 13px;
            color: #4a90d9;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Login</h2>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <input type="submit" value="Login">
        </form>

        <a href="/WebTechProject/register.php" class="register-link">New user? Register here</a>
    </div>
</body>
</html>