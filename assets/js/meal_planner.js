// assets/js/meal_planner.js

document.addEventListener("DOMContentLoaded", function() {
    
    // Grab all the dropdowns in the grid
    const mealSelects = document.querySelectorAll(".meal-select");

    mealSelects.forEach(function(select) {
        
        // Listen for when the user changes the selected recipe
        select.addEventListener("change", function() {
            
            const recipeId = this.value;
            const weekStartDate = this.getAttribute("data-week");
            const dayOfWeek = this.getAttribute("data-day");
            const mealType = this.getAttribute("data-meal");
            const originalBg = this.style.backgroundColor || "white";

            // 1. Visual feedback: briefly turn yellow to indicate "Saving..."
            this.style.backgroundColor = "#fff3cd";

            // 2. Initialize classic XMLHttpRequest for the API call
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../api/user/save_meal_plan_entry.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function() {
                if (this.status === 200) {
                    try {
                        const response = JSON.parse(this.responseText);
                        
                        if (response.status === "success") {
                            // 3. Visual feedback: Flash green for success, then return to normal
                            select.style.backgroundColor = "#d4edda";
                            setTimeout(() => {
                                select.style.backgroundColor = originalBg;
                            }, 1000);
                        } else {
                            // Highlight red if the database rejected it
                            alert("Error: " + response.error);
                            select.style.backgroundColor = "#f8d7da"; 
                        }
                    } catch (e) {
                        console.error("Error parsing JSON:", e);
                        alert("Error parsing server response.");
                        select.style.backgroundColor = "#f8d7da";
                    }
                } else {
                    console.error("Server returned status:", this.status);
                    alert("Server error. Could not save your meal plan.");
                    select.style.backgroundColor = "#f8d7da";
                }
            };

            xhr.onerror = function() {
                alert("Network error. Please check your connection.");
                select.style.backgroundColor = "#f8d7da";
            };

            // 4. Send the required data as a URL-encoded string
            const params = "recipe_id=" + recipeId + 
                           "&week_start_date=" + encodeURIComponent(weekStartDate) + 
                           "&day_of_week=" + encodeURIComponent(dayOfWeek) + 
                           "&meal_type=" + encodeURIComponent(mealType);

            xhr.send(params);
        });
    });
});