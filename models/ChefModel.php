<?php
// models/ChefModel.php

/**
 * Get or create chef profile for a user.
 */
function getChefProfile($conn, $user_id)
{
    $sql = "SELECT u.id, u.name, u.username, u.bio, u.profile_pic, u.chef_verified,
                   cp.display_name, cp.specialization, cp.credentials, cp.years_experience, cp.website, cp.social_links
            FROM users u
            LEFT JOIN chef_profiles cp ON cp.user_id = u.id
            WHERE u.id = ? AND u.role = 'chef'";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $profile = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $profile;
}

/**
 * Get all recipes by a chef with aggregate stats.
 */
function getChefRecipes($conn, $chef_id, $include_drafts = true)
{
    $status_filter = $include_drafts ? "" : "AND r.status='published'";
    $sql = "SELECT r.id, r.title, r.status, r.is_chef_pick, r.view_count, r.created_at,
                   c.name AS cuisine_name,
                   (SELECT COUNT(*) FROM bookmarks b WHERE b.recipe_id=r.id) AS bookmark_count,
                   (SELECT COUNT(*) FROM reviews rv WHERE rv.recipe_id=r.id) AS review_count,
                   (SELECT AVG(rv2.rating) FROM reviews rv2 WHERE rv2.recipe_id=r.id) AS avg_rating
            FROM recipes r
            LEFT JOIN cuisines c ON c.id = r.cuisine_id
            WHERE r.author_id = ? $status_filter
            ORDER BY r.created_at DESC";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return [];
    $stmt->bind_param("i", $chef_id);
    $stmt->execute();
    $recipes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $recipes;
}

/**
 * Get collections for a chef.
 */
function getChefCollections($conn, $chef_id, $public_only = false)
{
    $filter = $public_only ? "AND col.is_public=1" : "";
    $sql = "SELECT col.*, COUNT(cr.recipe_id) AS recipe_count
            FROM collections col
            LEFT JOIN collection_recipes cr ON cr.collection_id = col.id
            WHERE col.chef_id = ? $filter
            GROUP BY col.id
            ORDER BY col.created_at DESC";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return [];
    $stmt->bind_param("i", $chef_id);
    $stmt->execute();
    $cols = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $cols;
}

/**
 * Get chef's pick recipes (max 3).
 */
function getChefPicks($conn, $chef_id)
{
    $sql = "SELECT r.id, r.title, r.view_count, r.featured_image_path, c.name AS cuisine_name
            FROM recipes r
            LEFT JOIN cuisines c ON c.id = r.cuisine_id
            WHERE r.author_id = ? AND r.is_chef_pick = 1 AND r.status = 'published'
            LIMIT 3";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return [];
    $stmt->bind_param("i", $chef_id);
    $stmt->execute();
    $picks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $picks;
}

/**
 * Count current chef picks for a chef.
 */
function countChefPicks($conn, $chef_id)
{
    $stmt = $conn->prepare("SELECT COUNT(*) AS c FROM recipes WHERE author_id=? AND is_chef_pick=1");
    $stmt->bind_param("i", $chef_id);
    $stmt->execute();
    $c = $stmt->get_result()->fetch_assoc()['c'];
    $stmt->close();
    return (int)$c;
}

/**
 * Get aggregate chef analytics.
 */
function getChefAnalytics($conn, $chef_id)
{
    $sql = "SELECT
                COUNT(r.id) AS total_published,
                COALESCE(SUM(r.view_count),0) AS total_views,
                (SELECT COUNT(*) FROM follows WHERE chef_id=?) AS follower_count,
                (SELECT COUNT(*) FROM bookmarks b JOIN recipes r2 ON b.recipe_id=r2.id WHERE r2.author_id=?) AS total_bookmarks
            FROM recipes r
            WHERE r.author_id=? AND r.status='published'";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;
    $stmt->bind_param("iii", $chef_id, $chef_id, $chef_id);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $stats;
}

/**
 * Get follower trend (last 6 months).
 */
function getFollowerTrend($conn, $chef_id)
{
    $sql = "SELECT DATE_FORMAT(created_at,'%Y-%m') AS month, COUNT(*) AS new_followers
            FROM follows WHERE chef_id=? AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY month ORDER BY month";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return [];
    $stmt->bind_param("i", $chef_id);
    $stmt->execute();
    $trend = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $trend;
}

/**
 * Get all reviews on a chef's recipes.
 */
function getChefRecipeReviews($conn, $chef_id)
{
    $sql = "SELECT rv.id, rv.recipe_id, rv.rating, rv.review_text, rv.chef_reply, rv.created_at,
                   u.name AS reviewer_name, r.title AS recipe_title
            FROM reviews rv
            JOIN recipes r ON r.id = rv.recipe_id
            JOIN users u ON u.id = rv.user_id
            WHERE r.author_id = ?
            ORDER BY rv.created_at DESC";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return [];
    $stmt->bind_param("i", $chef_id);
    $stmt->execute();
    $reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $reviews;
}

/**
 * Get pending chef verification requests.
 */
function getPendingVerificationRequests($conn)
{
    $sql = "SELECT cvr.*, u.name, u.username, u.email
            FROM chef_verification_requests cvr
            JOIN users u ON u.id = cvr.user_id
            WHERE cvr.status = 'pending'
            ORDER BY cvr.submitted_at ASC";
    $result = $conn->query($sql);
    if (!$result) return [];
    return $result->fetch_all(MYSQLI_ASSOC);
}
