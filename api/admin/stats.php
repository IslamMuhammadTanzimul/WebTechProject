<?php
require_once "../../includes/auth.php";
require_once "../../config/db_connect.php";
/** @var mysqli $conn */
require_once "../../models/AdminModel.php";

require_admin();

header('Content-Type: application/json');

$stats = get_dashboard_stats($conn);

echo json_encode([
    'total_users'    => $stats['total_users'],
    'total_recipes'  => $stats['total_recipes'],
    'active_chefs'   => $stats['active_chefs'],
    'new_this_week'  => $stats['new_this_week'],
    'total_reviews'  => $stats['total_reviews'],
    'pending_verifs' => $stats['pending_verifs']
]);
