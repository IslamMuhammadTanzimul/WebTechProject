<?php
require_once "../../includes/auth.php";
require_once "../../config/db_connect.php";
/** @var mysqli $conn */
require_once "../../models/AdminModel.php";

require_admin();

$action = isset($_GET['action']) ? $_GET['action'] : "";
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header("Location: /WebTechProject/admin/users.php?err=Invalid+user");
    exit();
}

// prevent admin from modifying own account
if ($id === (int)$_SESSION['user_id']) {
    header("Location: /WebTechProject/admin/users.php?err=You+cannot+modify+your+own+account");
    exit();
}

$user = get_user_by_id($conn, $id);

if (!$user) {
    header("Location: /WebTechProject/admin/users.php?err=User+not+found");
    exit();
}

switch ($action) {
    case 'activate':
        update_user_status($conn, $id, 1);
        header("Location: /WebTechProject/admin/users.php?msg=User+activated");
        break;

    case 'deactivate':
        update_user_status($conn, $id, 0);
        header("Location: /WebTechProject/admin/users.php?msg=User+deactivated");
        break;

    case 'promote':
        if ($user['role'] !== 'user') {
            header("Location: /WebTechProject/admin/users.php?err=Only+regular+users+can+be+promoted");
            exit();
        }
        update_user_role($conn, $id, 'moderator');
        header("Location: /WebTechProject/admin/users.php?msg=User+promoted+to+moderator");
        break;

    case 'demote':
        if ($user['role'] !== 'moderator') {
            header("Location: /WebTechProject/admin/users.php?err=Only+moderators+can+be+demoted");
            exit();
        }
        update_user_role($conn, $id, 'user');
        header("Location: /WebTechProject/admin/users.php?msg=Moderator+demoted+to+user");
        break;

    default:
        header("Location: /WebTechProject/admin/users.php?err=Invalid+action");
        break;
}
exit();
