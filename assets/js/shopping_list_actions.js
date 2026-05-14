// assets/js/shopping_list_actions.js

document.addEventListener("DOMContentLoaded", function() {

    // 1. Handle Checkbox Toggles
    const checkboxes = document.querySelectorAll(".item-checkbox");
    
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener("change", function() {
            const itemId = this.getAttribute("data-item-id");
            const isChecked = this.checked ? 1 : 0;
            const textSpan = this.nextElementSibling; // The span containing the ingredient text

            // Apply visual strike-through instantly for good UX
            if (this.checked) {
                textSpan.style.textDecoration = "line-through";
                textSpan.style.color = "#aaa";
            } else {
                textSpan.style.textDecoration = "none";
                textSpan.style.color = "#222";
            }

            // Send AJAX request to database
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../api/user/toggle_shopping_item.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            
            xhr.onerror = function() {
                console.error("AJAX request failed.");
                alert("Failed to save changes. Check your connection.");
            };

            xhr.send("item_id=" + itemId + "&is_checked=" + isChecked);
        });
    });

    // 2. Handle Delete List Buttons
    const deleteButtons = document.querySelectorAll(".delete-list-btn");

    deleteButtons.forEach(function(button) {
        button.addEventListener("click", function() {
            if (!confirm("Are you sure you want to delete this entire shopping list?")) {
                return;
            }

            const listId = this.getAttribute("data-list-id");
            const listCard = this.closest(".card"); // Find the parent card to remove it

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../api/user/delete_shopping_list.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function() {
                if (this.status === 200) {
                    try {
                        const response = JSON.parse(this.responseText);
                        if (response.status === "success") {
                            // Remove the card from the UI smoothly
                            listCard.style.display = "none";
                        } else {
                            alert("Error: " + response.error);
                        }
                    } catch (e) {
                        alert("Error processing response.");
                    }
                }
            };

            xhr.send("list_id=" + listId);
        });
    });

});