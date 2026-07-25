const stars = document.querySelectorAll(".rating input");
const ratingText = document.getElementById("ratingText");

const form = document.getElementById("reviewForm");

const nameInput = document.getElementById("name");
const reviewInput = document.getElementById("review");

const reviewsContainer = document.getElementById("reviewsContainer");

let selectedRating = 0;

function showNotification(message) {
    let notification = document.querySelector(".notification");

    if (!notification) {
        notification = document.createElement("div");
        notification.className = "notification";
        notification.setAttribute("role", "status");
        notification.setAttribute("aria-live", "polite");
        document.body.appendChild(notification);
    }

    notification.innerHTML = '<span class="notification-icon" aria-hidden="true">&#10003;</span>';
    const messageElement = document.createElement("span");
    messageElement.textContent = message;
    notification.appendChild(messageElement);

    requestAnimationFrame(() => notification.classList.add("show"));
    clearTimeout(notification.hideTimer);
    notification.hideTimer = setTimeout(() => {
        notification.classList.remove("show");
    }, 2800);
}


// star selection
// stars.forEach((star) => {

//     star.addEventListener("change", function () {

//         selectedRating = Number(this.id.replace("star", ""));

//         ratingText.innerHTML =
//             "You selected <b>" + selectedRating + "</b> star" +
//             (selectedRating > 1 ? "s ⭐" : " ⭐");

//     });

// });

// Rating selection
stars.forEach((star) => {

    star.addEventListener("change", function () {

        selectedRating = Number(this.id.replace("star", ""));

        ratingText.innerHTML = `
            You selected <b>${selectedRating}</b> Pokéball${selectedRating > 1 ? "s" : ""}
            <img src="/tcgzone/assets/logos/review/poke-open.svg"
                 alt="Pokeball"
                 class="rating-icon">
        `;

    });

});




// submit
form.addEventListener("submit", async function (e) {

    e.preventDefault();

    // Reviews require an account now, so every review can be tied
    // to a user_id. Not logged in -> send them to log in instead
    // of letting the form proceed at all.
    if (!isLoggedIn) {
        window.location.href = "/tcgzone/customer/Login Page/login.html";
        return;
    }

    const name = nameInput.value.trim() || "Anonymous";
    const review = reviewInput.value.trim();

    // Check if a rating is selected
    if (selectedRating === 0) {
        alert("Please select a rating.");
        return;
    }

    // Check if a review is written
    if (review === "") {
        alert("Please write your review.");
        return;
    }

    // Save to the database before showing any success feedback
    const formData = new FormData();
    formData.append("name", name);
    formData.append("rating", selectedRating);
    formData.append("review", review);

    let result;
    try {
        const response = await fetch("submit_review.php", {
            method: "POST",
            body: formData
        });
        result = await response.json();
    } catch (err) {
        console.error(err);
        alert("Something went wrong submitting your review. Please try again.");
        return;
    }

    if (!result.success) {
        alert("Something went wrong submitting your review. Please try again.");
        return;
    }

    document.querySelector(".no-review")?.remove();

    // Create Review Card
    const card = document.createElement("div");
    card.className = "review-card";

    // Display stars — pokeball icons, matching the rating selector above
    let starsHTML = "";

    for (let i = 0; i < selectedRating; i++) {
        starsHTML += '<img src="/tcgzone/assets/logos/review/poke-open.svg" alt="star" class="review-star-icon">';
    }

    const date = new Date().toLocaleDateString();

    card.innerHTML = `
        <div class="review-header">
            <div class="review-name">${name}</div>
            <div class="review-stars">${starsHTML}</div>
        </div>

        <div class="review-text">
            ${review}
        </div>

        <div class="review-date">
            ${date}
        </div>
    `;



    // Reset form
    form.reset();
    stars.forEach((star) => {
        star.checked = false;
    });
    selectedRating = 0;
    ratingText.textContent = "No rating selected";

    // Keep the user on this page so the success toast remains visible.
    showNotification("Review added successfully!");

});
