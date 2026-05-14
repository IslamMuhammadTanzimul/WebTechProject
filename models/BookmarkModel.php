<?php
// models/BookmarkModel.php

function isBookmarked($conn, $user_id, $recipe_id)
{
    $sql = "SELECT id FROM bookmarks WHERE user_id = ? AND recipe_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return false;

    $stmt->bind_param("ii", $user_id, $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $is_saved = $result->num_rows > 0;
    $stmt->close();

    return $is_saved;
}

function addBookmark($conn, $user_id, $recipe_id)
{
    $sql = "INSERT IGNORE INTO bookmarks (user_id, recipe_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return false;

    $stmt->bind_param("ii", $user_id, $recipe_id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

function removeBookmark($conn, $user_id, $recipe_id)
{
    $sql = "DELETE FROM bookmarks WHERE user_id = ? AND recipe_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return false;

    $stmt->bind_param("ii", $user_id, $recipe_id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

/**
 * Fetch all published recipes that a specific user has bookmarked.
 */
function getBookmarkedRecipes($conn, $user_id)
{
    $sql = "SELECT r.id, r.title, r.difficulty, r.prep_time_mins, r.cook_time_mins, r.is_chef_pick,
                   u.name as author_name, u.chef_verified, c.name as cuisine_name, c.flag_emoji
            FROM bookmarks b
            JOIN recipes r ON b.recipe_id = r.id
            JOIN users u ON r.author_id = u.id
            LEFT JOIN cuisines c ON r.cuisine_id = c.id
            WHERE b.user_id = ? AND r.status = 'published'
            ORDER BY r.title ASC";

    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return [];

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $recipes = [];
    while ($row = $result->fetch_assoc()) {
        $recipes[] = $row;
    }

    $stmt->close();
    return $recipes;
}
?>