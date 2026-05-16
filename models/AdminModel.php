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
