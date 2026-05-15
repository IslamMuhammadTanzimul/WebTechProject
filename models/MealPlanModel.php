<?php
// models/MealPlanModel.php

/**
 * Fetch a user's meal plan for a specific week.
 * If it doesn't exist, this returns false.
 */
function getMealPlan($conn, $user_id, $week_start_date)
{
    $sql = "SELECT id, week_start_date FROM meal_plans WHERE user_id = ? AND week_start_date = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return false;

    $stmt->bind_param("is", $user_id, $week_start_date);
    $stmt->execute();
    $result = $stmt->get_result();

    $plan = $result->fetch_assoc();
    $stmt->close();

    return $plan;
}

/**
 * Create a new meal plan for a specific week.
 * Returns the ID of the new plan.
 */
function createMealPlan($conn, $user_id, $week_start_date)
{
    $sql = "INSERT IGNORE INTO meal_plans (user_id, week_start_date) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return false;

    $stmt->bind_param("is", $user_id, $week_start_date);
    $stmt->execute();

    // If it was just created, get the new ID. 
    // If it was ignored (already exists), we fetch the existing ID.
    $plan_id = $stmt->insert_id;
    $stmt->close();

    if ($plan_id == 0) {
        $existing = getMealPlan($conn, $user_id, $week_start_date);
        return $existing ? $existing['id'] : false;
    }

    return $plan_id;
}

/**
 * Fetch all the scheduled recipes for a specific meal plan.
 * Joins with the recipes table to get the title and time required.
 */
function getMealPlanEntries($conn, $meal_plan_id)
{
    $sql = "SELECT mpe.*, r.title, r.prep_time_mins, r.cook_time_mins, r.difficulty 
            FROM meal_plan_entries mpe
            JOIN recipes r ON mpe.recipe_id = r.id
            WHERE mpe.meal_plan_id = ?
            ORDER BY mpe.day_of_week ASC, mpe.meal_type ASC";

    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return [];

    $stmt->bind_param("i", $meal_plan_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $entries = [];
    while ($row = $result->fetch_assoc()) {
        $entries[] = $row;
    }

    $stmt->close();
    return $entries;
}

/**
 * Assign a recipe to a specific day and meal slot.
 * If a recipe is already assigned to that exact slot, this query overwrites it securely.
 */
function setMealEntry($conn, $meal_plan_id, $recipe_id, $day_of_week, $meal_type)
{
    $sql = "INSERT INTO meal_plan_entries (meal_plan_id, recipe_id, day_of_week, meal_type) 
            VALUES (?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE recipe_id = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return false;

    // "iissi" = Integer, Integer, String, String, Integer
    $stmt->bind_param("iissi", $meal_plan_id, $recipe_id, $day_of_week, $meal_type, $recipe_id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

/**
 * Remove a recipe from a specific slot.
 */
function removeMealEntry($conn, $meal_plan_id, $day_of_week, $meal_type)
{
    $sql = "DELETE FROM meal_plan_entries WHERE meal_plan_id = ? AND day_of_week = ? AND meal_type = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return false;

    $stmt->bind_param("iss", $meal_plan_id, $day_of_week, $meal_type);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}
?>