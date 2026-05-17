<?php
// chef/profile.php
$page_title = "Manage Chef Profile - Recipe Sharing Platform";
$base_url = "../";
include "../includes/header.php";
include "../includes/chef_nav.php";
require_once "../config/db_connect.php";
require_role("chef", $base_url);

$chef_id = $_SESSION['user_id'];

// Fetch user + chef profile
$sql = "SELECT u.name, u.username, u.email, u.bio, u.profile_pic,
               cp.display_name, cp.specialization, cp.credentials, cp.years_experience, cp.website, cp.social_links
        FROM users u
        LEFT JOIN chef_profiles cp ON cp.user_id = u.id
        WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $chef_id);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
$stmt->close();

$social_links = $profile['social_links'] ? json_decode($profile['social_links'], true) : [];
$success = $_SESSION['success'] ?? null; unset($_SESSION['success']);
$error   = $_SESSION['error'] ?? null;   unset($_SESSION['error']);
?>

<?php if ($success): ?><div class="card" style="background:#d4edda;color:#155724;"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<?php if ($error):   ?><div class="card" style="background:#f8d7da;color:#721c24;"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<div class="card">
    <h2>Chef Profile</h2>
    <form action="../controllers/chef/update_profile_action.php" method="POST" enctype="multipart/form-data">

        <h3 style="margin-top:1.5rem;">Basic Info</h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div>
                <label>Display Name *</label><br>
                <input type="text" name="display_name" required style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;" value="<?php echo htmlspecialchars($profile['display_name'] ?? $profile['name']); ?>">
            </div>
            <div>
                <label>Specialization (e.g. Pastry Chef, BBQ Expert)</label><br>
                <input type="text" name="specialization" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;" value="<?php echo htmlspecialchars($profile['specialization'] ?? ''); ?>">
            </div>
            <div>
                <label>Years of Experience</label><br>
                <input type="number" name="years_experience" min="0" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;" value="<?php echo htmlspecialchars($profile['years_experience'] ?? 0); ?>">
            </div>
            <div>
                <label>Website</label><br>
                <input type="url" name="website" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;" value="<?php echo htmlspecialchars($profile['website'] ?? ''); ?>">
            </div>
        </div>

        <div style="margin-top:1rem;">
            <label>Bio</label><br>
            <textarea name="bio" rows="4" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;"><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></textarea>
        </div>

        <div style="margin-top:1rem;">
            <label>Credentials / Qualifications</label><br>
            <textarea name="credentials" rows="3" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;"><?php echo htmlspecialchars($profile['credentials'] ?? ''); ?></textarea>
        </div>

        <h3 style="margin-top:1.5rem;">Social Links</h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div>
                <label>Instagram URL</label><br>
                <input type="url" name="instagram" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;" value="<?php echo htmlspecialchars($social_links['instagram'] ?? ''); ?>">
            </div>
            <div>
                <label>YouTube URL</label><br>
                <input type="url" name="youtube" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;" value="<?php echo htmlspecialchars($social_links['youtube'] ?? ''); ?>">
            </div>
            <div>
                <label>Facebook URL</label><br>
                <input type="url" name="facebook" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;" value="<?php echo htmlspecialchars($social_links['facebook'] ?? ''); ?>">
            </div>
            <div>
                <label>TikTok URL</label><br>
                <input type="url" name="tiktok" style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;" value="<?php echo htmlspecialchars($social_links['tiktok'] ?? ''); ?>">
            </div>
        </div>

        <div style="margin-top:1.5rem;">
            <button type="submit" class="btn-small">Save Profile</button>
        </div>
    </form>
</div>

<?php include "../includes/footer.php"; ?>
