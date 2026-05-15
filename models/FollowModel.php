<?php
// models/FollowModel.php

/**
 * Check if a user is currently following a specific chef.
 */
function isFollowing($conn, $follower_id, $chef_id)
{
    $sql = "SELECT id FROM follows WHERE follower_id = ? AND chef_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return false;

    $stmt->bind_param("ii", $follower_id, $chef_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $is_following = $result->num_rows > 0;
    $stmt->close();

    return $is_following;
}

/**
 * Add a new follow relationship.
 */
function followChef($conn, $follower_id, $chef_id)
{
    // We use INSERT IGNORE to prevent duplicate errors if they click twice quickly
    $sql = "INSERT IGNORE INTO follows (follower_id, chef_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return false;

    $stmt->bind_param("ii", $follower_id, $chef_id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

/**
 * Remove a follow relationship.
 */
function unfollowChef($conn, $follower_id, $chef_id)
{
    $sql = "DELETE FROM follows WHERE follower_id = ? AND chef_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return false;

    $stmt->bind_param("ii", $follower_id, $chef_id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

/**
 * Fetch a list of all chefs that the current user is following.
 * We join with chef_profiles to get their specialization!
 */
function getFollowedChefs($conn, $follower_id)
{
    $sql = "SELECT u.id, u.name, u.profile_pic, cp.specialization 
            FROM follows f
            JOIN users u ON f.chef_id = u.id
            LEFT JOIN chef_profiles cp ON u.id = cp.user_id
            WHERE f.follower_id = ? AND u.role = 'chef'
            ORDER BY u.name ASC";

    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return [];

    $stmt->bind_param("i", $follower_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $chefs = [];
    while ($row = $result->fetch_assoc()) {
        $chefs[] = $row;
    }

    $stmt->close();
    return $chefs;
}
?>