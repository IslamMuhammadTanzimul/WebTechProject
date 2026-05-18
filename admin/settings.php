<?php
require_once "../includes/auth.php";
require_once "../config/db_connect.php";
/** @var mysqli $conn */
require_once "../models/AdminModel.php";

require_admin();

$settings = get_all_settings($conn);

$page_title = "Platform Settings";
require_once "../includes/header.php";
?>

<h1>Platform Settings</h1>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
<?php endif; ?>

<?php if (isset($_GET['err'])): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($_GET['err']); ?></div>
<?php endif; ?>

<div class="card" style="max-width: 600px;">
    <form method="POST" action="../controllers/admin/SettingsController.php?action=save">

        <label>Chef Verification Mode</label>
        <select name="chef_verification_mode" style="margin-bottom: 16px;">
            <option value="moderator" <?php echo $settings['chef_verification_mode'] === 'moderator' ? 'selected' : ''; ?>>
                Managed by Moderators
            </option>
            <option value="admin" <?php echo $settings['chef_verification_mode'] === 'admin' ? 'selected' : ''; ?>>
                Requires Admin Approval
            </option>
        </select>

        <label>Maximum Bookmarks per User <span style="font-weight: normal; color: #777;">(0 = unlimited)</span></label>
        <input type="number" name="max_bookmarks" min="0"
            value="<?php echo htmlspecialchars($settings['max_bookmarks']); ?>">

        <label>Default Recipe Visibility</label>
        <select name="default_recipe_visibility" style="margin-bottom: 16px;">
            <option value="published" <?php echo $settings['default_recipe_visibility'] === 'published' ? 'selected' : ''; ?>>
                Published
            </option>
            <option value="draft" <?php echo $settings['default_recipe_visibility'] === 'draft' ? 'selected' : ''; ?>>
                Draft
            </option>
        </select>

        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
</div>

<?php require_once "../includes/footer.php"; ?>