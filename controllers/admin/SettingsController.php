<?php
require_once "../../includes/auth.php";
require_once "../../config/db_connect.php";
require_once "../../models/AdminModel.php";

require_admin();

$action = isset($_GET['action']) ? $_GET['action'] : "";

switch ($action) {
    case 'save':
        $chef_verification_mode    = trim($_POST['chef_verification_mode']);
        $max_bookmarks             = trim($_POST['max_bookmarks']);
        $default_recipe_visibility = trim($_POST['default_recipe_visibility']);

        // validate
        if (!in_array($chef_verification_mode, ['moderator', 'admin'])) {
            header("Location: /WebTechProject/admin/settings.php?err=Invalid+verification+mode");
            exit();
        }

        if (!is_numeric($max_bookmarks) || $max_bookmarks < 0) {
            header("Location: /WebTechProject/admin/settings.php?err=Invalid+bookmark+limit");
            exit();
        }

        if (!in_array($default_recipe_visibility, ['published', 'draft'])) {
            header("Location: /WebTechProject/admin/settings.php?err=Invalid+recipe+visibility");
            exit();
        }

        update_setting($conn, 'chef_verification_mode',    $chef_verification_mode);
        update_setting($conn, 'max_bookmarks',             $max_bookmarks);
        update_setting($conn, 'default_recipe_visibility', $default_recipe_visibility);

        header("Location: /WebTechProject/admin/settings.php?msg=Settings+saved");
        break;

    default:
        header("Location: /WebTechProject/admin/settings.php?err=Invalid+action");
        break;
}
exit();
