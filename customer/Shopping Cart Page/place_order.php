<?php
require '../../config/db_connect.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'You must be signed in to place an order.']);
    exit;
}

$userId = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

$firstName = trim($data['firstName'] ?? '');
$lastName = trim($data['lastName'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');
$houseNoStreet = trim($data['houseNumber'] ?? '');
$city = trim($data['city'] ?? '');
$province = trim($data['province'] ?? '');
$zipCode = trim($data['zipCode'] ?? '');
$paymentMethod = strtolower(trim($data['paymentMethod'] ?? 'cod'));

if (!in_array($paymentMethod, ['cod', 'gcash'], true)) {
    $paymentMethod = 'cod';
}

if (
    $firstName === '' || $lastName === '' || $email === '' || $phone === ''
    || $houseNoStreet === '' || $city === '' || $province === '' || $zipCode === ''
) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please fill in all required billing fields.']);
    exit;
}

try {
    $conn->begin_transaction();

    // ---- Pull the user's actual cart + real prices/stock from the DB ----
    // Never trust price/subtotal from the client — always recompute here.
    $cartStmt = $conn->prepare(
        "SELECT ci.cart_item_id, ci.product_id, ci.quantity,
                p.selling_price, p.stock_quantity
         FROM cart_items ci
         JOIN cart c ON ci.cart_id = c.cart_id
         JOIN products p ON ci.product_id = p.product_id
         WHERE c.user_id = ?"
    );
    $cartStmt->bind_param("i", $userId);
    $cartStmt->execute();
    $cartRows = $cartStmt->get_result()->fetch_all(MYSQLI_ASSOC);

    if (empty($cartRows)) {
        throw new Exception('Your cart is empty.');
    }

    // ---- Stock check + total calculation, before writing anything ----
    $shippingFee = 150.00;
    $subtotalAmount = 0;
    foreach ($cartRows as $row) {
        if ((int) $row['quantity'] > (int) $row['stock_quantity']) {
            throw new Exception('Not enough stock for one of the items in your cart.');
        }
        $subtotalAmount += (float) $row['selling_price'] * (int) $row['quantity'];
    }

    $totalAmount = $subtotalAmount + $shippingFee;

    // ---- Insert the order header ----
    $orderStatus = 'Pending';

    $orderStmt = $conn->prepare(
        "INSERT INTO orders
            (user_id, order_date, payment_method, order_status, total_amount,
             house_no_street, city, province, zip_code)
         VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?)"
    );
    $orderStmt->bind_param(
        "issdssss",
        $userId,
        $paymentMethod,
        $orderStatus,
        $totalAmount,
        $houseNoStreet,
        $city,
        $province,
        $zipCode
    );
    $orderStmt->execute();
    $orderId = $orderStmt->insert_id;

    // ---- Save billing info back to the user's profile so checkout ----
    // ---- and account.php stay in sync for their next order ----------
    $profileStmt = $conn->prepare(
        "UPDATE users
         SET first_name = ?, last_name = ?, email = ?, phone = ?,
             address_details = ?, address_city = ?, address_province = ?, address_zip = ?
         WHERE id = ?"
    );
    $profileStmt->bind_param(
        "ssssssssi",
        $firstName,
        $lastName,
        $email,
        $phone,
        $houseNoStreet,
        $city,
        $province,
        $zipCode,
        $userId
    );
    $profileStmt->execute();

    // ---- Insert one order_items row per cart line, and decrement stock ----
    $itemStmt = $conn->prepare(
        "INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stockStmt = $conn->prepare(
        "UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?"
    );

    foreach ($cartRows as $row) {
        $productId = (int) $row['product_id'];
        $quantity = (int) $row['quantity'];
        $unitPrice = (float) $row['selling_price'];
        $subtotal = $unitPrice * $quantity;

        $itemStmt->bind_param("iiidd", $orderId, $productId, $quantity, $unitPrice, $subtotal);
        $itemStmt->execute();

        $stockStmt->bind_param("ii", $quantity, $productId);
        $stockStmt->execute();
    }

    // ---- Empty the cart now that the order has been placed ----
    $clearCart = $conn->prepare(
        "DELETE ci FROM cart_items ci
         JOIN cart c ON ci.cart_id = c.cart_id
         WHERE c.user_id = ?"
    );
    $clearCart->bind_param("i", $userId);
    $clearCart->execute();

    $conn->commit();

    echo json_encode(['success' => true, 'orderId' => $orderId, 'totalAmount' => $totalAmount]);

} catch (Throwable $e) {
    $conn->rollback();
    http_response_code(500);
    // TODO: once this is stable, swap $e->getMessage() for a generic
    // message in production so DB details never reach the client.
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}