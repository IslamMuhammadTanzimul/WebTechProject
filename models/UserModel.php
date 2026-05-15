<?php

function checkUserExists($conn, $username, $email)
{
    $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return true;
    }

    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt->close();
        return true;
    }

    $stmt->close();
    return false;
}

function registerUser($conn, $name, $username, $email, $password_hash, $dietary_json)
{
    $role = "user";
    $is_active = 1;
    $chef_verified = 0;

    $sql = "INSERT INTO users 
        (name, username, email, password_hash, role, dietary_prefs, is_active, chef_verified) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "ssssssii",
        $name,
        $username,
        $email,
        $password_hash,
        $role,
        $dietary_json,
        $is_active,
        $chef_verified
    );

    if ($stmt->execute()) {
        $new_user_id = $stmt->insert_id;
        $stmt->close();
        return $new_user_id;
    }

    $stmt->close();
    return false;
}

// ============================================================
// ADMIN — User Management
// ============================================================

function getUserById($conn, $id)
{
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getUsers($conn, $search = '', $role_filter = '', $status_filter = '')
{
    $sql = "SELECT * FROM users WHERE 1=1";
    $params = [];
    $types = "";

    if (!empty($search)) {
        $sql .= " AND (name LIKE ? OR username LIKE ? OR email LIKE ?)";
        $like = "%$search%";
        $params = [$like, $like, $like];
        $types = "sss";
    }
    if (!empty($role_filter)) {
        $sql .= " AND role = ?";
        $params[] = $role_filter;
        $types .= "s";
    }
    if ($status_filter !== '') {
        $sql .= " AND is_active = ?";
        $params[] = (int)$status_filter;
        $types .= "i";
    }
    $sql .= " ORDER BY created_at DESC";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result();
}

function toggleUserActive($conn, $id)
{
    $user = getUserById($conn, $id);
    $new = $user['is_active'] ? 0 : 1;
    $stmt = $conn->prepare("UPDATE users SET is_active = ? WHERE id = ?");
    $stmt->bind_param("ii", $new, $id);
    $stmt->execute();
    return $new;
}

function updateUserRole($conn, $id, $role)
{
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $role, $id);
    $stmt->execute();
}

function updateChefVerified($conn, $id, $status)
{
    $stmt = $conn->prepare("UPDATE users SET chef_verified = ? WHERE id = ?");
    $stmt->bind_param("ii", $status, $id);
    $stmt->execute();
}

function getMostActiveUsers($conn, $limit = 10)
{
    return $conn->query(
        "SELECT u.id, u.username, u.name, u.role,
                (SELECT COUNT(*) FROM recipes WHERE author_id = u.id) as recipe_count,
                (SELECT COUNT(*) FROM reviews WHERE user_id = u.id) as review_count,
                (SELECT COUNT(*) FROM bookmarks WHERE user_id = u.id) as bookmark_count
         FROM users u
         ORDER BY (recipe_count + review_count + bookmark_count) DESC
         LIMIT $limit"
    );
}

function getMostFollowedChefs($conn, $limit = 10)
{
    return $conn->query(
        "SELECT u.id, u.username, u.name, COUNT(f.id) as follower_count
         FROM users u
         JOIN follows f ON u.id = f.chef_id
         WHERE u.role = 'chef'
         GROUP BY u.id
         ORDER BY follower_count DESC
         LIMIT $limit"
    );
}

function getRegistrationsByMonth($conn, $year = null)
{
    $year = $year ?: date('Y');
    $stmt = $conn->prepare("SELECT MONTH(created_at) as month, COUNT(*) as count FROM users WHERE YEAR(created_at) = ? GROUP BY MONTH(created_at)");
    $stmt->bind_param("i", $year);
    $stmt->execute();
    return $stmt->get_result();
}

function getNewUsersByRole($conn)
{
    return $conn->query("SELECT role, COUNT(*) as count FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY role");
}

function getChefConversionRate($conn)
{
    $total = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
    $chefs = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='chef'")->fetch_assoc()['c'];
    return $total > 0 ? round(($chefs / $total) * 100, 1) : 0;
}

function getMonthlyActiveUsers($conn)
{
    $result = $conn->query(
        "SELECT COUNT(DISTINCT user_id) as count FROM (
            SELECT author_id AS user_id FROM recipes WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            UNION SELECT user_id FROM reviews WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            UNION SELECT user_id FROM bookmarks WHERE saved_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ) as active"
    );
    return $result->fetch_assoc()['count'];
}

function getActiveModerators($conn)
{
    return $conn->query(
        "SELECT u.*, (SELECT COUNT(*) FROM moderation_logs WHERE moderator_id = u.id) as actions_count
         FROM users u WHERE u.role = 'moderator' AND u.is_active = 1
         ORDER BY u.created_at DESC"
    );
}
?>