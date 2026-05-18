<?php
require_once "../../includes/auth.php";
require_once "../../config/db_connect.php";
/** @var mysqli $conn */
require_once "../../models/AdminModel.php";

require_admin();

$action = isset($_GET['action']) ? $_GET['action'] : "";
$id     = isset($_GET['id'])     ? (int)$_GET['id'] : 0;

if (!$id) {
    header("Location: /WebTechProject/admin/recipes.php?err=Invalid+recipe");
    exit();
}

switch ($action) {
    case 'delete':
        $confirmed = isset($_GET['confirmed']) ? $_GET['confirmed'] : "";

        if ($confirmed !== 'yes') {
            // show confirmation page
            require_once "../../config/db_connect.php";
            $sql    = "SELECT title FROM recipes WHERE id = ?";
            $stmt   = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $recipe = mysqli_fetch_assoc($result);

            if (!$recipe) {
                header("Location: /WebTechProject/admin/recipes.php?err=Recipe+not+found");
                exit();
            }
?>
            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="UTF-8">
                <title>Confirm Delete</title>
                <link rel="stylesheet" href="/WebTechProject/assets/css/admin.css">
            </head>

            <body>
                <div style="max-width: 480px; margin: 100px auto;">
                    <div class="card">
                        <h2>Confirm Delete</h2>
                        <p style="margin: 16px 0;">Are you sure you want to permanently delete <strong><?php echo htmlspecialchars($recipe['title']); ?></strong>? This cannot be undone.</p>
                        <div style="display: flex; gap: 8px;">
                            <a href="RecipeController.php?action=delete&id=<?php echo $id; ?>&confirmed=yes" class="btn btn-danger">Yes, Delete</a>
                            <a href="/WebTechProject/admin/recipes.php" class="btn btn-warning">Cancel</a>
                        </div>
                    </div>
                </div>
            </body>

            </html>
<?php
            exit();
        }

        delete_recipe($conn, $id);
        header("Location: /WebTechProject/admin/recipes.php?msg=Recipe+deleted+successfully");
        break;

    default:
        header("Location: /WebTechProject/admin/recipes.php?err=Invalid+action");
        break;
}
exit();
?>