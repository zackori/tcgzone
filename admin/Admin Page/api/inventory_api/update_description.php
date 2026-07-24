<?php
/* Updates the customer-facing description and detail rows in products.json. */
session_start();
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true) ?: [];
$productId = trim($data['productId'] ?? '');
$description = trim($data['description'] ?? '');
$requirements = $data['requirements'] ?? [];
$productMeta = is_array($data['product'] ?? null) ? $data['product'] : [];

if ($productId === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing product ID.']);
    exit;
}

if (!is_array($requirements)) {
    $requirements = [];
}

$requirements = array_values(array_filter(array_map(function ($item) {
    if (!is_array($item)) return null;
    $label = trim($item['label'] ?? '');
    $value = trim($item['value'] ?? '');
    return ($label === '' && $value === '') ? null : ['label' => $label, 'value' => $value];
}, $requirements)));

$jsonPath = __DIR__ . '/../../../../customer/Shop Page/products.json';
$file = fopen($jsonPath, 'c+');
if (!$file || !flock($file, LOCK_EX)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not lock products.json.']);
    exit;
}

try {
    $contents = stream_get_contents($file);
    $products = $contents !== '' ? json_decode($contents, true) : [];
    if (!is_array($products)) throw new RuntimeException('products.json contains invalid JSON.');

    $found = false;
    foreach ($products as &$product) {
        if (($product['id'] ?? '') === $productId) {
            $product['description'] = $description;
            $product['requirements'] = $requirements;
            $product['cardType'] = trim($productMeta['productType'] ?? $product['cardType'] ?? 'Cards');
            $product['rarity'] = trim($productMeta['rarity'] ?? $product['rarity'] ?? '');
            $product['condition'] = trim($productMeta['condition'] ?? $product['condition'] ?? '');
            $found = true;
            break;
        }
    }
    unset($product);

    if (!$found) {
        /* Used only for a product created manually from Inventory. Seller approvals
           always create their complete entry in review-sell-request.php. */
        $products[] = [
            'id' => $productId,
            'title' => trim($productMeta['title'] ?? ''),
            'subtitle' => trim($productMeta['subtitle'] ?? ''),
            'description' => $description,
            'image' => '',
            'price' => (float)($productMeta['price'] ?? 0),
            'marketPrice' => 0,
            'cardType' => trim($productMeta['productType'] ?? 'Cards'),
            'rarity' => trim($productMeta['rarity'] ?? ''),
            'condition' => trim($productMeta['condition'] ?? ''),
            'stock' => (string)($productMeta['stock'] ?? 0),
            'category' => trim($productMeta['category'] ?? ''),
            'requirements' => $requirements
        ];
    }

    ftruncate($file, 0);
    rewind($file);
    fwrite($file, json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    fflush($file);
    echo json_encode(['success' => true]);
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $error->getMessage()]);
} finally {
    flock($file, LOCK_UN);
    fclose($file);
}
