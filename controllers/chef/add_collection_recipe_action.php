<?php
// controllers/chef/add_collection_recipe_action.php
session_start();
require_once "../../config/db_connect.php";
require_once "../../includes/auth.php";
require_role("chef", "../../");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../chef/collections.php");
    exit();
}

$chef_id       = $_SESSION['user_id'];
$collection_id = (int)($_POST['collection_id'] ?? 0);
$recipe_id     = (int)($_POST['recipe_id'] ?? 0);

// Verify collection belongs to chef
$chk = $conn->prepare("SELECT id FROM collections WHERE id=? AND chef_id=?");
$chk->bind_param("ii", $collection_id, $chef_id);
$chk->execute();
if (!$chk->get_result()->fetch_assoc()) {
    $_SESSION['error'] = "Collection not found.";
    header("Location: ../../chef/collections.php");
    exit();
}
$chk->close();

// Verify recipe belongs to chef and is published
$rchk = $conn->prepare("SELECT id FROM recipes WHERE id=? AND author_id=? AND status='published'");
$rchk->bind_param("ii", $recipe_id, $chef_id);
$rchk->execute();
if (!$rchk->get_result()->fetch_assoc()) {
    $_SESSION['error'] = "Recipe not found or not published.";
    header("Location: ../../chef/collection_recipes.php?id=$collection_id");
    exit();
}
$rchk->close();

// Get max display_order
$max_res = $conn->query("SELECT COALESCE(MAX(display_order),0)+1 AS next_order FROM collection_recipes WHERE collection_id=$collection_id");
$next_order = $max_res->fetch_assoc()['next_order'];

$stmt = $conn->prepare("INSERT IGNORE INTO collection_recipes (collection_id, recipe_id, display_order) VALUES (?,?,?)");
$stmt->bind_param("iii", $collection_id, $recipe_id, $next_order);
$stmt->execute();
$stmt->close();

$_SESSION['success'] = "Recipe added to collection.";
header("Location: ../../chef/collection_recipes.php?id=$collection_id");
exit();
