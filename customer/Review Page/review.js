const stars = document.querySelectorAll(".rating input");
const ratingText = document.getElementById("ratingText");

const form = document.getElementById("reviewForm");

const nameInput = document.getElementById("name");
const reviewInput = document.getElementById("review");

const reviewsContainer = document.getElementById("reviewsContainer");

let selectedRating = 0;


// star selection
stars.forEach((star) => {

    star.addEventListener("change", function () {

        selectedRating = Number(this.id.replace("star", ""));

        ratingText.innerHTML =
            "You selected <b>" + selectedRating + "</b> star" +
            (selectedRating > 1 ? "s ⭐" : " ⭐");

    });

});


// submit
form.addEventListener("submit", function (e) {

    e.preventDefault();

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

    document.querySelector(".no-review")?.remove();

    // Create Review Card
    const card = document.createElement("div");
    card.className = "review-card";

    // Display stars
    let starsHTML = "";

    for (let i = 0; i < selectedRating; i++) {
        starsHTML += "⭐";
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
    selectedRating = 0;
    ratingText.textContent = "No rating selected";

    // Show success message
alert("Review added successfully!");

// Redirect to homepage
window.location.href = "/tcgzone/customer/Landing Page/index.html";

});