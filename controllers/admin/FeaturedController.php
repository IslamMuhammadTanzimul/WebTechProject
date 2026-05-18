<?php
require_once "../../includes/auth.php";
require_once "../../config/db_connect.php";
/** @var mysqli $conn */
require_once "../../models/AdminModel.php";

require_admin();

$action  = isset($_GET['action'])  ? $_GET['action']  : "";
$id      = isset($_GET['id'])      ? (int)$_GET['id'] : 0;
$chef_id = isset($_GET['chef_id']) ? (int)$_GET['chef_id'] : 0;

switch ($action) {
    case 'feature':
        if (!$id) {
            header("Location: /WebTechProject/admin/featured.php?err=Invalid+recipe");
            exit();
        }
        $count = count_featured_recipes($conn);
        if ($count >= 5) {
            header("Location: /WebTechProject/admin/featured.php?err=Maximum+5+featured+recipes+allowed");
            exit();
        }
        set_featured_recipe($conn, $id);
        header("Location: /WebTechProject/admin/featured.php?msg=Recipe+added+to+featured");
        break;

    case 'unfeature':
        if (!$id) {
            header("Location: /WebTechProject/admin/featured.php?err=Invalid+recipe");
            exit();
        }
        unset_featured_recipe($conn, $id);
        header("Location: /WebTechProject/admin/featured.php?msg=Recipe+removed+from+featured");
        break;

    case 'set_chef':
        if (!$chef_id) {
            header("Location: /WebTechProject/admin/featured.php?err=Please+select+a+chef");
            exit();
        }
        set_chef_of_the_week($conn, $chef_id);
        header("Location: /WebTechProject/admin/featured.php?msg=Chef+of+the+week+updated");
        break;

    default:
        header("Location: /WebTechProject/admin/featured.php?err=Invalid+action");
        break;
}
exit();
