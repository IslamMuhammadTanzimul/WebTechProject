<?php
require_once "../includes/auth.php";
require_once "../config/db_connect.php";
require_once "../models/AdminModel.php";

require_admin();

$announcements = get_announcements($conn);

$page_title = "Announcements";
require_once "../includes/header.php";
?>

<h1>Announcements</h1>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
<?php endif; ?>

<?php if (isset($_GET['err'])): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($_GET['err']); ?></div>
<?php endif; ?>

<!-- Create Announcement -->
<div class="card">
    <h2>New Announcement</h2>
    <form method="POST" action="../controllers/admin/AnnouncementController.php?action=create">
        <label>Title</label>
        <input type="text" name="title" required>

        <label>Message</label>
        <textarea name="message" rows="4" required style="width: 100%; padding: 8px; font-size: 14px; border: 1px solid #ccc; border-radius: 3px; margin-bottom: 12px;"></textarea>

        <button type="submit" class="btn btn-primary">Post Announcement</button>
    </form>
</div>

<!-- Announcements List -->
<div class="card">
    <h2>All Announcements</h2>
    <table id="announcements-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Message</th>
                <th>Posted By</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($announcements)): ?>
                <tr>
                    <td colspan="5">No announcements yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($announcements as $ann): ?>
                    <tr class="announcement-row"
                        data-id="<?php echo $ann['id']; ?>"
                        data-title="<?php echo htmlspecialchars($ann['title']); ?>">
                        <td><?php echo $ann['id']; ?></td>
                        <td><?php echo htmlspecialchars($ann['title']); ?></td>
                        <td><?php echo htmlspecialchars($ann['message']); ?></td>
                        <td><?php echo htmlspecialchars($ann['created_by_name']); ?></td>
                        <td><?php echo date('d M Y', strtotime($ann['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Action Panel -->
    <div id="action-panel" style="display: none; margin-top: 12px;">
        <p style="margin-bottom: 8px; font-size: 14px;">Selected: <strong id="selected-title"></strong></p>
        <a id="btn-delete" href="#" class="btn btn-danger">Delete</a>
    </div>
</div>

<style>
    .announcement-row {
        cursor: pointer;
    }

    .announcement-row:hover {
        background: #f9f9f9;
    }

    .announcement-row.selected {
        background: #eaf3ff;
    }
</style>

<script>
    const base = "/WebTechProject/controllers/admin/AnnouncementController.php";

    document.querySelectorAll('.announcement-row').forEach(row => {
        row.addEventListener('click', function() {
            document.querySelectorAll('.announcement-row').forEach(r => r.classList.remove('selected'));
            this.classList.add('selected');

            const id = this.dataset.id;
            const title = this.dataset.title;

            document.getElementById('selected-title').textContent = title;
            document.getElementById('btn-delete').href = `${base}?action=delete&id=${id}`;
            document.getElementById('action-panel').style.display = 'block';
        });
    });
</script>

<?php require_once "../includes/footer.php"; ?>