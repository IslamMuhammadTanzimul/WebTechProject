<?php
require_once "../includes/auth.php";
require_once "../config/db_connect.php";
require_once "../models/AdminModel.php";

require_admin();

$recipes_per_cuisine = get_recipes_per_cuisine_per_month($conn);
$most_active_users   = get_most_active_users($conn);
$most_followed_chefs = get_most_followed_chefs($conn);
$avg_rating_cuisine  = get_avg_rating_by_cuisine($conn);
$user_growth         = get_user_growth_report($conn);
$content_creation    = get_content_creation_report($conn);

$page_title = "Reports & Analytics";
require_once "../includes/header.php";
?>

<h1>Reports & Analytics</h1>

<!-- User Growth -->
<div class="card">
    <h2>User Growth — Last 6 Months</h2>
    <div style="margin-bottom: 12px;">
        <select id="filter-user-growth" class="month-filter">
            <option value="">All Months</option>
            <option>January</option>
            <option>February</option>
            <option>March</option>
            <option>April</option>
            <option>May</option>
            <option>June</option>
            <option>July</option>
            <option>August</option>
            <option>September</option>
            <option>October</option>
            <option>November</option>
            <option>December</option>
        </select>
    </div>
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Role</th>
                <th>New Users</th>
            </tr>
        </thead>
        <tbody id="tbody-user-growth">
            <?php if (empty($user_growth)): ?>
                <tr>
                    <td colspan="3">No data available.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($user_growth as $row): ?>
                    <tr data-month="<?php echo strtok($row['month'], ' '); ?>">
                        <td><?php echo $row['month']; ?></td>
                        <td><?php echo ucfirst($row['role']); ?></td>
                        <td><?php echo $row['total']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <p id="no-data-user-growth" style="display:none; color:#777; font-size:14px; margin-top:8px;">No data for selected month.</p>
</div>

<!-- Content Creation -->
<div class="card">
    <h2>Content Creation — Last 6 Months</h2>
    <div style="margin-bottom: 12px;">
        <select id="filter-content" class="month-filter">
            <option value="">All Months</option>
            <option>January</option>
            <option>February</option>
            <option>March</option>
            <option>April</option>
            <option>May</option>
            <option>June</option>
            <option>July</option>
            <option>August</option>
            <option>September</option>
            <option>October</option>
            <option>November</option>
            <option>December</option>
        </select>
    </div>
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Total Recipes</th>
                <th>Published</th>
                <th>Drafts</th>
            </tr>
        </thead>
        <tbody id="tbody-content">
            <?php if (empty($content_creation)): ?>
                <tr>
                    <td colspan="4">No data available.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($content_creation as $row): ?>
                    <tr data-month="<?php echo strtok($row['month'], ' '); ?>">
                        <td><?php echo $row['month']; ?></td>
                        <td><?php echo $row['total_recipes']; ?></td>
                        <td><?php echo $row['published']; ?></td>
                        <td><?php echo $row['drafts']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <p id="no-data-content" style="display:none; color:#777; font-size:14px; margin-top:8px;">No data for selected month.</p>
</div>

<!-- Recipes per Cuisine per Month -->
<div class="card">
    <h2>Recipes per Cuisine — Last 6 Months</h2>
    <div style="margin-bottom: 12px;">
        <select id="filter-cuisine" class="month-filter">
            <option value="">All Months</option>
            <option>January</option>
            <option>February</option>
            <option>March</option>
            <option>April</option>
            <option>May</option>
            <option>June</option>
            <option>July</option>
            <option>August</option>
            <option>September</option>
            <option>October</option>
            <option>November</option>
            <option>December</option>
        </select>
    </div>
    <table>
        <thead>
            <tr>
                <th>Cuisine</th>
                <th>Month</th>
                <th>Total Recipes</th>
            </tr>
        </thead>
        <tbody id="tbody-cuisine">
            <?php if (empty($recipes_per_cuisine)): ?>
                <tr>
                    <td colspan="3">No data available.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($recipes_per_cuisine as $row): ?>
                    <tr data-month="<?php echo strtok($row['month'], ' '); ?>">
                        <td><?php echo htmlspecialchars($row['cuisine_name']); ?></td>
                        <td><?php echo $row['month']; ?></td>
                        <td><?php echo $row['total']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <p id="no-data-cuisine" style="display:none; color:#777; font-size:14px; margin-top:8px;">No data for selected month.</p>
</div>

<!-- Most Active Users -->
<div class="card">
    <h2>Most Active Users</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Role</th>
                <th>Recipes</th>
                <th>Reviews</th>
                <th>Bookmarks</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($most_active_users)): ?>
                <tr>
                    <td colspan="6">No data available.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($most_active_users as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo ucfirst($row['role']); ?></td>
                        <td><?php echo $row['recipes']; ?></td>
                        <td><?php echo $row['reviews']; ?></td>
                        <td><?php echo $row['bookmarks']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Most Followed Chefs -->
<div class="card">
    <h2>Most Followed Chefs</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Followers</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($most_followed_chefs)): ?>
                <tr>
                    <td colspan="3">No data available.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($most_followed_chefs as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo $row['followers']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Average Rating by Cuisine -->
<div class="card">
    <h2>Average Rating by Cuisine</h2>
    <table>
        <thead>
            <tr>
                <th>Cuisine</th>
                <th>Avg Rating</th>
                <th>Total Reviews</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($avg_rating_cuisine)): ?>
                <tr>
                    <td colspan="3">No data available.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($avg_rating_cuisine as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['cuisine_name']); ?></td>
                        <td><?php echo $row['avg_rating']; ?></td>
                        <td><?php echo $row['total_reviews']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<script>
    function filterTable(selectId, tbodyId, noDataId) {
        const select = document.getElementById(selectId);
        const tbody = document.getElementById(tbodyId);
        const noData = document.getElementById(noDataId);
        const month = select.value;
        const rows = tbody.querySelectorAll('tr');
        let visible = 0;

        rows.forEach(row => {
            if (!month || row.dataset.month === month) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        noData.style.display = visible === 0 ? 'block' : 'none';
    }

    document.getElementById('filter-user-growth').addEventListener('change', function() {
        filterTable('filter-user-growth', 'tbody-user-growth', 'no-data-user-growth');
    });

    document.getElementById('filter-content').addEventListener('change', function() {
        filterTable('filter-content', 'tbody-content', 'no-data-content');
    });

    document.getElementById('filter-cuisine').addEventListener('change', function() {
        filterTable('filter-cuisine', 'tbody-cuisine', 'no-data-cuisine');
    });
</script>
<?php require_once "../includes/footer.php"; ?>