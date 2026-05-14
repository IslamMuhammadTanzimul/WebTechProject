<?php
// api/user/delete_shopping_list.php
session_start();

require_once "../../config/db_connect.php";
require_once "../../includes/auth.php";
require_once "../../models/ShoppingListModel.php";

header('Content-Type: application/json');

if (!is_logged_in() || $_SESSION['role'] != 'user') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized access"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $list_id = isset($_POST['list_id']) ? (int) $_POST['list_id'] : 0;

    if ($list_id <= 0) {
        echo json_encode(["error" => "Invalid list ID"]);
        exit();
    }

    if (deleteShoppingList($conn, $list_id, $user_id)) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["error" => "Failed to delete list"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}
?>