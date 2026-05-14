<?php
// controllers/submit_review_action.php
session_start();

require_once "../config/db_connect.php";
require_once "../includes/auth.php";
require_once "../models/ReviewModel.php";

// 1. Security Check: Only logged-in users can post reviews
if (!is_logged_in() || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

// 2. Only process POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION['user_id'];
    $recipe_id = isset($_POST['recipe_id']) ? (int) $_POST['recipe_id'] : 0;
    $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
    $review_text = trim($_POST['review_text']);

    // Determine where to send the user back to
    $redirect_url = "../user/recipe_detail.php?id=" . $recipe_id;

    // 3. Server-Side Form Validation (Required by Rubric)
    if ($recipe_id <= 0) {
        $_SESSION['error'] = "Invalid recipe.";
        header("Location: ../user/recipes.php");
        exit();
    }

    if ($rating < 1 || $rating > 5) {
        $_SESSION['error'] = "Please select a valid star rating (1 to 5).";
        header("Location: " . $redirect_url);
        exit();
    }

    if (empty($review_text)) {
        $_SESSION['error'] = "Review text cannot be empty.";
        header("Location: " . $redirect_url);
        exit();
    }

    // 4. Duplicate Check (Enforcing our Database Constraint)
    if (hasUserReviewed($conn, $recipe_id, $user_id)) {
        $_SESSION['error'] = "You have already submitted a review for this recipe.";
        header("Location: " . $redirect_url);
        exit();
    }

    // 5. Insert into Database
    if (addReview($conn, $recipe_id, $user_id, $rating, $review_text)) {
        $_SESSION['success'] = "Thank you! Your review has been posted successfully.";
    } else {
        $_SESSION['error'] = "A database error occurred while posting your review.";
    }

    // Redirect back to the recipe page
    header("Location: " . $redirect_url);
    exit();

} else {
    // If someone tries to access this file directly via URL, kick them out
    header("Location: ../user/recipes.php");
    exit();
}
?>