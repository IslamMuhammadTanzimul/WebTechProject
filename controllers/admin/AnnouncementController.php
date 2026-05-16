<?php
require_once "../../includes/auth.php";
require_once "../../config/db_connect.php";
require_once "../../models/AdminModel.php";

require_admin();

$action = isset($_GET['action']) ? $_GET['action'] : "";
$id     = isset($_GET['id'])     ? (int)$_GET['id'] : 0;

switch ($action) {
    case 'create':
        $title   = trim($_POST['title']);
        $message = trim($_POST['message']);
        $user_id = (int)$_SESSION['user_id'];

        if (empty($title) || empty($message)) {
            header("Location: /WebTechProject/admin/announcements.php?err=Title+and+message+are+required");
            exit();
        }

        create_announcement($conn, $title, $message, $user_id);
        header("Location: /WebTechProject/admin/announcements.php?msg=Announcement+posted");
        break;

    case 'delete':
        if (!$id) {
            header("Location: /WebTechProject/admin/announcements.php?err=Invalid+announcement");
            exit();
        }
        delete_announcement($conn, $id);
        header("Location: /WebTechProject/admin/announcements.php?msg=Announcement+deleted");
        break;

    default:
        header("Location: /WebTechProject/admin/announcements.php?err=Invalid+action");
        break;
}
exit();
