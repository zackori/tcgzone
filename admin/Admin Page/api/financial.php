<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/../../../config/db_connect.php";

$summaryQuery = "SELECT COALESCE(SUM(total_amount), 0) AS revenue, COUNT(*) AS completed_orders, COALESCE(AVG(total_amount), 0) AS average_order, COALESCE(MAX(total_amount), 0) AS largest_order, COALESCE(MIN(total_amount), 0) AS smallest_order FROM orders WHERE order_status = 'Delivered'";
$summary = mysqli_fetch_assoc(mysqli_query($conn, $summaryQuery));

$sellCostQuery = "
    SELECT COALESCE(SUM(COALESCE(p.product_cost, sr.selling_price) * sr.stock_quantity), 0) AS total
    FROM sell_requests sr
    LEFT JOIN products p ON sr.product_id = p.product_id
    WHERE sr.status = 'Approved'
";
$sellRequestCost = (float) mysqli_fetch_assoc(mysqli_query($conn, $sellCostQuery))['total'];

$procurementCostQuery = "SELECT purchase.procurement_order_id, COALESCE(SUM(item.subtotal), 0) AS item_total, s.supplier_name FROM procurement_orders purchase LEFT JOIN procurement_order_items item ON item.procurement_order_id = purchase.procurement_order_id LEFT JOIN suppliers s ON s.supplier_id = purchase.supplier_id WHERE purchase.order_status = 'Delivered' GROUP BY purchase.procurement_order_id, s.supplier_name";
$procurementCostResult = mysqli_query($conn, $procurementCostQuery);
$procurementCost = 0.0;
while ($row = mysqli_fetch_assoc($procurementCostResult)) {
    $itemTotal = (float) ($row['item_total'] ?? 0);
    $supplierName = trim((string) ($row['supplier_name'] ?? ''));
    $shippingFee = strtolower($supplierName) === 'tcgzone' ? 0.0 : 200.0;
    $procurementCost += $itemTotal + $shippingFee;
}

$revenueTotal = (float) $summary['revenue'];
$productCost = $sellRequestCost + $procurementCost;
$grossProfit = $revenueTotal - $productCost;
$profitMargin = $revenueTotal > 0 ? ($grossProfit / $revenueTotal) * 100 : 0;

$monthly = [];
$monthlyQuery = "SELECT YEAR(order_date) AS year, MONTH(order_date) AS month, COUNT(*) AS orders, COALESCE(SUM(total_amount), 0) AS revenue FROM orders WHERE order_status = 'Delivered' GROUP BY YEAR(order_date), MONTH(order_date) ORDER BY year, month";
$result = mysqli_query($conn, $monthlyQuery);
while ($row = mysqli_fetch_assoc($result)) {
    $year = (string) $row['year'];
    if (!isset($monthly[$year]))
        $monthly[$year] = array_fill(0, 12, ['orders' => 0, 'revenue' => 0.0]);
    $monthly[$year][(int) $row['month'] - 1] = ['orders' => (int) $row['orders'], 'revenue' => (float) $row['revenue']];
}

$years = array_keys($monthly);
rsort($years, SORT_NUMERIC);
$selectedYear = $years[0] ?? (string) date('Y');
if (!isset($monthly[$selectedYear]))
    $monthly[$selectedYear] = array_fill(0, 12, ['orders' => 0, 'revenue' => 0.0]);

$highestMonth = ['label' => '—', 'revenue' => 0.0];
foreach ($monthly as $year => $months)
    foreach ($months as $index => $month) {
        if ($month['revenue'] > $highestMonth['revenue']) {
            $highestMonth = ['label' => date('F', mktime(0, 0, 0, $index + 1, 1)) . ' ' . $year, 'revenue' => $month['revenue']];
        }
    }

$revenueData = array_map(fn($month) => $month['revenue'], $monthly[$selectedYear]);
$orderData = array_map(fn($month) => $month['orders'], $monthly[$selectedYear]);
$monthlyCosts = array_fill(0, 12, 0.0);

$sellMonthlyCostQuery = "
    SELECT YEAR(sr.created_at) AS year, MONTH(sr.created_at) AS month,
           COALESCE(SUM(COALESCE(p.product_cost, sr.selling_price) * sr.stock_quantity), 0) AS cost
    FROM sell_requests sr
    LEFT JOIN products p ON sr.product_id = p.product_id
    WHERE sr.status = 'Approved'
    GROUP BY YEAR(sr.created_at), MONTH(sr.created_at)
";
$sellMonthlyCosts = mysqli_query($conn, $sellMonthlyCostQuery);
while ($row = mysqli_fetch_assoc($sellMonthlyCosts)) {
    if ((string) $row['year'] === (string) $selectedYear)
        $monthlyCosts[(int) $row['month'] - 1] += (float) $row['cost'];
}

$procurementMonthlyCostQuery = "SELECT YEAR(purchase.order_date) AS year, MONTH(purchase.order_date) AS month, COALESCE(SUM(item.subtotal), 0) AS item_total, s.supplier_name FROM procurement_orders purchase LEFT JOIN procurement_order_items item ON item.procurement_order_id = purchase.procurement_order_id LEFT JOIN suppliers s ON s.supplier_id = purchase.supplier_id WHERE purchase.order_status = 'Delivered' GROUP BY YEAR(purchase.order_date), MONTH(purchase.order_date), purchase.procurement_order_id, s.supplier_name";
$procurementMonthlyCosts = mysqli_query($conn, $procurementMonthlyCostQuery);
while ($row = mysqli_fetch_assoc($procurementMonthlyCosts)) {
    if ((string) $row['year'] === (string) $selectedYear) {
        $itemTotal = (float) ($row['item_total'] ?? 0);
        $supplierName = trim((string) ($row['supplier_name'] ?? ''));
        $shippingFee = strtolower($supplierName) === 'tcgzone' ? 0.0 : 200.0;
        $monthlyCosts[(int) $row['month'] - 1] += $itemTotal + $shippingFee;
    }
}

$profitData = array_map(fn($revenue, $cost) => $revenue - $cost, $revenueData, $monthlyCosts);

echo json_encode([
    'cards' => ['revenue' => $revenueTotal, 'gross_profit' => $grossProfit, 'average' => (float) $summary['average_order'], 'completed' => (int) $summary['completed_orders'], 'orders' => (int) $summary['completed_orders']],
    'revenue' => $revenueData,
    'profit' => $profitData,
    'orders' => $orderData,
    'details' => [
        'revenue' => ['total' => $revenueTotal, 'completed_orders' => (int) $summary['completed_orders'], 'average_order' => (float) $summary['average_order'], 'highest_month' => $highestMonth, 'years' => $years, 'monthly' => $monthly],
        'profit' => ['revenue' => $revenueTotal, 'product_cost' => $productCost, 'gross_profit' => $grossProfit, 'margin' => $profitMargin],
        'average' => ['average_order' => (float) $summary['average_order'], 'revenue' => $revenueTotal, 'completed_orders' => (int) $summary['completed_orders'], 'largest_order' => (float) $summary['largest_order'], 'smallest_order' => (float) $summary['smallest_order']]
    ]
]);
mysqli_close($conn);
