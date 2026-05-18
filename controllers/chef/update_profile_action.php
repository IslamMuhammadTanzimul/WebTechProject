<?php
// controllers/chef/update_profile_action.php
session_start();
require_once "../../config/db_connect.php";
require_once "../../includes/auth.php";
require_role("chef", "../../");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../chef/profile.php");
    exit();
}

$chef_id = $_SESSION['user_id'];

$display_name = trim($_POST['display_name'] ?? '');
$specialization = trim($_POST['specialization'] ?? '');
$years_experience = (int)($_POST['years_experience'] ?? 0);
$website = trim($_POST['website'] ?? '');
$bio = trim($_POST['bio'] ?? '');
$credentials = trim($_POST['credentials'] ?? '');

if (empty($display_name)) {
    $_SESSION['error'] = "Display name is required.";
    header("Location: ../../chef/profile.php");
    exit();
}

$social_links = [];
foreach (['instagram','youtube','facebook','tiktok'] as $platform) {
    $val = trim($_POST[$platform] ?? '');
    if (!empty($val)) $social_links[$platform] = $val;
}
$social_json = json_encode($social_links);

// Update users.bio
$stmt = $conn->prepare("UPDATE users SET bio=? WHERE id=?");
$stmt->bind_param("si", $bio, $chef_id);
$stmt->execute();
$stmt->close();

// Upsert chef_profiles
$check = $conn->prepare("SELECT id FROM chef_profiles WHERE user_id=?");
$check->bind_param("i", $chef_id);
$check->execute();
$exists = $check->get_result()->fetch_assoc();
$check->close();

if ($exists) {
    $stmt = $conn->prepare("UPDATE chef_profiles SET display_name=?, specialization=?, credentials=?, years_experience=?, website=?, social_links=? WHERE user_id=?");
    $stmt->bind_param("sssissi", $display_name, $specialization, $credentials, $years_experience, $website, $social_json, $chef_id);
} else {
    $stmt = $conn->prepare("INSERT INTO chef_profiles (user_id, display_name, specialization, credentials, years_experience, website, social_links) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param("isssis s", $chef_id, $display_name, $specialization, $credentials, $years_experience, $website, $social_json);
}
$stmt->execute();
$stmt->close();

$_SESSION['success'] = "Profile updated successfully.";
header("Location: ../../chef/profile.php");
exit();
