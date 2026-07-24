<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../../config/db_connect.php';

$data = json_decode(file_get_contents('php://input'), true) ?: [];
$requestId = (int)($data['request_id'] ?? 0);
$action = trim($data['action'] ?? '');

if ($requestId <= 0 || !in_array($action, ['approve', 'reject'], true)) {
    http_response_code(422); echo json_encode(['success' => false, 'message' => 'Invalid sell request.']); exit;
}

if ($action === 'approve') {
    $productCost = filter_var($data['product_cost'] ?? null, FILTER_VALIDATE_FLOAT);
    $sellingPrice = filter_var($data['selling_price'] ?? null, FILTER_VALIDATE_FLOAT);
    $marketPrice = filter_var($data['market_price'] ?? null, FILTER_VALIDATE_FLOAT);
    if ($productCost === false || $sellingPrice === false || $marketPrice === false || $productCost < 0 || $sellingPrice < 0 || $marketPrice < 0) {
        http_response_code(422); echo json_encode(['success' => false, 'message' => 'Enter valid non-negative prices.']); exit;
    }
}

mysqli_begin_transaction($conn);
try {
    $stmt = mysqli_prepare($conn, 'SELECT request_id, card_name, set_name, category, product_type, rarity, `condition`, stock_quantity, image, notes, status FROM sell_requests WHERE request_id = ? FOR UPDATE');
    mysqli_stmt_bind_param($stmt, 'i', $requestId); mysqli_stmt_execute($stmt);
    $request = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); mysqli_stmt_close($stmt);
    if (!$request) throw new RuntimeException('Sell request not found.');
    if ($request['status'] !== 'Pending') throw new RuntimeException('This sell request has already been reviewed.');

    if ($action === 'reject') {
        $status = 'Rejected'; $stmt = mysqli_prepare($conn, 'UPDATE sell_requests SET status = ? WHERE request_id = ?');
        mysqli_stmt_bind_param($stmt, 'si', $status, $requestId); mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt);
        mysqli_commit($conn); echo json_encode(['success' => true, 'status' => $status, 'product_id' => null]); exit;
    }

    $stmt = mysqli_prepare($conn, 'INSERT INTO products (category, card_name, product_type, set_name, rarity, `condition`, selling_price, market_price, product_cost, stock_quantity, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $quantity = (int)$request['stock_quantity']; $image = $request['image'];
    mysqli_stmt_bind_param($stmt, 'ssssssdddis', $request['category'], $request['card_name'], $request['product_type'], $request['set_name'], $request['rarity'], $request['condition'], $sellingPrice, $marketPrice, $productCost, $quantity, $image);
    mysqli_stmt_execute($stmt); $productId = mysqli_insert_id($conn); mysqli_stmt_close($stmt);

    $status = 'Approved'; $stmt = mysqli_prepare($conn, 'UPDATE sell_requests SET status = ?, product_id = ? WHERE request_id = ?');
    mysqli_stmt_bind_param($stmt, 'sii', $status, $productId, $requestId); mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt);

    addProductToJson($request, $productId, $sellingPrice, $marketPrice);
    mysqli_commit($conn);
    echo json_encode(['success' => true, 'status' => $status, 'product_id' => $productId]);
} catch (Throwable $error) {
    mysqli_rollback($conn); http_response_code(500); echo json_encode(['success' => false, 'message' => $error->getMessage()]);
} finally { mysqli_close($conn); }

function addProductToJson(array $request, int $productId, float $sellingPrice, float $marketPrice): void {
    $path = __DIR__ . '/../../../customer/Shop Page/products.json';
    $file = fopen($path, 'c+');
    if (!$file || !flock($file, LOCK_EX)) throw new RuntimeException('Could not lock products.json.');
    try {
        $contents = stream_get_contents($file); $products = $contents !== '' ? json_decode($contents, true) : [];
        if (!is_array($products)) throw new RuntimeException('products.json contains invalid JSON.');
        $products[] = [
            'id' => 'prod-' . $productId, 'title' => $request['card_name'], 'subtitle' => $request['set_name'],
            'description' => $request['notes'] ?? '', 'image' => $request['image'], 'price' => $sellingPrice,
            'marketPrice' => $marketPrice, 'cardType' => $request['product_type'],
            'rarity' => strtolower(str_replace(' ', '-', $request['rarity'])),
            'condition' => strtolower(str_replace(' ', '-', $request['condition'])),
            'stock' => (string)$request['stock_quantity'], 'category' => $request['category'], 'requirements' => []
        ];
        ftruncate($file, 0); rewind($file);
        fwrite($file, json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); fflush($file);
    } finally { flock($file, LOCK_UN); fclose($file); }
}
