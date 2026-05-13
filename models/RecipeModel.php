<?php
// models/RecipeModel.php

/**
 * Fetch a list of published recipes with their author and cuisine names.
 */
function getPublishedRecipes($conn, $limit = 20)
{
    $sql = "SELECT r.id, r.title, r.difficulty, r.prep_time_mins, r.cook_time_mins, r.view_count, r.is_chef_pick,
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
?>