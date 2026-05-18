<?php
session_start();

if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['role']) {
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

header("Location: /WebTechProject/login.php");
exit();
?>