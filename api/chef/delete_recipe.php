<?php
// api/chef/delete_recipe.php
session_start();
require_once "../../config/db_connect.php";
require_once "../../includes/auth.php";
require_role("chef", "../../");

$chef_id   = $_SESSION['user_id'];
$recipe_id = (int)($_GET['id'] ?? 0);

// Verify ownership
$chk = $conn->prepare("SELECT id FROM recipes WHERE id=? AND author_id=?");
$chk->bind_param("ii", $recipe_id, $chef_id);
$chk->execute();
if (!$chk->get_result()->fetch_assoc()) {
    $_SESSION['error'] = "Recipe not found.";
    header("Location: ../../chef/manage_recipes.php");
    exit();
}
$chk->close();

// Check if any user has bookmarked this recipe
$bk_stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM bookmarks WHERE recipe_id=?");
$bk_stmt->bind_param("i", $recipe_id);
$bk_stmt->execute();
$bk_count = $bk_stmt->get_result()->fetch_assoc()['cnt'];
$bk_stmt->close();

if ($bk_count > 0) {
    $_SESSION['error'] = "Cannot delete: this recipe has been bookmarked by $bk_count user(s). Please unpublish it instead.";
    header("Location: ../../chef/manage_recipes.php");
    exit();
}

$stmt = $conn->prepare("DELETE FROM recipes WHERE id=? AND author_id=?");
$stmt->bind_param("ii", $recipe_id, $chef_id);
$stmt->execute();
$stmt->close();

$_SESSION['success'] = "Recipe deleted successfully.";
header("Location: ../../chef/manage_recipes.php");
exit();
