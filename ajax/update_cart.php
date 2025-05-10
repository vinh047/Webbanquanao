<?php
require_once '../database/DBConnection.php';
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

$db = DBConnect::getInstance();
$user_id = $_SESSION['user_id'];

$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'] ?? '';
$variant_id = $data['variant_id'] ?? null;

if (!$variant_id) {
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu']);
    exit;
}

// Lấy cart_id theo user
$cart = $db->selectOne("SELECT cart_id FROM cart WHERE user_id = ?", [$user_id]);

if (!$cart) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy giỏ hàng']);
    exit;
}

$cart_id = $cart['cart_id'];
$affected = 0;

switch ($action) {
    case 'increase':
        $affected = $db->execute("
            UPDATE cart_details 
            SET quantity = quantity + 1 
            WHERE cart_id = ? AND variant_id = ?", 
            [$cart_id, $variant_id]
        );
        break;

    case 'decrease':
        $affected = $db->execute("
            UPDATE cart_details 
            SET quantity = quantity - 1 
            WHERE cart_id = ? AND variant_id = ? AND quantity > 1", 
            [$cart_id, $variant_id]
        );
        break;

    case 'remove':
        $affected = $db->execute("
            DELETE FROM cart_details 
            WHERE cart_id = ? AND variant_id = ?", 
            [$cart_id, $variant_id]
        );
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
        exit;
}

// Trả kết quả
if ($affected > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Không có bản ghi nào bị ảnh hưởng']);
}
