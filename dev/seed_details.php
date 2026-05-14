<?php
// dev/seed_details.php
require_once '../config/db_connect.php';

echo "<h2>Seeding Recipe Details...</h2>";

// Insert ingredients for Recipe 1
$conn->query("INSERT IGNORE INTO ingredients (id, recipe_id, name, quantity, unit, order_index) VALUES 
    (1, 1, 'Pizza Dough', 1, 'ball', 1),
    (2, 1, 'Tomato Sauce', 0.5, 'cup', 2),
    (3, 1, 'Fresh Mozzarella', 200, 'g', 3),
    (4, 1, 'Fresh Basil', 10, 'leaves', 4)");
echo "Ingredients added.<br>";

// Insert steps for Recipe 1
$conn->query("INSERT IGNORE INTO steps (id, recipe_id, instruction, step_order) VALUES 
    (1, 1, 'Preheat oven to 475°F (245°C). Roll out the pizza dough on a floured surface.', 1),
    (2, 1, 'Spread the tomato sauce evenly over the dough, leaving a small border.', 2),
    (3, 1, 'Tear the fresh mozzarella into pieces and distribute them evenly over the sauce.', 3),
    (4, 1, 'Bake for 10-12 minutes until the crust is golden. Garnish with fresh basil before serving.', 4)");
echo "Steps added.<br>";

// Insert nutrition info for Recipe 1
$conn->query("INSERT IGNORE INTO nutrition_info (id, recipe_id, calories, protein_g, carbs_g, fat_g, fibre_g) VALUES 
    (1, 1, 850, 35.5, 90.0, 38.0, 4.5)");
echo "Nutrition added.<br>";

echo "<br><strong style='color:green;'>Details Seeded!</strong>";
?>