<?php
// api/user/toggle_follow.php
session_start();

require_once "../../config/db_connect.php";
require_once "../../includes/auth.php";
require_once "../../models/FollowModel.php";

// Set header for JSON response
header('Content-Type: application/json');

// 1. Security Check
if (!is_logged_in() || $_SESSION['role'] != 'user') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized access"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $follower_id = $_SESSION['user_id'];
    $chef_id = isset($_POST['chef_id']) ? (int) $_POST['chef_id'] : 0;

    // Basic validation (prevent following oneself or an invalid ID)
    if ($chef_id <= 0 || $follower_id == $chef_id) {
        echo json_encode(["error" => "Invalid chef ID."]);
        exit();
    }

    // 2. Check current status and toggle
    if (isFollowing($conn, $follower_id, $chef_id)) {
        // They are currently following, so UNFOLLOW
        if (unfollowChef($conn, $follower_id, $chef_id)) {
            echo json_encode(["status" => "unfollowed", "message" => "You are no longer following this chef."]);
        } else {
            echo json_encode(["error" => "Database error: Could not unfollow."]);
        }
    } else {
        // They are not following, so FOLLOW
        if (followChef($conn, $follower_id, $chef_id)) {
            echo json_encode(["status" => "followed", "message" => "You are now following this chef!"]);
        } else {
            echo json_encode(["error" => "Database error: Could not follow."]);
        }
    }

} else {
    echo json_encode(["error" => "Invalid request method."]);
}
?>