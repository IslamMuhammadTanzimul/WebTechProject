<?php
// controllers/chef/edit_recipe_action.php
session_start();
require_once "../../config/db_connect.php";
require_once "../../includes/auth.php";
require_role("chef", "../../");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../chef/manage_recipes.php");
    exit();
}

$chef_id   = $_SESSION['user_id'];
$recipe_id = (int)($_POST['recipe_id'] ?? 0);

// Verify ownership
$chk = $conn->prepare("SELECT id, featured_image_path FROM recipes WHERE id=? AND author_id=?");
$chk->bind_param("ii", $recipe_id, $chef_id);
$chk->execute();
$existing = $chk->get_result()->fetch_assoc();
$chk->close();

if (!$existing) {
    $_SESSION['error'] = "Recipe not found.";
    header("Location: ../../chef/manage_recipes.php");
    exit();
}

$title        = trim($_POST['title'] ?? '');
$description  = trim($_POST['description'] ?? '');
$cuisine_id   = !empty($_POST['cuisine_id']) ? (int)$_POST['cuisine_id'] : null;
$diet_type_id = !empty($_POST['diet_type_id']) ? (int)$_POST['diet_type_id'] : null;
$difficulty   = $_POST['difficulty'] ?? 'easy';
$prep_time    = (int)($_POST['prep_time_mins'] ?? 0);
$cook_time    = (int)($_POST['cook_time_mins'] ?? 0);
$servings     = (int)($_POST['servings'] ?? 1);
$status       = in_array($_POST['status'] ?? '', ['draft','published']) ? $_POST['status'] : 'draft';

if (empty($title) || empty($description)) {
    $_SESSION['error'] = "Title and description are required.";
    header("Location: ../../chef/edit_recipe.php?id=$recipe_id");
    exit();
}

// Handle featured image
$featured_image_path = $existing['featured_image_path'];
if (!empty($_FILES['featured_image']['name'])) {
    $upload_dir = "../../assets/uploads/";
    $ext = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
    $filename = "recipe_" . uniqid() . "." . $ext;
    if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $upload_dir . $filename)) {
        $featured_image_path = "assets/uploads/" . $filename;
    }
}

$stmt = $conn->prepare("UPDATE recipes SET cuisine_id=?, title=?, description=?, diet_type_id=?, difficulty=?, prep_time_mins=?, cook_time_mins=?, servings=?, featured_image_path=?, status=? WHERE id=? AND author_id=?");
$stmt->bind_param("issisissssii", $cuisine_id, $title, $description, $diet_type_id, $difficulty, $prep_time, $cook_time, $servings, $featured_image_path, $status, $recipe_id, $chef_id);
$stmt->execute();
$stmt->close();

// Replace ingredients
$conn->query("DELETE FROM ingredients WHERE recipe_id=$recipe_id");
if (!empty($_POST['ingredients']) && is_array($_POST['ingredients'])) {
    $ing_stmt = $conn->prepare("INSERT INTO ingredients (recipe_id, name, quantity, unit, order_index) VALUES (?,?,?,?,?)");
    foreach ($_POST['ingredients'] as $idx => $ing) {
        $name = trim($ing['name'] ?? '');
        if (empty($name)) continue;
        $qty  = trim($ing['quantity'] ?? '');
        $unit = trim($ing['unit'] ?? '');
        $ing_stmt->bind_param("isssi", $recipe_id, $name, $qty, $unit, $idx);
        $ing_stmt->execute();
    }
    $ing_stmt->close();
}

// Replace steps
$conn->query("DELETE FROM steps WHERE recipe_id=$recipe_id");
if (!empty($_POST['steps']) && is_array($_POST['steps'])) {
    $step_stmt = $conn->prepare("INSERT INTO steps (recipe_id, instruction, step_image_path, step_order) VALUES (?,?,?,?)");
    foreach ($_POST['steps'] as $idx => $step) {
        $instruction = trim($step['instruction'] ?? '');
        if (empty($instruction)) continue;
        $step_img = null;
        if (!empty($_FILES['step_images']['name'][$idx])) {
            $upload_dir = "../../assets/uploads/";
            $ext = pathinfo($_FILES['step_images']['name'][$idx], PATHINFO_EXTENSION);
            $fname = "step_" . uniqid() . "." . $ext;
            if (move_uploaded_file($_FILES['step_images']['tmp_name'][$idx], $upload_dir . $fname)) {
                $step_img = "assets/uploads/" . $fname;
            }
        }
        $order = $idx + 1;
        $step_stmt->bind_param("issi", $recipe_id, $instruction, $step_img, $order);
        $step_stmt->execute();
    }
    $step_stmt->close();
}

// Upsert nutrition
$cal   = !empty($_POST['calories']) ? (int)$_POST['calories'] : null;
$prot  = !empty($_POST['protein_g']) ? (float)$_POST['protein_g'] : null;
$carbs = !empty($_POST['carbs_g']) ? (float)$_POST['carbs_g'] : null;
$fat   = !empty($_POST['fat_g']) ? (float)$_POST['fat_g'] : null;
$fibre = !empty($_POST['fibre_g']) ? (float)$_POST['fibre_g'] : null;

$nut_stmt = $conn->prepare("INSERT INTO nutrition_info (recipe_id, calories, protein_g, carbs_g, fat_g, fibre_g) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE calories=VALUES(calories), protein_g=VALUES(protein_g), carbs_g=VALUES(carbs_g), fat_g=VALUES(fat_g), fibre_g=VALUES(fibre_g)");
$nut_stmt->bind_param("iidddd", $recipe_id, $cal, $prot, $carbs, $fat, $fibre);
$nut_stmt->execute();
$nut_stmt->close();

$_SESSION['success'] = "Recipe updated successfully.";
header("Location: ../../chef/manage_recipes.php");
exit();
