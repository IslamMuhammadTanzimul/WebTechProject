<?php
// api/chef/toggle_publish.php
session_start();
require_once "../../config/db_connect.php";
require_once "../../includes/auth.php";
require_role("chef", "../../");

$chef_id   = $_SESSION['user_id'];
$recipe_id = (int)($_GET['id'] ?? 0);
$action    = $_GET['action'] ?? '';

if (!in_array($action, ['publish','unpublish'])) {
    header("Location: ../../chef/manage_recipes.php");
    exit();
}

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

$new_status = $action === 'publish' ? 'published' : 'draft';
$stmt = $conn->prepare("UPDATE recipes SET status=? WHERE id=? AND author_id=?");
$stmt->bind_param("sii", $new_status, $recipe_id, $chef_id);
$stmt->execute();
$stmt->close();

$_SESSION['success'] = "Recipe " . ($action === 'publish' ? "published" : "unpublished") . " successfully.";
header("Location: ../../chef/manage_recipes.php");
exit();
