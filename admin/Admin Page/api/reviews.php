<?php

session_start();

header('Content-Type: application/json');


include("../../../config/db_connect.php");

$method = $_SERVER['REQUEST_METHOD'];

/* ============================================================
   DELETE — remove a review
   ============================================================ */

if ($method === 'DELETE') {

    $input = json_decode(file_get_contents("php://input"), true);
    $id = isset($input['id']) ? (int)$input['id'] : 0;

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid review ID"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    echo json_encode(["success" => true, "deleted" => $id]);
    exit;

}

/* ============================================================
   GET — list reviews (with optional filter/search) + stats
   ============================================================ */

$ratingFilter = isset($_GET['rating']) && $_GET['rating'] !== 'all' ? (int)$_GET['rating'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT id, name, rating, review_text, created_at FROM reviews WHERE 1=1";
$params = [];
$types = '';

if ($ratingFilter >= 1 && $ratingFilter <= 5) {
    $sql .= " AND rating = ?";
    $params[] = $ratingFilter;
    $types .= 'i';
}

if ($search !== '') {
    $sql .= " AND (name LIKE ? OR review_text LIKE ?)";
    $likeSearch = "%" . $search . "%";
    $params[] = $likeSearch;
    $params[] = $likeSearch;
    $types .= 'ss';
}

$sql .= " ORDER BY created_at DESC, id DESC";

$stmt = $conn->prepare($sql);

if ($params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$reviews = $result->fetch_all(MYSQLI_ASSOC);

/* Overall stats are always computed from the FULL table,
   not the filtered list, so the cards don't jump around
   when the admin applies a search/filter. */

$statsSql = "SELECT COUNT(*) total, AVG(rating) avgRating FROM reviews";
$statsResult = $conn->query($statsSql);
$statsRow = $statsResult->fetch_assoc();

/* Count of reviews at each rating level (5 down to 1),
   used to draw the distribution bar chart. */

$breakdown = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

$breakdownResult = $conn->query("SELECT rating, COUNT(*) cnt FROM reviews GROUP BY rating");
while ($row = $breakdownResult->fetch_assoc()) {
    $r = (int)$row['rating'];
    if (isset($breakdown[$r])) {
        $breakdown[$r] = (int)$row['cnt'];
    }
}

$response = [
    "reviews" => $reviews,
    "stats" => [
        "total"     => (int)$statsRow['total'],
        "average"   => $statsRow['avgRating'] ? round(($statsRow['avgRating'] / 5) * 5, 1) : 0,
        "breakdown" => $breakdown
    ]
];

echo json_encode($response);

?>