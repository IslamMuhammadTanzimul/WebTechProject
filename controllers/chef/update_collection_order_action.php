<?php
// controllers/chef/update_collection_order_action.php
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
$display_order = (int)($_POST['display_order'] ?? 0);

// Verify ownership
$chk = $conn->prepare("SELECT id FROM collections WHERE id=? AND chef_id=?");
$chk->bind_param("ii", $collection_id, $chef_id);
$chk->execute();
if (!$chk->get_result()->fetch_assoc()) {
    $_SESSION['error'] = "Collection not found.";
    header("Location: ../../chef/collections.php");
    exit();
}
$chk->close();

$stmt = $conn->prepare("UPDATE collection_recipes SET display_order=? WHERE collection_id=? AND recipe_id=?");
$stmt->bind_param("iii", $display_order, $collection_id, $recipe_id);
$stmt->execute();
$stmt->close();

$_SESSION['success'] = "Order updated.";
header("Location: ../../chef/collection_recipes.php?id=$collection_id");
exit();
