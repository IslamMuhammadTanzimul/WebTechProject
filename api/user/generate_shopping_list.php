<?php
// api/user/generate_shopping_list.php
session_start();

require_once "../../config/db_connect.php";
require_once "../../includes/auth.php";
require_once "../../models/RecipeModel.php";
require_once "../../models/ShoppingListModel.php";

// Set header for JSON response
header('Content-Type: application/json');

// 1. Security Check
if (!is_logged_in() || $_SESSION['role'] != 'user') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized access"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION['user_id'];
    $recipe_id = isset($_POST['recipe_id']) ? (int) $_POST['recipe_id'] : 0;

    if ($recipe_id <= 0) {
        echo json_encode(["error" => "Invalid recipe ID."]);
        exit();
    }

    // 2. Fetch Recipe Details and Ingredients
    $recipe = getRecipeById($conn, $recipe_id);
    $ingredients = getRecipeIngredients($conn, $recipe_id);

    if (!$recipe) {
        echo json_encode(["error" => "Recipe not found."]);
        exit();
    }

    if (empty($ingredients)) {
        echo json_encode(["error" => "This recipe has no ingredients to add."]);
        exit();
    }

    // 3. Create the Main Shopping List
    $list_name = "Ingredients for: " . $recipe['title'];
    $list_id = createShoppingList($conn, $user_id, $list_name);

    if (!$list_id) {
        echo json_encode(["error" => "Failed to create shopping list. Please try again."]);
        exit();
    }

    // 4. Loop Through Ingredients and Add Them to the List
    $items_added = 0;
    foreach ($ingredients as $ing) {
        // We pass the ingredient name, quantity, unit, and the recipe_id
        $success = addIngredientToList($conn, $list_id, $ing['name'], $ing['quantity'], $ing['unit'], $recipe_id);

        if ($success) {
            $items_added++;
        }
    }

    // 5. Return Success Response
    echo json_encode([
        "status" => "success",
        "message" => "Successfully created a shopping list with $items_added ingredients!",
        "list_id" => $list_id
    ]);

} else {
    echo json_encode(["error" => "Invalid request method."]);
}
?>