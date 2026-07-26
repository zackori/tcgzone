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
let reviewToDelete = null;
let reviewPage = 1;
const REVIEWS_PER_PAGE = 10;

// =========================================
// Load Reviews + Stats
// =========================================

async function loadReviews() {
  try {
    const params = new URLSearchParams({
      rating: currentRatingFilter,
      search: currentSearch,
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
  document.getElementById("totalReviews").innerHTML = Number(
    stats.total,
  ).toLocaleString();

  document.getElementById("averageRating").innerHTML = Number(
    stats.average,
  ).toFixed(1);

  // Approximate star/pokeball fill from the raw 1-5 average
  // stats.average is already on a 1-5 scale, just round it
  const displayRating = stats.total > 0 ? Math.round(stats.average) : 0;
  document.getElementById("scorePokeballs").innerHTML =
    renderPokeballs(displayRating);

  renderRatingBreakdown(stats.breakdown, stats.total);
}

function renderRatingBreakdown(breakdown, total) {
  const container = document.getElementById("ratingBreakdown");
  const colors = {
    5: "#27ae60",
    4: "#8bc34a",
    3: "#f1c40f",
    2: "#e67e22",
    1: "#e74c3c",
  };
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
  const totalPages = Math.max(1, Math.ceil(reviews.length / REVIEWS_PER_PAGE));
  reviewPage = Math.min(reviewPage, totalPages);
  const pageReviews = reviews.slice(
    (reviewPage - 1) * REVIEWS_PER_PAGE,
    reviewPage * REVIEWS_PER_PAGE,
  );
  document.getElementById("reviewPage").textContent = reviewPage;
  document.getElementById("previousReviewPage").disabled = reviewPage === 1;
  document.getElementById("nextReviewPage").disabled =
    reviewPage === totalPages;

  if (reviews.length === 0) {
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; color:#888;">No reviews found</td></tr>`;
    return;
  }

  pageReviews.forEach((review) => {
    const row = document.createElement("tr");
    row.style.cursor = "pointer";

    const date = new Date(review.created_at).toLocaleDateString("en-US", {
      year: "numeric",
      month: "short",
      day: "numeric",
    });

    row.innerHTML = `
            <td>${review.id}</td>
            <td>${escapeHtml(review.name || "Anonymous")}</td>
            <td>${renderPokeballs(review.rating)}</td>
            <td>${escapeHtml(truncateText(review.review_text, 60))}</td>
            <td>${date}</td>
            <td>
                <button class="delete-btn" onclick="event.stopPropagation(); openDeleteReviewModal(${review.id})">
                    <div class="btn-delete">
                    <i class="fa-solid fa-trash"></i>
                    </div>
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
  const review = lastReviews.find((r) => r.id === id);
  if (!review || !reviewModal) return;

  const date = new Date(review.created_at).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });

  reviewModalTitle.textContent = review.name || "Anonymous";
  reviewModalStars.innerHTML = renderPokeballs(review.rating);
  reviewModalText.textContent = review.review_text;
  reviewModalDate.textContent = date;

  reviewModal.classList.remove("d-none");
}

function closeReviewModal() {
  if (!reviewModal) return;

  reviewModal.classList.add("d-none");
}

document.querySelectorAll("[data-close-modal]").forEach((el) => {
  el.addEventListener("click", closeReviewModal);
});

reviewModal.addEventListener("click", (event) => {
  if (event.target.id === "reviewModal") closeReviewModal();
});

document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") {
    closeReviewModal();
    closeDeleteReviewModal();
  }
});

// =========================================
// Delete Review
// =========================================

function openDeleteReviewModal(id) {
  reviewToDelete = id;
  document.getElementById("deleteReviewModalMessage").classList.add("d-none");
  document.getElementById("deleteReviewModal").classList.remove("d-none");
}

function closeDeleteReviewModal() {
  reviewToDelete = null;
  document.getElementById("deleteReviewModal").classList.add("d-none");
}

async function deleteReview() {
  if (!reviewToDelete) return;

  const confirmButton = document.getElementById("confirmDeleteReview");
  const message = document.getElementById("deleteReviewModalMessage");
  confirmButton.disabled = true;
  message.className = "modal-msg";
  message.textContent = "Deleting review...";

  try {
    const response = await fetch("api/reviews.php", {
      method: "DELETE",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id: reviewToDelete }),
    });

    const data = await response.json();

    if (data.success) {
      closeDeleteReviewModal();
      loadReviews();
    } else {
      throw new Error(data.error || "Failed to delete review.");
    }
  } catch (error) {
    console.error("Delete Error:", error);
    message.className = "modal-msg error";
    message.textContent = error.message || "Failed to delete review.";
  } finally {
    confirmButton.disabled = false;
  }
}

document
  .getElementById("closeDeleteReviewModal")
  .addEventListener("click", closeDeleteReviewModal);
document
  .getElementById("cancelDeleteReview")
  .addEventListener("click", closeDeleteReviewModal);
document
  .getElementById("confirmDeleteReview")
  .addEventListener("click", deleteReview);
document
  .getElementById("deleteReviewModal")
  .addEventListener("click", (event) => {
    if (event.target.id === "deleteReviewModal") closeDeleteReviewModal();
  });

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
  reviewPage = 1;
  loadReviews();
});

document.getElementById("searchReview").addEventListener("input", (e) => {
  clearTimeout(searchDebounce);
  searchDebounce = setTimeout(() => {
    currentSearch = e.target.value;
    reviewPage = 1;
    loadReviews();
  }, 300);
});

document.getElementById("previousReviewPage").addEventListener("click", () => {
  if (reviewPage > 1) {
    reviewPage -= 1;
    renderReviewsTable(lastReviews);
  }
});
document.getElementById("nextReviewPage").addEventListener("click", () => {
  reviewPage += 1;
  renderReviewsTable(lastReviews);
});

// =========================================
// Initial Load
// =========================================

loadReviews();
