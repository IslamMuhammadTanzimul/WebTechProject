// assets/js/shopping_list.js

document.addEventListener("DOMContentLoaded", function() {
    const addToListBtn = document.getElementById("add-to-list-btn");

    if (addToListBtn) {
        addToListBtn.addEventListener("click", function(e) {
            e.preventDefault();
            
            const recipeId = this.getAttribute("data-recipe-id");
            const btn = this;
            const originalText = btn.innerHTML;

            // Visual feedback while the request processes
            btn.innerHTML = "⏳ Generating List...";
            btn.disabled = true;

            // Initialize classic XMLHttpRequest
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../api/user/generate_shopping_list.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function() {
                if (this.status === 200) {
                    try {
                        const response = JSON.parse(this.responseText);
                        
                        if (response.status === "success") {
                            // Change button to show success
                            btn.innerHTML = "✅ List Created!";
                            btn.style.backgroundColor = "#27ae60"; // Green
                            btn.style.color = "white";
                            
                            alert(response.message);
                        } else {
                            // Revert button and show error
                            btn.innerHTML = originalText;
                            btn.disabled = false;
                            alert("Error: " + response.error);
                        }
                    } catch (err) {
                        console.error("Failed to parse JSON response");
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                        alert("An unexpected error occurred.");
                    }
                } else {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    alert("Server error. Please try again.");
                }
            };

            xhr.onerror = function() {
                btn.innerHTML = originalText;
                btn.disabled = false;
                alert("Network error. Please check your connection.");
            };

            xhr.send("recipe_id=" + recipeId);
        });
    }
});