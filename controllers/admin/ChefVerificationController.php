<?php
require_once "../../includes/auth.php";
require_once "../../config/db_connect.php";
require_once "../../models/AdminModel.php";

require_admin();

$action     = isset($_GET['action'])  ? $_GET['action']       : "";
$request_id = isset($_GET['id'])      ? (int)$_GET['id']      : 0;
$user_id    = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$admin_id   = (int)$_SESSION['user_id'];

switch ($action) {
    case 'approve':
        if (!$request_id || !$user_id) {
            header("Location: /WebTechProject/admin/chef_verification.php?err=Invalid+request");
            exit();
        }
        approve_chef($conn, $user_id, $request_id, $admin_id);
        header("Location: /WebTechProject/admin/chef_verification.php?msg=Chef+approved+successfully");
        break;

    case 'reject':
        if (!$request_id) {
            header("Location: /WebTechProject/admin/chef_verification.php?err=Invalid+request");
            exit();
        }
        reject_chef_request($conn, $request_id, $admin_id);
        header("Location: /WebTechProject/admin/chef_verification.php?msg=Request+rejected");
        break;

    case 'revoke':
        if (!$user_id) {
            header("Location: /WebTechProject/admin/chef_verification.php?err=Invalid+user");
            exit();
        }
        revoke_chef($conn, $user_id, $admin_id);
        header("Location: /WebTechProject/admin/chef_verification.php?msg=Chef+status+revoked");
        break;

    default:
        header("Location: /WebTechProject/admin/chef_verification.php?err=Invalid+action");
        break;
}
exit();
