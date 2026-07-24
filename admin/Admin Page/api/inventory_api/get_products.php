<?php
/* ============================================================
   GET PRODUCTS — admin inventory list
   ------------------------------------------------------------
   NOTE: card descriptions live in products.json, not in this
   table — inventory.js fetches products.json separately and
   merges description in on the client side, matching by id
   ("prod-" + product_id). Nothing here needs to know about it.
   ============================================================ */

session_start();
header('Content-Type: application/json');


require '../../../../config/db_connect.php';

// ---- Single product lookup (used by the Edit button) ----
if (isset($_GET['id'])) {
    $productId = (int) str_replace('prod-', '', $_GET['id']);

    $stmt = $conn->prepare(
        "SELECT product_id, card_name, category, product_type, rarity, `condition`, selling_price, stock_quantity, image
         FROM products WHERE product_id = ?"
    );
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if (!$row) {
        echo json_encode(['success' => true, 'products' => []]);
        exit;
    }

    echo json_encode(['success' => true, 'products' => [formatProduct($row)]]);
    exit;
}

// ---- List with search + filter + pagination ----
$search   = trim($_GET['search'] ?? '');
$filter   = $_GET['filter'] ?? 'all';       // all | in-stock | low-stock | out-of-stock
$sort     = ($_GET['sort'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';
$page     = max(1, (int)($_GET['page'] ?? 1));
$pageSize = max(1, (int)($_GET['pageSize'] ?? 10));
$offset   = ($page - 1) * $pageSize;

$where  = [];
$types  = '';
$params = [];

if ($search !== '') {
    $where[] = "(card_name LIKE ? OR CAST(product_id AS CHAR) LIKE ?)";
    $types  .= 'ss';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

switch ($filter) {
    case 'out-of-stock':
        $where[] = "stock_quantity = 0";
        break;
    case 'low-stock':
        $where[] = "stock_quantity BETWEEN 1 AND 3";
        break;
    case 'in-stock':
        $where[] = "stock_quantity >= 4";
        break;
    // 'all' -> no extra condition
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// ---- Total count (for pagination) ----
$countSql = "SELECT COUNT(*) AS total FROM products $whereSql";
$countStmt = $conn->prepare($countSql);
if ($types !== '') $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$total = (int)$countStmt->get_result()->fetch_assoc()['total'];
$totalPages = max(1, (int)ceil($total / $pageSize));

// ---- Page of results ----
$listSql = "SELECT product_id, card_name, category, product_type, rarity, `condition`, selling_price, stock_quantity, image
            FROM products $whereSql
            ORDER BY product_id $sort
            LIMIT ? OFFSET ?";

$listTypes = $types . 'ii';
$listParams = array_merge($params, [$pageSize, $offset]);

$listStmt = $conn->prepare($listSql);
$listStmt->bind_param($listTypes, ...$listParams);
$listStmt->execute();
$result = $listStmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = formatProduct($row);
}

echo json_encode([
    'success'     => true,
    'products'    => $products,
    'currentPage' => $page,
    'totalPages'  => $totalPages
]);

/* ---- Shared row formatter, so single-lookup and list use identical shape ---- */
function formatProduct($row) {
    return [
        'id'            => 'prod-' . $row['product_id'],
        'productId'     => (int)$row['product_id'],
        'cardName'      => $row['card_name'],
        'category'      => $row['category'],
        'productType'   => $row['product_type'],
        'rarity'        => $row['rarity'],
        'condition'     => $row['condition'],
        'price'         => (float)$row['selling_price'],
        'stockQuantity' => (int)$row['stock_quantity'],
        'image'         => $row['image']
    ];
}
