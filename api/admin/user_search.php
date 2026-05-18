<?php
require_once "../../includes/auth.php";
require_once "../../config/db_connect.php";
/** @var mysqli $conn */
require_once "../../models/AdminModel.php";

require_admin();

header('Content-Type: application/json');

$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$users  = get_all_users($conn, $search);

echo json_encode($users);
