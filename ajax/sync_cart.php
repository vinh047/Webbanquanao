<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../database/DBConnection.php';
header('Content-Type: application/json');
$db = DBConnect::getInstance();

// Ghi log đầu vào để debug nếu cần
$raw_input = file_get_contents("php://input");
file_put_contents("log_cart.txt", $raw_input);

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode($raw_input, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

try {
    // 1. Lấy hoặc tạo giỏ hàng
    $cart = $db->selectOne("SELECT * FROM cart WHERE user_id = ?", [$user_id]);
    if ($cart) {
        $cart_id = $cart['cart_id'];
    } else {
        $db->execute("INSERT INTO cart (user_id, created_at) VALUES (?, NOW())", [$user_id]);
        $cart_id = $db->lastInsertId();
    }

    // 2. Cập nhật hoặc thêm từng sản phẩm từ localStorage
    foreach ($data as $item) {
        $product_id = (int)($item['product_id'] ?? 0);
        $variant_id = (int)($item['variant_id'] ?? 0);
        $quantity = (int)($item['quantity'] ?? 0);

        if ($product_id <= 0 || $variant_id <= 0 || $quantity <= 0) {
            continue;
        }

        // Kiểm tra tồn kho
        $stockRow = $db->selectOne("SELECT stock FROM product_variants WHERE variant_id = ?", [$variant_id]);
        $stock = isset($stockRow['stock']) ? (int)$stockRow['stock'] : 0;

        if ($stock < 1) {
            continue;
        }

        // Kiểm tra xem sản phẩm đã có trong cart chưa
        $existing = $db->selectOne("SELECT cart_detail_id, quantity FROM cart_details WHERE cart_id = ? AND variant_id = ?", [$cart_id, $variant_id]);

        if ($existing) {
            // Nếu tồn tại → cộng dồn số lượng, nhưng không vượt quá tồn kho
            $newQty = min($existing['quantity'] + $quantity, $stock);
            $db->execute("UPDATE cart_details SET quantity = ? WHERE cart_detail_id = ?", [$newQty, $existing['cart_detail_id']]);
        } else {
            // Nếu chưa có → thêm mới nhưng không vượt tồn kho
            $qtyToAdd = min($quantity, $stock);
            $db->execute(
                "INSERT INTO cart_details (cart_id, product_id, variant_id, quantity) VALUES (?, ?, ?, ?)",
                [$cart_id, $product_id, $variant_id, $qtyToAdd]
            );
        }
    }

    echo json_encode(['success' => true, 'message' => 'Đồng bộ thành công']);
} catch (Exception $e) {
    http_response_code(500);
    file_put_contents("log_cart_error.txt", $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi server: ' . $e->getMessage()
    ]);
}
