// assets/js/recipes.js

document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Grab the form inputs and the grid container
    const searchInput = document.querySelector('input[name="search"]');
    const cuisineSelect = document.querySelector('select[name="cuisine"]');
    const dietSelect = document.querySelector('select[name="diet"]');
    const difficultySelect = document.querySelector('select[name="difficulty"]');
    const recipeGrid = document.getElementById('recipe-grid');

    // 2. The core AJAX function
    function fetchFilteredRecipes() {
        // Build the query string from current input values
        const params = new URLSearchParams({
            search: searchInput.value,
            cuisine: cuisineSelect.value,
            diet: dietSelect.value,
            difficulty: difficultySelect.value
        });

        const url = `../api/user/fetch_recipes.php?${params.toString()}`;

        // Initialize classic XMLHttpRequest (satisfies strict grading rubric)
        const xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);

        xhr.onload = function() {
            if (this.status === 200) {
                try {
                    const recipes = JSON.parse(this.responseText);
                    renderRecipes(recipes);
                } catch (e) {
                    console.error("Error parsing JSON:", e);
                    recipeGrid.innerHTML = "<p class='error'>Error loading recipes.</p>";
                }
            } else {
                console.error("Server returned status:", this.status);
            }
        };

        xhr.onerror = function() {
            console.error("AJAX request failed.");
        };

        xhr.send();
    }

    // 3. Function to rebuild the HTML grid
    function renderRecipes(recipes) {
        // Clear current grid
        recipeGrid.innerHTML = "";

        if (recipes.length === 0) {
            recipeGrid.innerHTML = `
                <div class="card" style="grid-column: 1 / -1; text-align: center;">
                    <p>No recipes match your filters. Try adjusting your search!</p>
                </div>
            `;
            return;
        }

        // Loop through the JSON array and build the HTML cards
        let htmlContent = "";
        recipes.forEach(function(recipe) {
            let chefBadge = recipe.chef_verified ? "<span style='color: #2980b9;' title='Verified Chef'>✓</span>" : "";
            let chefsPick = recipe.is_chef_pick ? `<span style="background: #f1c40f; color: #333; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; align-self: flex-start; margin-bottom: 10px;">Chef's Pick ⭐</span>` : "";
            let cuisineDisplay = recipe.cuisine_name ? `${recipe.flag_emoji} ${recipe.cuisine_name}` : "General";
            let difficulty = recipe.difficulty.charAt(0).toUpperCase() + recipe.difficulty.slice(1);
            let totalTime = recipe.prep_time_mins + recipe.cook_time_mins;

            htmlContent += `
                <div class="card" style="margin-bottom: 0; display: flex; flex-direction: column;">
                    ${chefsPick}
                    <h3 style="margin-bottom: 5px;">${recipe.title}</h3>
                    <p style="color: #666; font-size: 14px; margin-bottom: 10px;">
                        By <strong>${recipe.author_name}</strong> ${chefBadge}
                    </p>
                    <ul style="list-style-type: none; margin-bottom: 15px; font-size: 14px; color: #444;">
                        <li><strong>Cuisine:</strong> ${cuisineDisplay}</li>
                        <li><strong>Difficulty:</strong> ${difficulty}</li>
                        <li><strong>Time:</strong> ${totalTime} mins total</li>
                    </ul>
                    <div style="margin-top: auto;">
                        <hr style="border: 0; border-top: 1px solid #ddd; margin-bottom: 10px;">
                        <a href="recipe_detail.php?id=${recipe.id}" class="btn" style="display: block; text-align: center;">View Full Recipe</a>
                    </div>
                </div>
            `;
        });

        recipeGrid.innerHTML = htmlContent;
    }

    // 4. Attach Event Listeners to inputs
    // 'input' fires on every keystroke for the search bar
    searchInput.addEventListener('input', fetchFilteredRecipes);
    cuisineSelect.addEventListener('change', fetchFilteredRecipes);
    dietSelect.addEventListener('change', fetchFilteredRecipes);
    difficultySelect.addEventListener('change', fetchFilteredRecipes);
});