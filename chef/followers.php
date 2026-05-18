<?php
// chef/followers.php
$page_title = "My Followers - Recipe Sharing Platform";
$base_url = "../";
include "../includes/header.php";
include "../includes/chef_nav.php";
require_once "../config/db_connect.php";
require_role("chef", $base_url);

$chef_id = $_SESSION['user_id'];

$sql = "SELECT u.id, u.name, u.username, f.created_at AS followed_at
        FROM follows f
        JOIN users u ON u.id = f.follower_id
        WHERE f.chef_id = ?
        ORDER BY f.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $chef_id);
$stmt->execute();
$followers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Monthly trend last 6 months
$trend_sql = "SELECT DATE_FORMAT(created_at,'%Y-%m') AS month, COUNT(*) AS cnt
              FROM follows WHERE chef_id=? AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
              GROUP BY month ORDER BY month";
$stmt = $conn->prepare($trend_sql);
$stmt->bind_param("i", $chef_id);
$stmt->execute();
$trend = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="card">
    <h2>👥 Followers (<?php echo count($followers); ?>)</h2>

    <?php if (!empty($trend)): ?>
    <h3 style="margin-top:1rem;">Follower Growth Trend</h3>
    <canvas id="followerTrend" style="max-height:200px;"></canvas>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    new Chart(document.getElementById('followerTrend').getContext('2d'), {
        type: 'line',
        data: {
            labels: [<?php echo implode(',', array_map(fn($t) => '"'.$t['month'].'"', $trend)); ?>],
            datasets: [{
                label: 'New Followers',
                data: [<?php echo implode(',', array_column($trend, 'cnt')); ?>],
                fill: true,
                borderColor: 'rgba(184,80,47,1)',
                backgroundColor: 'rgba(184,80,47,0.1)',
                tension: 0.3
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });
    </script>
    <?php endif; ?>

    <h3 style="margin-top:1.5rem;">Follower List</h3>
    <?php if (empty($followers)): ?>
        <p>No followers yet.</p>
    <?php else: ?>
        <table style="width:100%;border-collapse:collapse;">
            <thead><tr style="border-bottom:2px solid #eee;">
                <th style="text-align:left;padding:0.5rem;">Name</th>
                <th style="text-align:left;">Username</th>
                <th>Followed On</th>
            </tr></thead>
            <tbody>
            <?php foreach ($followers as $f): ?>
                <tr style="border-bottom:1px solid #f0f0f0;">
                    <td style="padding:0.5rem;"><?php echo htmlspecialchars($f['name']); ?></td>
                    <td>@<?php echo htmlspecialchars($f['username']); ?></td>
                    <td style="text-align:center;"><?php echo date('M j, Y', strtotime($f['followed_at'])); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>
