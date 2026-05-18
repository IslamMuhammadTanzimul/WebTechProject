<?php
// user/chef_verification_request.php
$page_title = "Apply to Become a Chef - Recipe Sharing Platform";
$base_url = "../";
include "../includes/header.php";
require_once "../config/db_connect.php";
require_role("user", $base_url);

$user_id = $_SESSION['user_id'];

// Check if already has a pending/approved request
$stmt = $conn->prepare("SELECT status FROM chef_verification_requests WHERE user_id=? ORDER BY submitted_at DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$existing = $stmt->get_result()->fetch_assoc();
$stmt->close();

$success = $_SESSION['success'] ?? null; unset($_SESSION['success']);
$error   = $_SESSION['error'] ?? null;   unset($_SESSION['error']);
?>

<?php if ($success): ?><div class="card" style="background:#d4edda;color:#155724;"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<?php if ($error):   ?><div class="card" style="background:#f8d7da;color:#721c24;"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<div class="card">
    <h2>👨‍🍳 Apply for Verified Chef Status</h2>
    <p>Verified Chefs get access to professional features: create advanced recipes, manage collections, and track analytics. Your application will be reviewed by a moderator.</p>

    <?php if ($existing && $existing['status'] === 'pending'): ?>
        <div style="background:#fff3cd;color:#856404;padding:1rem;border-radius:6px;margin-top:1rem;">
            ⏳ Your application is currently <strong>pending review</strong>. Please wait for a moderator to process it.
        </div>

    <?php elseif ($existing && $existing['status'] === 'approved'): ?>
        <div style="background:#d4edda;color:#155724;padding:1rem;border-radius:6px;margin-top:1rem;">
            ✅ Your application was <strong>approved</strong>! If you still see the user dashboard, please log out and log back in.
        </div>

    <?php elseif ($existing && $existing['status'] === 'rejected'): ?>
        <div style="background:#f8d7da;color:#721c24;padding:1rem;border-radius:6px;margin-top:1rem;">
            ❌ Your previous application was <strong>rejected</strong>. You can submit a new application below.
        </div>
        <?php $show_form = true; ?>

    <?php else: ?>
        <?php $show_form = true; ?>
    <?php endif; ?>

    <?php if (!empty($show_form)): ?>
    <form action="../controllers/chef/submit_verification_action.php" method="POST" style="margin-top:1.5rem;">
        <div style="margin-bottom:1rem;">
            <label><strong>Motivation *</strong> — Why do you want to become a Verified Chef?</label><br>
            <textarea name="motivation" rows="4" required style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;margin-top:0.3rem;" placeholder="Tell us about your passion for cooking and why you'd like to be a verified chef..."></textarea>
        </div>
        <div style="margin-bottom:1rem;">
            <label><strong>Credentials Description *</strong> — List your qualifications, training, or experience.</label><br>
            <textarea name="credentials_description" rows="4" required style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;margin-top:0.3rem;" placeholder="e.g. Culinary school graduate, 5 years professional experience, won local cooking competition..."></textarea>
        </div>
        <div style="margin-bottom:1rem;">
            <label><strong>Portfolio Link</strong> (optional) — Link to your food blog, Instagram, YouTube, etc.</label><br>
            <input type="url" name="portfolio_link" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;margin-top:0.3rem;" placeholder="https://...">
        </div>
        <button type="submit" class="btn-small">Submit Application</button>
    </form>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>
