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
?>