<?php
// models/ShoppingListModel.php

/**
 * Create a new, empty shopping list for a user.
 * Returns the ID of the newly created list so we can add items to it.
 */
function createShoppingList($conn, $user_id, $list_name)
{
    $sql = "INSERT INTO shopping_lists (user_id, name) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return false;

    $stmt->bind_param("is", $user_id, $list_name);

    if ($stmt->execute()) {
        $new_id = $stmt->insert_id;
        $stmt->close();
        return $new_id;
    }

    $stmt->close();
    return false;
}

/**
 * Fetch all shopping lists belonging to a specific user.
 */
function getUserShoppingLists($conn, $user_id)
{
    $sql = "SELECT * FROM shopping_lists WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return [];

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $lists = [];
    while ($row = $result->fetch_assoc()) {
        $lists[] = $row;
    }

    $stmt->close();
    return $lists;
}

/**
 * Add a single ingredient to a specific shopping list.
 */
function addIngredientToList($conn, $list_id, $ingredient_name, $quantity, $unit, $recipe_id = null)
{
    $sql = "INSERT INTO shopping_list_items (list_id, ingredient_name, quantity, unit, recipe_id) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return false;

    // "isssi" = Integer, String, String, String, Integer
    $stmt->bind_param("isssi", $list_id, $ingredient_name, $quantity, $unit, $recipe_id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

/**
 * Fetch all items inside a specific shopping list.
 * We securely join the shopping_lists table to ensure the current user actually owns this list.
 */
function getListItems($conn, $list_id, $user_id)
{
    $sql = "SELECT sli.* FROM shopping_list_items sli
            JOIN shopping_lists sl ON sli.list_id = sl.id
            WHERE sli.list_id = ? AND sl.user_id = ?
            ORDER BY sli.is_checked ASC, sli.id ASC";

    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return [];

    $stmt->bind_param("ii", $list_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    $stmt->close();
    return $items;
}

/**
 * Toggle an item between checked (1) and unchecked (0).
 */
function updateItemStatus($conn, $item_id, $user_id, $is_checked)
{
    // Secure update: only allow update if the item belongs to a list owned by the user
    $sql = "UPDATE shopping_list_items sli
            JOIN shopping_lists sl ON sli.list_id = sl.id
            SET sli.is_checked = ?
            WHERE sli.id = ? AND sl.user_id = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return false;

    $stmt->bind_param("iii", $is_checked, $item_id, $user_id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

/**
 * Delete an entire shopping list (cascade handles deleting the items).
 */
function deleteShoppingList($conn, $list_id, $user_id)
{
    $sql = "DELETE FROM shopping_lists WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return false;

    $stmt->bind_param("ii", $list_id, $user_id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}
?>