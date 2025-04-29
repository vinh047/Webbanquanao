<?php
session_start();
require_once '../database/DBConnection.php';

header('Content-Type: application/json');

$db = DBConnect::getInstance();
$conn = $db->getConnection();

// Bắt dữ liệu JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$product_id = $data['product_id'] ?? null;
$color_id = $data['color_id'] ?? null;
$size_name = $data['size_name'] ?? null;
$quantity = $data['quantity'] ?? 1;

// Lấy user_id từ session
$user_id = $_SESSION['user_id'] ?? null;

// Kiểm tra dữ liệu
if (!$user_id || !$product_id || !$color_id || !$size_name || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

try {
    // 1. Kiểm tra user đã có giỏ hàng chưa
    $stmt = $conn->prepare("SELECT cart_id FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart = $stmt->fetch();

    if (!$cart) {
        // Nếu chưa có thì tạo giỏ hàng mới
        $stmt = $conn->prepare("INSERT INTO cart (user_id) VALUES (?)");
        $stmt->execute([$user_id]);
        $cart_id = $conn->lastInsertId();
    } else {
        $cart_id = $cart['cart_id'];
    }

    // 2. Tìm variant_id dựa trên product_id, color_id, size_name
    $stmt = $conn->prepare("
        SELECT pv.variant_id
        FROM product_variants pv
        JOIN sizes s ON s.size_id = pv.size_id
        WHERE pv.product_id = ? AND pv.color_id = ? AND s.name = ? AND pv.is_deleted = 0
        LIMIT 1
    ");
    $stmt->execute([$product_id, $color_id, $size_name]);
    $variant = $stmt->fetch();

    if (!$variant) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy biến thể sản phẩm.']);
        exit;
    }

    $variant_id = $variant['variant_id'];

    // 3. Thêm vào bảng cart_details
    $stmt = $conn->prepare("
        INSERT INTO cart_details (cart_id, product_id, variant_id, quantity)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$cart_id, $product_id, $variant_id, $quantity]);

    echo json_encode(['success' => true, 'message' => 'Đã thêm vào giỏ hàng']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
