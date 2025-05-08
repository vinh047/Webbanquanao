<?php
session_start();
require_once '../database/DBConnection.php';
$db = DBConnect::getInstance();

header('Content-Type: application/json');

// Ghi log đầu vào (tùy chọn để debug)
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
    if (!$cart) {
        $db->execute("INSERT INTO cart(user_id, created_at) VALUES (?, NOW())", [$user_id]);
        $cart_id = $db->lastInsertId();
    } else {
        $cart_id = $cart['cart_id'];
    }

    // 2. Lấy danh sách variant_id từ client
    $variantIdsFromClient = array_map(function ($item) {
        return (int)($item['variant_id'] ?? 0);
    }, $data);
    $variantIdsFromClient = array_filter($variantIdsFromClient);

    // 3. Xoá các bản ghi không còn tồn tại trong localStorage
    if (count($variantIdsFromClient) > 0) {
        $placeholders = implode(',', array_fill(0, count($variantIdsFromClient), '?'));
        $params = array_merge([$cart_id], $variantIdsFromClient);
        $sql = "DELETE FROM cart_details WHERE cart_id = ? AND variant_id NOT IN ($placeholders)";
        $db->execute($sql, $params);
    } else {
        // Nếu localStorage rỗng, xóa toàn bộ giỏ hàng
        $db->execute("DELETE FROM cart_details WHERE cart_id = ?", [$cart_id]);
    }

    // 4. Duyệt từng sản phẩm để thêm hoặc cập nhật
    foreach ($data as $item) {
        $product_id = (int)($item['id'] ?? 0);
        $variant_id = (int)($item['variant_id'] ?? 0);
        $quantity = (int)($item['quantity'] ?? 0);

        if ($product_id <= 0 || $variant_id <= 0 || $quantity <= 0) {
            continue; // Bỏ qua dữ liệu không hợp lệ
        }

        $existing = $db->selectOne(
            "SELECT * FROM cart_details WHERE cart_id = ? AND variant_id = ?",
            [$cart_id, $variant_id]
        );

        if ($existing) {
            // Cập nhật số lượng mới
            $db->execute(
                "UPDATE cart_details SET quantity = ? WHERE cart_detail_id = ?",
                [$quantity, $existing['cart_detail_id']]
            );
        } else {
            // Thêm mới vào giỏ hàng
            $db->execute(
                "INSERT INTO cart_details(cart_id, product_id, variant_id, quantity) VALUES (?, ?, ?, ?)",
                [$cart_id, $product_id, $variant_id, $quantity]
            );
        }
    }

    echo json_encode(['success' => true, 'message' => 'Đồng bộ thành công']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi server: ' . $e->getMessage()
    ]);
}
