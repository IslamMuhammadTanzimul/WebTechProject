<?php
require_once "../includes/auth.php";
require_once "../config/db_connect.php";
/** @var mysqli $conn */
require_once "../models/AdminModel.php";

require_admin();

$moderators = get_moderators($conn);

// load activity if a moderator is selected
$selected_mod      = isset($_GET['mod_id']) ? (int)$_GET['mod_id'] : 0;
$mod_activity      = [];
$selected_mod_name = "";

if ($selected_mod) {
    $mod_activity = get_moderator_activity($conn, $selected_mod);
    foreach ($moderators as $mod) {
        if ($mod['id'] === $selected_mod) {
            $selected_mod_name = $mod['name'];
            break;
        }
    }
}

$page_title = "Moderation Team";
require_once "../includes/header.php";
?>

<h1>Moderation Team</h1>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
<?php endif; ?>

<?php if (isset($_GET['err'])): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($_GET['err']); ?></div>
<?php endif; ?>

<div class="card">
    <table id="moderators-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Status</th>
                <th>Reports Processed</th>
                <th>Joined</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($moderators)): ?>
                <tr>
                    <td colspan="7">No moderators found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($moderators as $mod): ?>
                    <tr class="mod-row <?php echo $selected_mod === $mod['id'] ? 'selected' : ''; ?>"
                        data-id="<?php echo $mod['id']; ?>"
                        data-name="<?php echo htmlspecialchars($mod['name']); ?>">
                        <td><?php echo $mod['id']; ?></td>
                        <td><?php echo htmlspecialchars($mod['name']); ?></td>
                        <td><?php echo htmlspecialchars($mod['username']); ?></td>
                        <td><?php echo htmlspecialchars($mod['email']); ?></td>
                        <td><?php echo $mod['is_active'] ? 'Active' : 'Inactive'; ?></td>
                        <td><?php echo $mod['reports_processed']; ?></td>
                        <td><?php echo date('d M Y', strtotime($mod['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Action Panel -->
    <div id="action-panel" style="display: none; margin-top: 12px;">
        <p style="margin-bottom: 8px; font-size: 14px;">Selected: <strong id="selected-name"></strong></p>
        <a id="btn-activity" href="#" class="btn btn-primary">View Activity Log</a>
    </div>
</div>

<!-- Activity Log -->
<?php if ($selected_mod && !empty($mod_activity)): ?>
    <div class="card">
        <h2>Activity Log — <?php echo htmlspecialchars($selected_mod_name); ?></h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Note</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mod_activity as $log): ?>
                    <tr>
                        <td><?php echo $log['id']; ?></td>
                        <td><?php echo ucfirst($log['entity_type']); ?></td>
                        <td><?php echo htmlspecialchars($log['reason']); ?></td>
                        <td><?php echo ucfirst($log['status']); ?></td>
                        <td><?php echo htmlspecialchars($log['moderator_note'] ?? '-'); ?></td>
                        <td><?php echo date('d M Y', strtotime($log['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php elseif ($selected_mod && empty($mod_activity)): ?>
    <div class="card">
        <p>No activity found for this moderator.</p>
    </div>
<?php endif; ?>

<style>
    .mod-row {
        cursor: pointer;
    }

    .mod-row:hover {
        background: #f9f9f9;
    }

    .mod-row.selected {
        background: #eaf3ff;
    }
</style>

<script>
    document.querySelectorAll('.mod-row').forEach(row => {
        row.addEventListener('click', function() {
            document.querySelectorAll('.mod-row').forEach(r => r.classList.remove('selected'));
            this.classList.add('selected');

            const id = this.dataset.id;
            const name = this.dataset.name;

            document.getElementById('selected-name').textContent = name;
            document.getElementById('btn-activity').href = `moderators.php?mod_id=${id}`;
            document.getElementById('action-panel').style.display = 'block';
        });
    });
</script>

<?php require_once "../includes/footer.php"; ?>