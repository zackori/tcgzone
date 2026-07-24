// =========================================
// Reviews.js
// Requires:
// reviews.php
// api/reviews.php
// =========================================

let currentRatingFilter = "all";
let currentSearch = "";
let searchDebounce;
let lastReviews = [];

// =========================================
// Load Reviews + Stats
// =========================================

async function loadReviews() {

    try {

        const params = new URLSearchParams({
            rating: currentRatingFilter,
            search: currentSearch
        });

        const response = await fetch(`api/reviews.php?${params.toString()}`);
        const data = await response.json();

        renderStats(data.stats);
        renderReviewsTable(data.reviews);

    } catch (error) {
        console.error("Reviews Error:", error);
    }

}

// =========================================
// Render Stat Cards
// =========================================

function renderStats(stats) {

    document.getElementById("totalReviews").innerHTML =
        Number(stats.total).toLocaleString();

    document.getElementById("averageRating").innerHTML =
        Number(stats.average).toFixed(1);

    // Approximate star/pokeball fill from the raw 1-5 average
    // (stats.average is on a 0-10 scale, so convert back to 1-5 to pick fill count)
    const rawAverage = stats.total > 0 ? (stats.average / 10) * 5 : 0;
    document.getElementById("scorePokeballs").innerHTML =
        renderPokeballs(Math.round(rawAverage));

    renderRatingBreakdown(stats.breakdown, stats.total);

}

function renderRatingBreakdown(breakdown, total) {

    const container = document.getElementById("ratingBreakdown");
    const colors = { 5: "#27ae60", 4: "#8bc34a", 3: "#f1c40f", 2: "#e67e22", 1: "#e74c3c" };
    const maxCount = Math.max(...Object.values(breakdown), 1);

    let html = "";

    for (let level = 5; level >= 1; level--) {

        const count = breakdown[level] || 0;
        const widthPct = count > 0 ? Math.max((count / maxCount) * 100, 4) : 0;

        html += `
            <div class="rating-breakdown-row">
                <span class="breakdown-label">${level}</span>
                <div class="bar-track">
                    <div class="bar-fill" style="width:${widthPct}%; background:${colors[level]};"></div>
                </div>
                <span class="breakdown-count">${count.toLocaleString()}</span>
            </div>
        `;

    }

    container.innerHTML = html;

}

// =========================================
// Render Table
// =========================================

function renderReviewsTable(reviews) {

    lastReviews = reviews;

    const tbody = document.getElementById("reviewsTable");
    tbody.innerHTML = "";

    if (reviews.length === 0) {
        tbody.innerHTML =
            `<tr><td colspan="6" style="text-align:center; color:#888;">No reviews found</td></tr>`;
        return;
    }

    reviews.forEach(review => {

        const row = document.createElement("tr");
        row.style.cursor = "pointer";

        const date = new Date(review.created_at).toLocaleDateString("en-US", {
            year: "numeric", month: "short", day: "numeric"
        });

        row.innerHTML = `
            <td>${review.id}</td>
            <td>${escapeHtml(review.name || "Anonymous")}</td>
            <td>${renderPokeballs(review.rating)}</td>
            <td>${escapeHtml(truncateText(review.review_text, 60))}</td>
            <td>${date}</td>
            <td>
                <button class="delete-btn" onclick="event.stopPropagation(); deleteReview(${review.id})">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        `;

        row.addEventListener("click", () => openReviewModal(review.id));

        tbody.appendChild(row);

    });

}

// =========================================
// Review Detail Modal
// =========================================

const reviewModal = document.getElementById("reviewModal");
const reviewModalTitle = document.getElementById("reviewModalTitle");
const reviewModalStars = document.getElementById("reviewModalStars");
const reviewModalText = document.getElementById("reviewModalText");
const reviewModalDate = document.getElementById("reviewModalDate");

function openReviewModal(id) {

    const review = lastReviews.find(r => r.id === id);
    if (!review || !reviewModal) return;

    const date = new Date(review.created_at).toLocaleDateString("en-US", {
        year: "numeric", month: "short", day: "numeric"
    });

    reviewModalTitle.textContent = review.name || "Anonymous";
    reviewModalStars.innerHTML = renderPokeballs(review.rating);
    reviewModalText.textContent = review.review_text;
    reviewModalDate.textContent = date;

    reviewModal.classList.add("open");
    reviewModal.setAttribute("aria-hidden", "false");
    document.body.classList.add("modal-open");

}

function closeReviewModal() {

    if (!reviewModal) return;

    reviewModal.classList.remove("open");
    reviewModal.setAttribute("aria-hidden", "true");
    document.body.classList.remove("modal-open");

}

document.querySelectorAll("[data-close-modal]").forEach(el => {
    el.addEventListener("click", closeReviewModal);
});

document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeReviewModal();
});

// =========================================
// Delete Review
// =========================================

async function deleteReview(id) {

    if (!confirm("Delete this review? This can't be undone.")) return;

    try {

        const response = await fetch("api/reviews.php", {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id })
        });

        const data = await response.json();

        if (data.success) {
            loadReviews();
        } else {
            alert(data.error || "Failed to delete review.");
        }

    } catch (error) {
        console.error("Delete Error:", error);
    }

}

// =========================================
// Helpers
// =========================================

function renderPokeballs(rating) {

    let icons = "";

    for (let i = 0; i < 5; i++) {

        const icon = i < rating ? "poke-open.svg" : "poke-close.svg";

        icons += `<img src="/tcgzone/assets/logos/review/${icon}" alt="Pokeball" class="review-rating-icon">`;

    }

    return icons;

}

function truncateText(text, maxLength) {
    if (!text) return "";
    if (text.length <= maxLength) return text;
    return text.slice(0, maxLength).trim() + "...";
}

function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text ?? "";
    return div.innerHTML;
}

// =========================================
// Event Listeners
// =========================================

document.getElementById("ratingFilter").addEventListener("change", (e) => {
    currentRatingFilter = e.target.value;
    loadReviews();
});

document.getElementById("searchReview").addEventListener("input", (e) => {
    clearTimeout(searchDebounce);
    searchDebounce = setTimeout(() => {
        currentSearch = e.target.value;
        loadReviews();
    }, 300);
});

// =========================================
// Initial Load
// =========================================

loadReviews();