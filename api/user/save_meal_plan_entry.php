<?php
// api/user/save_meal_plan_entry.php
session_start();

require_once "../../config/db_connect.php";
require_once "../../includes/auth.php";
require_once "../../models/MealPlanModel.php";

// Set header for JSON response
header('Content-Type: application/json');

// 1. Security Check
if (!is_logged_in() || $_SESSION['role'] != 'user') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized access"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION['user_id'];

    // Capture and sanitize input data
    $week_start_date = isset($_POST['week_start_date']) ? trim($_POST['week_start_date']) : '';
    $day_of_week = isset($_POST['day_of_week']) ? trim($_POST['day_of_week']) : '';
    $meal_type = isset($_POST['meal_type']) ? trim($_POST['meal_type']) : '';
    $recipe_id = isset($_POST['recipe_id']) ? (int) $_POST['recipe_id'] : 0;

    // Validate required fields
    if (empty($week_start_date) || empty($day_of_week) || empty($meal_type)) {
        echo json_encode(["error" => "Missing required scheduling details."]);
        exit();
    }

    // 2. Fetch or Create the Parent Meal Plan for this specific week
    $meal_plan_id = createMealPlan($conn, $user_id, $week_start_date);

    if (!$meal_plan_id) {
        echo json_encode(["error" => "Database error: Could not initialize meal plan."]);
        exit();
    }

    // 3. Process the Entry (Add/Update vs. Remove)
    if ($recipe_id > 0) {
        // We have a recipe ID, so we are assigning it to the slot
        if (setMealEntry($conn, $meal_plan_id, $recipe_id, $day_of_week, $meal_type)) {
            echo json_encode([
                "status" => "success",
                "message" => "Recipe scheduled for " . ucfirst($day_of_week) . " " . ucfirst($meal_type) . "!"
            ]);
        } else {
            echo json_encode(["error" => "Failed to schedule recipe."]);
        }
    } else {
        // Recipe ID is 0, which means the user is clearing the slot
        if (removeMealEntry($conn, $meal_plan_id, $day_of_week, $meal_type)) {
            echo json_encode([
                "status" => "success",
                "message" => ucfirst($meal_type) . " slot cleared."
            ]);
        } else {
            echo json_encode(["error" => "Failed to clear the meal slot."]);
        }
    }

} else {
    echo json_encode(["error" => "Invalid request method."]);
}
?>