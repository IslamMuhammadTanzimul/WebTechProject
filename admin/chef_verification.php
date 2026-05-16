<?php
require_once "../includes/auth.php";
require_once "../config/db_connect.php";
require_once "../models/AdminModel.php";

require_admin();

$filter   = isset($_GET['status']) ? $_GET['status'] : "";
$requests = get_verification_requests($conn, $filter);

$page_title = "Chef Verification";
require_once "../includes/header.php";
?>

<h1>Chef Verification</h1>

<!-- Filter Tabs -->
<div style="margin-bottom: 16px; display: flex; gap: 8px;">
    <a href="chef_verification.php" class="btn <?php echo $filter === '' ? 'btn-primary' : 'btn-warning'; ?>">All</a>
    <a href="chef_verification.php?status=pending" class="btn <?php echo $filter === 'pending' ? 'btn-primary' : 'btn-warning'; ?>">Pending</a>
    <a href="chef_verification.php?status=approved" class="btn <?php echo $filter === 'approved' ? 'btn-primary' : 'btn-warning'; ?>">Approved</a>
    <a href="chef_verification.php?status=rejected" class="btn <?php echo $filter === 'rejected' ? 'btn-primary' : 'btn-warning'; ?>">Rejected</a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
<?php endif; ?>

<?php if (isset($_GET['err'])): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($_GET['err']); ?></div>
<?php endif; ?>

<div class="card">
    <table id="verification-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Motivation</th>
                <th>Portfolio</th>
                <th>Status</th>
                <th>Submitted</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($requests)): ?>
                <tr>
                    <td colspan="8">No requests found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($requests as $req): ?>
                    <tr class="request-row"
                        data-id="<?php echo $req['id']; ?>"
                        data-user-id="<?php echo $req['user_id']; ?>"
                        data-name="<?php echo htmlspecialchars($req['name']); ?>"
                        data-status="<?php echo $req['status']; ?>"
                        data-user-role="<?php echo $req['user_role']; ?>">
                        <td><?php echo $req['id']; ?></td>
                        <td><?php echo htmlspecialchars($req['name']); ?></td>
                        <td><?php echo htmlspecialchars($req['username']); ?></td>
                        <td><?php echo htmlspecialchars($req['email']); ?></td>
                        <td style="max-width: 200px;"><?php echo htmlspecialchars($req['motivation']); ?></td>
                        <td>
                            <?php if ($req['portfolio_link']): ?>
                                <a href="<?php echo htmlspecialchars($req['portfolio_link']); ?>" target="_blank">View</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?php echo ucfirst($req['status']); ?></td>
                        <td><?php echo date('d M Y', strtotime($req['submitted_at'])); ?></td>
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
        <a id="btn-approve" href="#" class="btn btn-success" style="display:none;">Approve</a>
        <a id="btn-reject" href="#" class="btn btn-danger" style="display:none;">Reject</a>
        <a id="btn-revoke" href="#" class="btn btn-warning" style="display:none;">Revoke Chef Status</a>
    </div>
</div>

<style>
    .request-row {
        cursor: pointer;
    }

    .request-row:hover {
        background: #f9f9f9;
    }

    .request-row.selected {
        background: #eaf3ff;
    }
</style>

<script>
    const base = "/WebTechProject/controllers/admin/ChefVerificationController.php";

    document.querySelectorAll('.request-row').forEach(row => {
        row.addEventListener('click', function() {
            document.querySelectorAll('.request-row').forEach(r => r.classList.remove('selected'));
            this.classList.add('selected');

            const id = this.dataset.id;
            const userId = this.dataset.userId;
            const name = this.dataset.name;
            const status = this.dataset.status;
            const userRole = this.dataset.userRole;

            document.getElementById('selected-name').textContent = name;

            // reset
            ['btn-approve', 'btn-reject', 'btn-revoke'].forEach(b => {
                document.getElementById(b).style.display = 'none';
            });

            if (status === 'pending') {
                document.getElementById('btn-approve').style.display = 'inline-block';
                document.getElementById('btn-approve').href = `${base}?action=approve&id=${id}&user_id=${userId}`;
                document.getElementById('btn-reject').style.display = 'inline-block';
                document.getElementById('btn-reject').href = `${base}?action=reject&id=${id}`;
            } else if (status === 'approved' && userRole === 'chef') {
                document.getElementById('btn-revoke').style.display = 'inline-block';
                document.getElementById('btn-revoke').href = `${base}?action=revoke&user_id=${userId}`;
            }

            document.getElementById('action-panel').style.display = 'block';
        });
    });
</script>

<?php require_once "../includes/footer.php"; ?>