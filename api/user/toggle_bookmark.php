<?php
// api/user/toggle_bookmark.php
session_start();

require_once "../../config/db_connect.php";
require_once "../../includes/auth.php";
require_once "../../models/BookmarkModel.php";

// Set header for JSON response
header('Content-Type: application/json');

// Security check
if (!is_logged_in() || $_SESSION['role'] != 'user') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $recipe_id = isset($_POST['recipe_id']) ? (int) $_POST['recipe_id'] : 0;

    if ($recipe_id <= 0) {
        echo json_encode(["error" => "Invalid recipe ID"]);
        exit();
    }

    // Check current state and toggle
    if (isBookmarked($conn, $user_id, $recipe_id)) {
        removeBookmark($conn, $user_id, $recipe_id);
        echo json_encode(["status" => "removed", "message" => "Recipe removed from saved list."]);
    } else {
        addBookmark($conn, $user_id, $recipe_id);
        echo json_encode(["status" => "added", "message" => "Recipe saved successfully!"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}
?>