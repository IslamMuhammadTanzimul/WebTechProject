<?php
$page_title = "User Management — Recipe Sharing Platform";
$base_url = "../";
include "../includes/header.php";

require_once "../config/db_connect.php";
require_once "../includes/auth.php";
require_role('admin', '../');
require_once "../models/UserModel.php";

$search = $_GET['search'] ?? '';
$role_filter = $_GET['role'] ?? '';
$status_filter = $_GET['status'] ?? '';
$users = getUsers($conn, $search, $role_filter, $status_filter);
?>

<h2>User Management</h2>

<?php if (isset($_SESSION['success'])): ?>
    <p class="success"><?php echo $_SESSION['success'];
                        unset($_SESSION['success']); ?></p>
<?php endif; ?>

<form method="get" class="filter-form">
    <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
    <select name="role">
        <option value="">All Roles</option>
        <option value="user" <?php echo $role_filter === 'user' ? 'selected' : ''; ?>>User</option>
        <option value="chef" <?php echo $role_filter === 'chef' ? 'selected' : ''; ?>>Chef</option>
        <option value="moderator" <?php echo $role_filter === 'moderator' ? 'selected' : ''; ?>>Moderator</option>
        <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Admin</option>
    </select>
    <select name="status">
        <option value="">All</option>
        <option value="1" <?php echo $status_filter === '1' ? 'selected' : ''; ?>>Active</option>
        <option value="0" <?php echo $status_filter === '0' ? 'selected' : ''; ?>>Inactive</option>
    </select>
    <input type="submit" value="Filter" class="btn">
    <a href="users.php" class="btn btn-secondary">Reset</a>
</form>

<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Active</th>
                <th>Chef</th>
                <th>Joined</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $users->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><span class="role-badge role-<?php echo $user['role']; ?>"><?php echo $user['role']; ?></span></td>
                    <td><?php echo $user['is_active'] ? '✅' : '❌'; ?></td>
                    <td><?php echo $user['chef_verified'] ? '✅' : '—'; ?></td>
                    <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                    <td><a href="user_detail.php?id=<?php echo $user['id']; ?>" class="btn-small">View</a></td>
                </tr>
            <?php endwhile;
            if ($users->num_rows === 0): ?>
                <tr>
                    <td colspan="9" style="text-align:center;color:#888;">No users found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include "../includes/footer.php"; ?>