<?php
// controllers/chef/reply_review_action.php
session_start();
require_once "../../config/db_connect.php";
require_once "../../includes/auth.php";
require_role("chef", "../../");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../chef/reviews.php");
    exit();
}

$chef_id   = $_SESSION['user_id'];
$review_id = (int)($_POST['review_id'] ?? 0);
$reply     = trim($_POST['chef_reply'] ?? '');

if (empty($reply)) {
    $_SESSION['error'] = "Reply cannot be empty.";
    header("Location: ../../chef/reviews.php");
    exit();
}

// Verify the review belongs to one of this chef's recipes and has no existing reply
$stmt = $conn->prepare("SELECT rv.id FROM reviews rv JOIN recipes r ON r.id=rv.recipe_id WHERE rv.id=? AND r.author_id=? AND rv.chef_reply IS NULL");
$stmt->bind_param("ii", $review_id, $chef_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    $_SESSION['error'] = "Review not found or already replied.";
    header("Location: ../../chef/reviews.php");
    exit();
}

$upd = $conn->prepare("UPDATE reviews SET chef_reply=? WHERE id=?");
$upd->bind_param("si", $reply, $review_id);
$upd->execute();
$upd->close();

$_SESSION['success'] = "Reply posted successfully.";
header("Location: ../../chef/reviews.php");
exit();
