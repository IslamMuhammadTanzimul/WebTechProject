<?php
// dev/seed_database.php 

require_once '../config/db_connect.php';

echo "<h2>Database Seeder Starting...</h2>";

// 1. Seed Cuisines
$conn->query("INSERT IGNORE INTO cuisines (id, name, flag_emoji) VALUES 
    (1, 'Italian', '🇮🇹'), 
    (2, 'Mexican', '🇲🇽'), 
    (3, 'Indian', '🇮🇳'), 
    (4, 'Japanese', '🇯🇵')");
echo "Cuisines seeded.<br>";

// 2. Seed Diet Types
$conn->query("INSERT IGNORE INTO diet_types (id, name) VALUES 
    (1, 'Vegetarian'), 
    (2, 'Vegan'), 
    (3, 'Keto'), 
    (4, 'Gluten-Free'), 
    (5, 'Halal')");
echo "Diet Types seeded.<br>";

// 3. Seed a Dummy Chef
// Password is 'password123'
$hashed_password = password_hash('password123', PASSWORD_DEFAULT);
$conn->query("INSERT IGNORE INTO users (id, name, username, email, password_hash, role, is_active, chef_verified) 
    VALUES (999, 'Gordon Ramsay', 'gordon_chef', 'gordon@example.com', '$hashed_password', 'chef', 1, 1)");
echo "Dummy Chef seeded.<br>";

// 4. Seed Dummy Recipes
$conn->query("INSERT IGNORE INTO recipes (id, author_id, cuisine_id, title, description, diet_type_id, difficulty, prep_time_mins, cook_time_mins, servings, status, is_chef_pick) VALUES 
    (1, 999, 1, 'Classic Margherita Pizza', 'A traditional Neapolitan pizza with fresh tomatoes and mozzarella.', 1, 'medium', 20, 15, 2, 'published', 1),
    (2, 999, 2, 'Spicy Beef Tacos', 'Authentic street-style tacos with a fiery kick.', NULL, 'easy', 15, 10, 4, 'published', 0),
    (3, 999, 3, 'Creamy Chicken Tikka Masala', 'Rich and creamy curry served with naan.', 5, 'hard', 30, 45, 4, 'published', 1),
    (4, 999, 4, 'Vegetarian Sushi Rolls', 'Fresh cucumber and avocado maki rolls.', 2, 'medium', 45, 0, 2, 'published', 0)");
echo "Dummy Recipes seeded.<br>";

echo "<br><strong style='color:green;'>Seeding Complete!</strong> You can now go to <a href='../user/recipes.php'>Browse Recipes</a>.";
?>