<?php
// models/StatsModel.php — Dashboard + monthly aggregate stats

function getDashboardStats($conn)
{
    $stats = [];

    $result = $conn->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
    $stats['users_by_role'] = ['user' => 0, 'chef' => 0, 'moderator' => 0, 'admin' => 0];
    while ($row = $result->fetch_assoc()) {
        $stats['users_by_role'][$row['role']] = (int)$row['count'];
    }
    $stats['total_users'] = array_sum($stats['users_by_role']);

    $r = $conn->query("SELECT COUNT(*) as c FROM recipes WHERE status='published'");
    $stats['published_recipes'] = $r->fetch_assoc()['c'];

    $r = $conn->query("SELECT COUNT(DISTINCT author_id) as c FROM recipes WHERE status='published'");
    $stats['active_chefs'] = $r->fetch_assoc()['c'];

    $r = $conn->query("SELECT COUNT(*) as c FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $stats['new_this_week'] = $r->fetch_assoc()['c'];

    $r = $conn->query("SELECT COUNT(*) as c FROM reviews");
    $stats['total_reviews'] = $r->fetch_assoc()['c'];

    $r = $conn->query("SELECT COUNT(*) as c FROM chef_verification_requests WHERE status='pending'");
    $stats['pending_verifications'] = $r->fetch_assoc()['c'];

    return $stats;
}

function getMonthlyStats($conn, $month, $year)
{
    $stats = [];
    $r = $conn->query("SELECT COUNT(*) as c FROM users WHERE MONTH(created_at) = $month AND YEAR(created_at) = $year");
    $stats['new_users'] = $r->fetch_assoc()['c'];

    $r = $conn->query("SELECT COUNT(*) as c FROM recipes WHERE MONTH(created_at) = $month AND YEAR(created_at) = $year");
    $stats['new_recipes'] = $r->fetch_assoc()['c'];

    $r = $conn->query("SELECT COUNT(*) as c FROM reviews WHERE MONTH(created_at) = $month AND YEAR(created_at) = $year");
    $stats['new_reviews'] = $r->fetch_assoc()['c'];

    $r = $conn->query("SELECT COUNT(*) as c FROM content_reports WHERE MONTH(created_at) = $month AND YEAR(created_at) = $year");
    $stats['new_reports'] = $r->fetch_assoc()['c'];

    return $stats;
}
?>
