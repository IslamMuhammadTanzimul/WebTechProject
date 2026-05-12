<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function get_logged_in_user_id() {
    if (isset($_SESSION['user_id'])) {
        return $_SESSION['user_id'];
    }
    return null;
}

function get_logged_in_role() {
    if (isset($_SESSION['role'])) {
        return $_SESSION['role'];
    }
    return null;
}

function require_login($base_path = "") {
    if (!is_logged_in()) {
        header("Location: " . $base_path . "login.php");
        exit();
    }
}

function require_role($allowed_role, $base_path = "") {
    require_login($base_path);

    if ($_SESSION['role'] != $allowed_role) {
        header("Location: " . $base_path . "index.php");
        exit();
    }
}

function redirect_user_by_role($role) {
    if ($role == "user") {
        header("Location: user/dashboard.php");
        exit();
    } elseif ($role == "chef") {
        header("Location: chef/dashboard.php");
        exit();
    } elseif ($role == "moderator") {
        header("Location: moderator/dashboard.php");
        exit();
    } elseif ($role == "admin") {
        header("Location: admin/dashboard.php");
        exit();
    } else {
        header("Location: index.php");
        exit();
    }
}
?>