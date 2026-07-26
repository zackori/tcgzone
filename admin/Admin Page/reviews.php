<?php
$pageTitle = "Reviews";
$currentPage = "reviews";

session_start();



?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin | <?= htmlspecialchars($pageTitle) ?></title>

<link rel="stylesheet" href="admin-shared.css?v=<?= filemtime(__DIR__ . '/admin-shared.css') ?>">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="icon" type="image/svg" href="/tcgzone/assets/logos/logo/transparent-image.png">

</head>

<body>

<div class="container">

    <?php include 'includes/sidebar.php'; ?>

    <main class="main">

        <?php include 'includes/header.php'; ?>

        <section class="reviews-cards">

    <div class="card-neutral">
        <div class="card-info">
            <p>Total Reviews</p>
            <h2 id="totalReviews">0</h2>
        </div>

        <div class="card-icon-neutral">
            <img src="/tcgzone/assets/logos/review/poke-open.svg" alt="Pokeball" class="card-icon-img">
        </div>
    </div>

    <div class="rating-summary-card">

        <div class="rating-summary-score">
            <div id="averageRating" class="score-number">0.0</div>
            <div id="scorePokeballs" class="score-pokeballs"></div>
        </div>

        <div id="ratingBreakdown" class="rating-breakdown"></div>

    </div>

</section>

<div class="orders-card">

    <div class="orders-header">

        <h3>All Reviews</h3>

        <div class="orders-actions">

            <input
                type="text"
                id="searchReview"
                placeholder="Search customer or review">

            <select id="ratingFilter">

    <option value="all">All Ratings</option>
    <option value="5">5 Pokeballs</option>
    <option value="4">4 Pokeballs</option>
    <option value="3">3 Pokeballs</option>
    <option value="2">2 Pokeballs</option>
    <option value="1">1 Pokeball</option>

</select>

        </div>

    </div>

    <table class="orders-table">

        <thead>

            <tr>

                <th>ID</th>
                <th>Customer</th>
                <th>Rating</th>
                <th>Review</th>
                <th>Date</th>
                <th>Action</th>

            </tr>

        </thead>

        <tbody id="reviewsTable">

        </tbody>

    </table>

    <div class="pagination" aria-label="Review pagination">

        <button type="button" id="previousReviewPage" aria-label="Previous page">
            <i class="fa-solid fa-angle-left"></i>
        </button>

        <button type="button" id="reviewPage" class="active" aria-current="page">1</button>

        <button type="button" id="nextReviewPage" aria-label="Next page">
            <i class="fa-solid fa-angle-right"></i>
        </button>

    </div>

</div>


    </main>

</div>

<div class="admin-modal-overlay d-none" id="reviewModal" role="dialog" aria-modal="true" aria-labelledby="reviewModalTitle">
    <div class="admin-modal review-detail-modal">
        <div class="admin-modal-header">
            <h3 id="reviewModalTitle"></h3>
            <button type="button" class="admin-modal-close" data-close-modal aria-label="Close">&times;</button>
        </div>
        <div class="admin-modal-body">
            <div id="reviewModalStars" class="review-modal-stars"></div>
            <p id="reviewModalText" class="review-modal-text"></p>
            <p id="reviewModalDate" class="review-modal-date"></p>
        </div>
    </div>
</div>

<div class="admin-modal-overlay d-none" id="deleteReviewModal" role="dialog" aria-modal="true" aria-labelledby="deleteReviewTitle">
    <div class="admin-modal">
        <div class="admin-modal-header">
            <h3 id="deleteReviewTitle">Delete Review</h3>
            <button type="button" class="admin-modal-close" id="closeDeleteReviewModal" aria-label="Close">&times;</button>
        </div>
        <div class="admin-modal-body">
            <p id="deleteReviewMessage">Delete this review? This can't be undone.</p>
            <p class="modal-msg d-none" id="deleteReviewModalMessage"></p>
            <div class="modal-actions">
                <button type="button" class="modal-btn reviews-reject" id="cancelDeleteReview">No</button>
                <button type="button" class="modal-btn reviews-accept" id="confirmDeleteReview">Yes, Delete</button>
            </div>
        </div>
    </div>
</div>

<script src="reviews.js?v=<?= filemtime(__DIR__ . '/reviews.js') ?>"></script>

</body>
</html>
