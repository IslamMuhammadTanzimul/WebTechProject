<?php
// api/chef/toggle_chef_pick.php
session_start();
require_once "../../config/db_connect.php";
require_once "../../includes/auth.php";
require_role("chef", "../../");

$chef_id   = $_SESSION['user_id'];
$recipe_id = (int)($_GET['id'] ?? 0);
$action    = $_GET['action'] ?? '';

if (!in_array($action, ['add','remove'])) {
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

if ($action === 'add') {
    // Check current pick count
    $cnt = $conn->query("SELECT COUNT(*) AS c FROM recipes WHERE author_id=$chef_id AND is_chef_pick=1")->fetch_assoc()['c'];
    if ($cnt >= 3) {
        $_SESSION['error'] = "You can only have up to 3 Chef's Picks at a time.";
        header("Location: ../../chef/manage_recipes.php");
        exit();
    }
    $new_pick = 1;
} else {
    $new_pick = 0;
}

$stmt = $conn->prepare("UPDATE recipes SET is_chef_pick=? WHERE id=? AND author_id=?");
$stmt->bind_param("iii", $new_pick, $recipe_id, $chef_id);
$stmt->execute();
$stmt->close();

$_SESSION['success'] = $action === 'add' ? "Recipe marked as Chef's Pick." : "Chef's Pick removed.";
header("Location: ../../chef/manage_recipes.php");
exit();
