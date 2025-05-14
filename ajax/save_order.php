<?php
session_start();
require_once __DIR__ . '/../database/DBConnection.php';
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$db = DBConnect::getInstance();
$pdo = $db->getConnection();

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data || empty($data['cart']) || empty($data['name'])) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu đơn hàng không hợp lệ']);
    exit;
}

try {
    $pdo->beginTransaction();

    $user_id = $_SESSION['user_id'] ?? null;
    $address = $data['address'] ?? [];

    // Ghép địa chỉ thành 1 chuỗi
    $shipping_address = $address['detail'] ?? '';
    if (!empty($address['ward']) || !empty($address['district']) || !empty($address['province'])) {
        $shipping_address = implode(', ', array_filter([
            $address['detail'] ?? '',
            $address['ward'] ?? '',
            $address['district'] ?? '',
            $address['province'] ?? ''
        ]));
    }

    // Thêm đơn hàng
    $stmt = $pdo->prepare("
        INSERT INTO orders (
            user_id, status, total_price, shipping_address, note,
            created_at, payment_method_id, staff_id
        )
        VALUES (?, 'pending', ?, ?, ?, NOW(), ?, NULL)
    ");
    $stmt->execute([
        $user_id,
        floatval($data['total_price']),
        $shipping_address,
        $data['email'] ?? '',
        $data['payment_method']
    ]);
    $order_id = $pdo->lastInsertId();

    // Lưu địa chỉ nếu là địa chỉ mới
    if (empty($address['saved_id']) && !empty($address['province'])) {
        $pdo->prepare("
            INSERT INTO user_addresses 
                (user_id, address_detail, ward, district, province, is_default, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, 0, NOW(), NOW())
        ")->execute([
            $user_id,
            $address['detail'] ?? '',
            $address['ward'] ?? '',
            $address['district'] ?? '',
            $address['province'] ?? ''
        ]);
    }

    // Chuẩn bị câu lệnh
    $stmtDetail = $pdo->prepare("
        INSERT INTO order_details (order_id, product_id, variant_id, price, quantity, total_price)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmtUpdateStock = $pdo->prepare("
        UPDATE product_variants SET stock = stock - ? WHERE variant_id = ?
    ");
    $stmtCheckStock = $pdo->prepare("
        SELECT stock FROM product_variants WHERE variant_id = ?
    ");

    foreach ($data['cart'] as $item) {
        if (!isset($item['product_id'], $item['variant_id'], $item['price'], $item['quantity'])) continue;

        $price = floatval($item['price']);
        $qty = intval($item['quantity']);
        $product_id = intval($item['product_id']);
        $variant_id = intval($item['variant_id']);

        // ✅ Kiểm tra tồn kho trước
        $stmtCheckStock->execute([$variant_id]);
        $currentStock = intval($stmtCheckStock->fetchColumn());

        if ($currentStock < $qty) {
            throw new Exception("Sản phẩm #$variant_id không đủ hàng (còn $currentStock, cần $qty)");
        }

        // Thêm chi tiết
        $stmtDetail->execute([
            $order_id,
            $product_id,
            $variant_id,
            $price,
            $qty,
            $price * $qty
        ]);

        // Trừ tồn kho
        $stmtUpdateStock->execute([
            $qty,
            $variant_id
        ]);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    file_put_contents(__DIR__ . '/log_order_error.txt', $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Lỗi xử lý đơn hàng']);
    exit;
}
