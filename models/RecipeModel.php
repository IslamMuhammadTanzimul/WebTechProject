<?php
// models/RecipeModel.php

/**
 * Fetch a list of published recipes with their author and cuisine names.
 */
function getPublishedRecipes($conn, $limit = 20)
{
    $sql = "SELECT r.id, r.title, r.difficulty, r.prep_time_mins, r.cook_time_mins, r.is_chef_pick, r.view_count,
                   u.name as author_name, u.chef_verified, c.name as cuisine_name, c.flag_emoji
            FROM recipes r
            JOIN users u ON r.author_id = u.id
            LEFT JOIN cuisines c ON r.cuisine_id = c.id
            WHERE r.status = 'published'
            ORDER BY r.created_at DESC
            LIMIT ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return false;

    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $recipes = [];
    while ($row = $result->fetch_assoc()) {
        $recipes[] = $row;
    }

    $stmt->close();
    return $recipes;
}

/**
 * Fetch all available cuisines for the filter dropdowns.
 */
function getCuisines($conn)
{
    $sql = "SELECT id, name, flag_emoji FROM cuisines ORDER BY name ASC";
    $result = $conn->query($sql);

    $cuisines = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $cuisines[] = $row;
        }
    }
    return $cuisines;
}

/**
 * Fetch all available diet types for the filter dropdowns.
 */
function getDietTypes($conn)
{
    $sql = "SELECT id, name FROM diet_types ORDER BY name ASC";
    $result = $conn->query($sql);

    $diet_types = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $diet_types[] = $row;
        }
    }
    return $diet_types;
}

/**
 * Fetch a single recipe by its ID with all author and category details.
 */
function getRecipeById($conn, $recipe_id)
{
    $sql = "SELECT r.*, u.name as author_name, u.chef_verified, u.id as chef_id, c.name as cuisine_name, c.flag_emoji, d.name as diet_name
            FROM recipes r
            JOIN users u ON r.author_id = u.id
            LEFT JOIN cuisines c ON r.cuisine_id = c.id
            LEFT JOIN diet_types d ON r.diet_type_id = d.id
            WHERE r.id = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return false;

    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $recipe = $result->fetch_assoc();
    $stmt->close();

    return $recipe;
}

/**
 * Fetch all ingredients for a specific recipe, ordered correctly.
 */
function getRecipeIngredients($conn, $recipe_id)
{
    $sql = "SELECT * FROM ingredients WHERE recipe_id = ? ORDER BY order_index ASC";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return [];

    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $ingredients = [];
    while ($row = $result->fetch_assoc()) {
        $ingredients[] = $row;
    }
    $stmt->close();
    return $ingredients;
}

/**
 * Fetch all preparation steps for a specific recipe, ordered correctly.
 */
function getRecipeSteps($conn, $recipe_id)
{
    $sql = "SELECT * FROM steps WHERE recipe_id = ? ORDER BY step_order ASC";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return [];

    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $steps = [];
    while ($row = $result->fetch_assoc()) {
        $steps[] = $row;
    }
    $stmt->close();
    return $steps;
}

/**
 * Fetch nutrition information for a specific recipe.
 */
function getRecipeNutrition($conn, $recipe_id)
{
    $sql = "SELECT * FROM nutrition_info WHERE recipe_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return false;

    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $nutrition = $result->fetch_assoc();
    $stmt->close();
    return $nutrition;
}

// ============================================================
// ADMIN — Recipe Management
// ============================================================

function getAllRecipes($conn, $search = '', $cuisine_id = '', $status = '')
{
    $sql = "SELECT r.*, u.username, c.name as cuisine_name
            FROM recipes r
            JOIN users u ON r.author_id = u.id
            LEFT JOIN cuisines c ON r.cuisine_id = c.id
            WHERE 1=1";
    $params = [];
    $types = "";

    if (!empty($search)) {
        $sql .= " AND (r.title LIKE ? OR u.username LIKE ?)";
        $like = "%$search%";
        $params = [$like, $like];
        $types = "ss";
    }
    if (!empty($cuisine_id)) {
        $sql .= " AND r.cuisine_id = ?";
        $params[] = (int)$cuisine_id;
        $types .= "i";
    }
    if (!empty($status)) {
        $sql .= " AND r.status = ?";
        $params[] = $status;
        $types .= "s";
    }
    $sql .= " ORDER BY r.created_at DESC";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result();
}

function getRecipeById_admin($conn, $id)
{
    $stmt = $conn->prepare("SELECT r.*, u.username FROM recipes r JOIN users u ON r.author_id = u.id WHERE r.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function permanentlyDeleteRecipe($conn, $id)
{
    $stmt = $conn->prepare("DELETE FROM recipes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

function getRecipeCountByCuisine($conn)
{
    return $conn->query(
        "SELECT c.name, COUNT(r.id) as count
         FROM cuisines c LEFT JOIN recipes r ON c.id = r.cuisine_id AND r.status = 'published'
         GROUP BY c.id, c.name ORDER BY count DESC"
    );
}

function getAvgRatingByCuisine($conn)
{
    return $conn->query(
        "SELECT c.name, COALESCE(AVG(v.rating), 0) as avg_rating
         FROM cuisines c LEFT JOIN recipes r ON c.id = r.cuisine_id
         LEFT JOIN reviews v ON r.id = v.recipe_id
         WHERE r.status = 'published' GROUP BY c.id, c.name ORDER BY avg_rating DESC"
    );
}

function getPublishedPerDay($conn)
{
    return $conn->query(
        "SELECT DATE(created_at) as date, COUNT(*) as count
         FROM recipes WHERE status = 'published' GROUP BY DATE(created_at) ORDER BY date DESC LIMIT 30"
    );
}
