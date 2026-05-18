<?php
// controllers/chef/submit_verification_action.php
session_start();
require_once "../../config/db_connect.php";
require_once "../../includes/auth.php";
require_role("user", "../../");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../user/chef_verification_request.php");
    exit();
}

$user_id    = $_SESSION['user_id'];
$motivation = trim($_POST['motivation'] ?? '');
$credentials_description = trim($_POST['credentials_description'] ?? '');
$portfolio_link = trim($_POST['portfolio_link'] ?? '');

if (empty($motivation) || empty($credentials_description)) {
    $_SESSION['error'] = "Motivation and credentials description are required.";
    header("Location: ../../user/chef_verification_request.php");
    exit();
}

// Check no pending request exists
$chk = $conn->prepare("SELECT id FROM chef_verification_requests WHERE user_id=? AND status='pending'");
$chk->bind_param("i", $user_id);
$chk->execute();
if ($chk->get_result()->fetch_assoc()) {
    $_SESSION['error'] = "You already have a pending application.";
    header("Location: ../../user/chef_verification_request.php");
    exit();
}
$chk->close();

$portfolio = !empty($portfolio_link) ? $portfolio_link : null;
$stmt = $conn->prepare("INSERT INTO chef_verification_requests (user_id, motivation, credentials_description, portfolio_link) VALUES (?,?,?,?)");
$stmt->bind_param("isss", $user_id, $motivation, $credentials_description, $portfolio);
$stmt->execute();
$stmt->close();

$_SESSION['success'] = "Your application has been submitted. A moderator will review it shortly.";
header("Location: ../../user/chef_verification_request.php");
exit();
