// assets/js/follow_chef.js

document.addEventListener("DOMContentLoaded", function() {
    const followBtn = document.getElementById("follow-chef-btn");

    if (followBtn) {
        followBtn.addEventListener("click", function(e) {
            e.preventDefault();
            
            const chefId = this.getAttribute("data-chef-id");
            const btn = this;
            const originalText = btn.innerHTML;

            // Give the user a tiny visual cue that it is loading
            btn.innerHTML = "...";

            // Initialize classic XMLHttpRequest
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../api/user/toggle_follow.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function() {
                if (this.status === 200) {
                    try {
                        const response = JSON.parse(this.responseText);
                        
                        if (response.status === "followed") {
                            btn.innerHTML = "Following";
                            btn.style.backgroundColor = "#2980b9"; // Blue
                            btn.style.color = "white";
                        } else if (response.status === "unfollowed") {
                            btn.innerHTML = "+ Follow";
                            btn.style.backgroundColor = "white";
                            btn.style.color = "#2980b9"; // Blue text
                        } else {
                            alert("Error: " + response.error);
                            btn.innerHTML = originalText;
                        }
                    } catch (err) {
                        console.error("Failed to parse JSON response");
                        btn.innerHTML = originalText;
                    }
                }
            };

            xhr.onerror = function() {
                alert("Network error. Please check your connection.");
                btn.innerHTML = originalText;
            };

            xhr.send("chef_id=" + chefId);
        });
    }
});