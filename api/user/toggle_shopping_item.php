<?php
// api/user/toggle_shopping_item.php
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
    $item_id = isset($_POST['item_id']) ? (int) $_POST['item_id'] : 0;
    $is_checked = isset($_POST['is_checked']) ? (int) $_POST['is_checked'] : 0;

    if ($item_id <= 0) {
        echo json_encode(["error" => "Invalid item ID"]);
        exit();
    }

    if (updateItemStatus($conn, $item_id, $user_id, $is_checked)) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["error" => "Failed to update item status"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}
?>