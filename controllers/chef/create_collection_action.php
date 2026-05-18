<?php
// controllers/chef/create_collection_action.php
session_start();
require_once "../../config/db_connect.php";
require_once "../../includes/auth.php";
require_role("chef", "../../");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../chef/collections.php");
    exit();
}

$chef_id    = $_SESSION['user_id'];
$title      = trim($_POST['title'] ?? '');
$description= trim($_POST['description'] ?? '');
$is_public  = isset($_POST['is_public']) ? (int)$_POST['is_public'] : 1;

if (empty($title)) {
    $_SESSION['error'] = "Collection title is required.";
    header("Location: ../../chef/collections.php");
    exit();
}

$cover_image_path = null;
if (!empty($_FILES['cover_image']['name'])) {
    $upload_dir = "../../assets/uploads/";
    $ext = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
    $filename = "collection_" . uniqid() . "." . $ext;
    if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $upload_dir . $filename)) {
        $cover_image_path = "assets/uploads/" . $filename;
    }
}

$stmt = $conn->prepare("INSERT INTO collections (chef_id, title, description, cover_image_path, is_public) VALUES (?,?,?,?,?)");
$stmt->bind_param("isssi", $chef_id, $title, $description, $cover_image_path, $is_public);
$stmt->execute();
$stmt->close();

$_SESSION['success'] = "Collection created successfully.";
header("Location: ../../chef/collections.php");
exit();
