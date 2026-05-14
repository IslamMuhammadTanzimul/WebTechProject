// assets/js/bookmark.js

document.addEventListener("DOMContentLoaded", function() {
    const bookmarkBtn = document.getElementById("bookmark-btn");

    if (bookmarkBtn) {
        bookmarkBtn.addEventListener("click", function(e) {
            e.preventDefault();
            
            const recipeId = this.getAttribute("data-recipe-id");
            const btn = this;

            // Initialize classic XMLHttpRequest
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../api/user/toggle_bookmark.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function() {
                if (this.status === 200) {
                    try {
                        const response = JSON.parse(this.responseText);
                        
                        if (response.status === "added") {
                            btn.innerHTML = "❤️ Saved";
                            btn.style.backgroundColor = "#c0392b"; // Red
                            btn.style.color = "white";
                        } else if (response.status === "removed") {
                            btn.innerHTML = "🤍 Save Recipe";
                            btn.style.backgroundColor = "#ecf0f1"; // Light Gray
                            btn.style.color = "#333";
                        }
                    } catch (err) {
                        console.error("Failed to parse JSON response");
                    }
                }
            };

            // Send the recipe ID as form data
            xhr.send("recipe_id=" + recipeId);
        });
    }
});