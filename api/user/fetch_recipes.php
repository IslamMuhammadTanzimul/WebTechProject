<?php
// api/user/fetch_recipes.php
session_start();

require_once "../../config/db_connect.php";
require_once "../../includes/auth.php";

// 1. Security Check: Only logged-in users can access this API
if (!is_logged_in() || $_SESSION['role'] != 'user') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized access"]);
    exit();
}

// 2. Set Header to return JSON
header('Content-Type: application/json');

// 3. Capture GET parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$cuisine = isset($_GET['cuisine']) ? (int) $_GET['cuisine'] : 0;
$diet = isset($_GET['diet']) ? (int) $_GET['diet'] : 0;
$difficulty = isset($_GET['difficulty']) ? trim($_GET['difficulty']) : '';

// 4. Base Query (Fetching published recipes)
$sql = "SELECT r.id, r.title, r.difficulty, r.prep_time_mins, r.cook_time_mins, r.is_chef_pick,
               u.name as author_name, u.chef_verified, c.name as cuisine_name, c.flag_emoji
        FROM recipes r
        JOIN users u ON r.author_id = u.id
        LEFT JOIN cuisines c ON r.cuisine_id = c.id
        WHERE r.status = 'published'";

$params = [];
$types = "";

// 5. Dynamically Append to Query based on Filters
if (!empty($search)) {
    // Search in title or description
    $sql .= " AND (r.title LIKE ? OR r.description LIKE ?)";
    $searchParam = "%" . $search . "%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "ss"; // Correctly appending string types
}

if ($cuisine > 0) {
    $sql .= " AND r.cuisine_id = ?";
    $params[] = $cuisine;
    $types .= "i"; // Correctly appending integer type
}

if ($diet > 0) {
    $sql .= " AND r.diet_type_id = ?";
    $params[] = $diet;
    $types .= "i"; // Correctly appending integer type
}

if (!empty($difficulty)) {
    $sql .= " AND r.difficulty = ?";
    $params[] = $difficulty;
    $types .= "s"; // Correctly appending string type
}

$sql .= " ORDER BY r.created_at DESC";

// 6. Execute Query Safely
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Database query failed"]);
    exit();
}

// Bind parameters dynamically if any exist
// The ... operator unpacks the array into individual arguments
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$recipes = [];
while ($row = $result->fetch_assoc()) {
    $recipes[] = $row;
}

$stmt->close();

// 7. Return JSON response
echo json_encode($recipes);
?>