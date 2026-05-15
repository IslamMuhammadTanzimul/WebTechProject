<?php
// models/ReviewModel.php

/**
 * Add a new review and rating to a recipe.
 */
function addReview($conn, $recipe_id, $user_id, $rating, $review_text)
{
    // We do not insert chef_reply here, as that is done later by the chef
    $sql = "INSERT INTO reviews (recipe_id, user_id, rating, review_text) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return false;

    // "iiis" means Integer, Integer, Integer, String
    $stmt->bind_param("iiis", $recipe_id, $user_id, $rating, $review_text);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

/**
 * Check if a user has already reviewed a specific recipe.
 * Returns true if they have, false if they haven't.
 */
function hasUserReviewed($conn, $recipe_id, $user_id)
{
    $sql = "SELECT id FROM reviews WHERE recipe_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return false;

    $stmt->bind_param("ii", $recipe_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $has_reviewed = $result->num_rows > 0;
    $stmt->close();

    return $has_reviewed;
}

/**
 * Fetch all reviews for a specific recipe, including the reviewer's name.
 * Ordered by the newest reviews first.
 */
function getReviewsByRecipe($conn, $recipe_id)
{
    $sql = "SELECT r.*, u.name as reviewer_name 
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.recipe_id = ?
            ORDER BY r.created_at DESC";

    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return [];

    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    $stmt->close();

    return $reviews;
}
?>