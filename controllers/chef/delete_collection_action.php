<?php
// controllers/chef/delete_collection_action.php
session_start();
require_once "../../config/db_connect.php";
require_once "../../includes/auth.php";
require_role("chef", "../../");

$chef_id       = $_SESSION['user_id'];
$collection_id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("DELETE FROM collections WHERE id=? AND chef_id=?");
$stmt->bind_param("ii", $collection_id, $chef_id);
$stmt->execute();
$stmt->close();

$_SESSION['success'] = "Collection deleted.";
header("Location: ../../chef/collections.php");
exit();
