<?php
function get_dashboard_stats($conn)
{
    $stats = [];

    $stats['total_users']    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"))['count'];
    $stats['total_recipes']  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM recipes"))['count'];
    $stats['active_chefs']   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'chef' AND is_active = 1"))['count'];
    $stats['new_this_week']  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"))['count'];
    $stats['total_reviews']  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM reviews"))['count'];
    $stats['pending_verifs'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM chef_verification_requests WHERE status = 'pending'"))['count'];


    $role_result = mysqli_query($conn, "SELECT role, COUNT(*) as count FROM users GROUP BY role");
    $stats['users_by_role'] = [];
    while ($row = mysqli_fetch_assoc($role_result)) {
        $stats['users_by_role'][$row['role']] = $row['count'];
    }

    return $stats;
}
function get_all_users($conn, $search = "")
{
    if ($search) {
        $sql  = "SELECT id, name, username, email, role, is_active, created_at FROM users WHERE name LIKE ? OR email LIKE ? OR role LIKE ? ORDER BY created_at DESC";
        $stmt = mysqli_prepare($conn, $sql);
        $like = "%" . $search . "%";
        mysqli_stmt_bind_param($stmt, "sss", $like, $like, $like);
    } else {
        $sql  = "SELECT id, name, username, email, role, is_active, created_at FROM users ORDER BY created_at DESC";
        $stmt = mysqli_prepare($conn, $sql);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function get_user_by_id($conn, $id)
{
    $sql  = "SELECT id, name, username, email, role, is_active, created_at FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function update_user_status($conn, $id, $is_active)
{
    $sql  = "UPDATE users SET is_active = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $is_active, $id);
    return mysqli_stmt_execute($stmt);
}

function update_user_role($conn, $id, $role)
{
    $sql  = "UPDATE users SET role = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $role, $id);
    return mysqli_stmt_execute($stmt);
}
function get_verification_requests($conn, $status = "")
{
    if ($status) {
        $sql  = "SELECT cvr.*, u.name, u.username, u.email, u.role as user_role FROM chef_verification_requests cvr JOIN users u ON cvr.user_id = u.id WHERE cvr.status = ? ORDER BY cvr.submitted_at DESC";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $status);
    } else {
        $sql  = "SELECT cvr.*, u.name, u.username, u.email, u.role as user_role FROM chef_verification_requests cvr JOIN users u ON cvr.user_id = u.id ORDER BY cvr.submitted_at DESC";
        $stmt = mysqli_prepare($conn, $sql);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function approve_chef($conn, $user_id, $request_id, $admin_id)
{
    // update user role to chef
    $sql  = "UPDATE users SET role = 'chef', chef_verified = 1 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    // update request status
    $sql  = "UPDATE chef_verification_requests SET status = 'approved', reviewed_by = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $admin_id, $request_id);
    return mysqli_stmt_execute($stmt);
}

function reject_chef_request($conn, $request_id, $admin_id)
{
    $sql  = "UPDATE chef_verification_requests SET status = 'rejected', reviewed_by = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $admin_id, $request_id);
    return mysqli_stmt_execute($stmt);
}

function revoke_chef($conn, $user_id, $admin_id)
{
    // demote user
    $sql  = "UPDATE users SET role = 'user', chef_verified = 0 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    // update request status
    $sql  = "UPDATE chef_verification_requests SET status = 'rejected', reviewed_by = ? WHERE user_id = ? AND status = 'approved'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $admin_id, $user_id);
    return mysqli_stmt_execute($stmt);
}



function get_all_recipes($conn, $filters = [])
{
    $sql  = "SELECT r.id, r.title, r.status, r.view_count, r.created_at,
                    u.name as author_name,
                    c.name as cuisine_name,
                    d.name as diet_name
             FROM recipes r
             JOIN users u ON r.author_id = u.id
             LEFT JOIN cuisines c ON r.cuisine_id = c.id
             LEFT JOIN diet_types d ON r.diet_type_id = d.id
             WHERE 1=1";

    $params = [];
    $types  = "";

    if (!empty($filters['author'])) {
        $sql     .= " AND u.name LIKE ?";
        $params[] = "%" . $filters['author'] . "%";
        $types   .= "s";
    }

    if (!empty($filters['cuisine'])) {
        $sql     .= " AND c.name LIKE ?";
        $params[] = "%" . $filters['cuisine'] . "%";
        $types   .= "s";
    }

    if (!empty($filters['diet'])) {
        $sql     .= " AND d.name LIKE ?";
        $params[] = "%" . $filters['diet'] . "%";
        $types   .= "s";
    }

    if (!empty($filters['status'])) {
        $sql     .= " AND r.status = ?";
        $params[] = $filters['status'];
        $types   .= "s";
    }

    $sql .= " ORDER BY r.created_at DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function delete_recipe($conn, $id)
{
    $sql  = "DELETE FROM recipes WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    return mysqli_stmt_execute($stmt);
}
