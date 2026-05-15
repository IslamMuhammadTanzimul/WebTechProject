<?php
// user/meal_plan.php
$page_title = "Weekly Meal Planner - Recipe Sharing Platform";
$base_url = "../";
include "../includes/header.php";

require_once "../config/db_connect.php";
require_once "../includes/auth.php";
require_once "../models/MealPlanModel.php";
require_once "../models/BookmarkModel.php";

// Protect the route
require_role("user", $base_url);

$user_id = $_SESSION['user_id'];

// 1. Determine which week we are looking at.
// The HTML week input sends format: "2026-W20". Default to current week.
$selected_week = isset($_GET['week']) ? $_GET['week'] : date('Y-\WW');

// Convert "2026-W20" into a standard Date string for the Monday of that week
$year = (int) substr($selected_week, 0, 4);
$week_no = (int) substr($selected_week, 6, 2);
$date = new DateTime();
$date->setISODate($year, $week_no);
$week_start_date = $date->format('Y-m-d');

// 2. Fetch the user's saved recipes (to populate the dropdowns)
$saved_recipes = getBookmarkedRecipes($conn, $user_id);

// 3. Fetch the existing meal plan for this specific week (if one exists)
$meal_plan = getMealPlan($conn, $user_id, $week_start_date);
$entries = [];
$schedule = [];

if ($meal_plan) {
    $entries = getMealPlanEntries($conn, $meal_plan['id']);
    // Organize entries into an easy-to-read array: $schedule['monday']['dinner'] = recipe_id
    foreach ($entries as $entry) {
        $schedule[$entry['day_of_week']][$entry['meal_type']] = $entry['recipe_id'];
    }
}

// Data structures for building the grid
$days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
$meals = ['breakfast', 'lunch', 'dinner', 'snack'];
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
        <div>
            <h2>📅 Weekly Meal Planner</h2>
            <p>Assign your saved recipes to specific days. Changes save automatically.</p>
        </div>

        <form method="GET" action="meal_plan.php"
            style="background: #f9f9f9; padding: 15px; border-radius: 6px; border: 1px solid #ddd;">
            <label style="display: inline-block; margin-right: 10px;">Select Week:</label>
            <input type="week" name="week" value="<?php echo htmlspecialchars($selected_week); ?>"
                onchange="this.form.submit()" style="padding: 8px; width: auto; display: inline-block;">
        </form>
    </div>
</div>

<div class="card" style="overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 800px;">
        <thead>
            <tr style="background-color: #34495e; color: white;">
                <th style="padding: 12px; border: 1px solid #ddd;">Day</th>
                <?php foreach ($meals as $meal): ?>
                    <th style="padding: 12px; border: 1px solid #ddd;">
                        <?php echo ucfirst($meal); ?>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($days as $day): ?>
                <tr>
                    <td style="padding: 12px; border: 1px solid #ddd; font-weight: bold; background: #fcfcfc;">
                        <?php echo ucfirst($day); ?>
                    </td>

                    <?php foreach ($meals as $meal): ?>
                        <td style="padding: 12px; border: 1px solid #ddd;">

                            <select class="meal-select" data-week="<?php echo $week_start_date; ?>"
                                data-day="<?php echo $day; ?>" data-meal="<?php echo $meal; ?>"
                                style="width: 100%; padding: 6px; border: 1px solid #ccc; border-radius: 4px;">

                                <option value="0">-- Clear Slot --</option>

                                <?php foreach ($saved_recipes as $recipe): ?>
                                    <?php
                                    $is_selected = isset($schedule[$day][$meal]) && $schedule[$day][$meal] == $recipe['id'];
                                    ?>
                                    <option value="<?php echo $recipe['id']; ?>" <?php echo $is_selected ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($recipe['title']); ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>

                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="../assets/js/meal_planner.js"></script>

<?php include "../includes/footer.php"; ?>