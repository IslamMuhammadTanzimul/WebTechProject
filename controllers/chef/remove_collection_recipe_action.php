<?php
// controllers/chef/remove_collection_recipe_action.php
session_start();
require_once "../../config/db_connect.php";
require_once "../../includes/auth.php";
require_role("chef", "../../");

$chef_id       = $_SESSION['user_id'];
$collection_id = (int)($_GET['collection_id'] ?? 0);
$recipe_id     = (int)($_GET['recipe_id'] ?? 0);

// Verify collection ownership
$chk = $conn->prepare("SELECT id FROM collections WHERE id=? AND chef_id=?");
$chk->bind_param("ii", $collection_id, $chef_id);
$chk->execute();
if (!$chk->get_result()->fetch_assoc()) {
    $_SESSION['error'] = "Collection not found.";
    header("Location: ../../chef/collections.php");
    exit();
}
$chk->close();

$stmt = $conn->prepare("DELETE FROM collection_recipes WHERE collection_id=? AND recipe_id=?");
$stmt->bind_param("ii", $collection_id, $recipe_id);
$stmt->execute();
$stmt->close();

$_SESSION['success'] = "Recipe removed from collection.";
header("Location: ../../chef/collection_recipes.php?id=$collection_id");
exit();
