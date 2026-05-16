<?php
require_once "../includes/auth.php";
require_once "../config/db_connect.php";
require_once "../models/AdminModel.php";

require_admin();

$id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user = get_user_by_id($conn, $id);

if (!$user) {
    header("Location: /WebTechProject/admin/users.php?err=User+not+found");
    exit();
}

$page_title = "View User";
require_once "../includes/header.php";
?>

<h1>User Detail</h1>

<div class="card" style="max-width: 600px;">
    <table>
        <tr>
            <th style="width: 160px;">ID</th>
            <td><?php echo $user['id']; ?></td>
        </tr>
        <tr>
            <th>Name</th>
            <td><?php echo htmlspecialchars($user['name']); ?></td>
        </tr>
        <tr>
            <th>Username</th>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
        </tr>
        <tr>
            <th>Role</th>
            <td><?php echo ucfirst($user['role']); ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?></td>
        </tr>
        <tr>
            <th>Joined</th>
            <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
        </tr>
    </table>

    <div style="margin-top: 16px;">
        <a href="users.php" class="btn btn-primary">Back to Users</a>
    </div>
</div>

<?php require_once "../includes/footer.php"; ?>