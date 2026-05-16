<?php
require_once "../includes/auth.php";
require_once "../config/db_connect.php";
require_once "../models/AdminModel.php";

require_admin();

$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$users  = get_all_users($conn, $search);

$page_title = "User Management";
require_once "../includes/header.php";
?>

<h1>User Management</h1>

<form method="GET" style="margin-bottom: 16px; display: flex; gap: 8px;">
    <input type="text" name="search" placeholder="Search by name, email or role" value="<?php echo htmlspecialchars($search); ?>" style="width: 300px; margin: 0;">
    <button type="submit" class="btn btn-primary">Search</button>
    <?php if ($search): ?>
        <a href="users.php" class="btn btn-warning">Clear</a>
    <?php endif; ?>
</form>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
<?php endif; ?>

<?php if (isset($_GET['err'])): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($_GET['err']); ?></div>
<?php endif; ?>

<div class="card">
    <table id="users-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Joined</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="7">No users found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <tr class="user-row"
                        data-id="<?php echo $user['id']; ?>"
                        data-name="<?php echo htmlspecialchars($user['name']); ?>"
                        data-role="<?php echo $user['role']; ?>"
                        data-status="<?php echo $user['is_active']; ?>"
                        data-self="<?php echo ($user['id'] === (int)$_SESSION['user_id']) ? '1' : '0'; ?>">
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo ucfirst($user['role']); ?></td>
                        <td><?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?></td>
                        <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Action Panel -->
<div class="card" id="action-panel" style="display: none;">
    <p style="margin-bottom: 12px; font-size: 14px;">Selected: <strong id="selected-name"></strong></p>
    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
        <!-- <a id="btn-view" href="#" class="btn btn-primary">View</a> -->
        <a id="btn-activate" href="#" class="btn btn-success" style="display:none;">Activate</a>
        <a id="btn-deactivate" href="#" class="btn btn-warning" style="display:none;">Deactivate</a>
        <a id="btn-promote" href="#" class="btn btn-primary" style="display:none;">Make Moderator</a>
        <a id="btn-demote" href="#" class="btn btn-warning" style="display:none;">Demote</a>
    </div>
</div>

<style>
    .user-row {
        cursor: pointer;
    }

    .user-row:hover {
        background: #f9f9f9;
    }

    .user-row.selected {
        background: #eaf3ff;
    }
</style>

<script>
    const base = "/WebTechProject/controllers/admin/UserController.php";

    document.querySelectorAll('.user-row').forEach(row => {
        row.addEventListener('click', function() {
            // deselect previous
            document.querySelectorAll('.user-row').forEach(r => r.classList.remove('selected'));
            this.classList.add('selected');

            const id = this.dataset.id;
            const name = this.dataset.name;
            const role = this.dataset.role;
            const status = this.dataset.status;
            const isSelf = this.dataset.self === '1';

            document.getElementById('selected-name').textContent = name;
            //document.getElementById('btn-view').href = `view_user.php?id=${id}`;

            // reset buttons
            ['btn-activate', 'btn-deactivate', 'btn-promote', 'btn-demote'].forEach(b => {
                document.getElementById(b).style.display = 'none';
            });

            if (!isSelf && role !== 'admin') {
                if (status === '1') {
                    document.getElementById('btn-deactivate').style.display = 'inline-block';
                    document.getElementById('btn-deactivate').href = `${base}?action=deactivate&id=${id}`;
                } else {
                    document.getElementById('btn-activate').style.display = 'inline-block';
                    document.getElementById('btn-activate').href = `${base}?action=activate&id=${id}`;
                }

                if (role === 'user') {
                    document.getElementById('btn-promote').style.display = 'inline-block';
                    document.getElementById('btn-promote').href = `${base}?action=promote&id=${id}`;
                } else if (role === 'moderator') {
                    document.getElementById('btn-demote').style.display = 'inline-block';
                    document.getElementById('btn-demote').href = `${base}?action=demote&id=${id}`;
                }
            }

            document.getElementById('action-panel').style.display = 'block';
        });
    });
</script>

<?php require_once "../includes/footer.php"; ?>