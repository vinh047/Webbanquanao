<?php
session_start();
require_once __DIR__ . '/../database/DBConnection.php';
header('Content-Type: application/json');

$db = DBConnect::getInstance();
$pdo = $db->getConnection();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$items = $data['items'] ?? [];

if (!is_array($items) || count($items) === 0) {
    echo json_encode(['success' => false, 'message' => 'Không có sản phẩm để xoá']);
    exit;
}

try {
    // Tìm cart_id của người dùng hiện tại
    $stmtCartId = $pdo->prepare("SELECT cart_id FROM cart WHERE user_id = ?");
    $stmtCartId->execute([$user_id]);
    $cart_id = $stmtCartId->fetchColumn();

    if (!$cart_id) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy giỏ hàng']);
        exit;
    }

    // Xoá từng dòng cart_details
    $stmt = $pdo->prepare("DELETE FROM cart_details WHERE cart_id = ? AND product_id = ? AND variant_id = ?");
    foreach ($items as $item) {
        $stmt->execute([
            $cart_id,
            (int)$item['product_id'],
            (int)$item['variant_id']
        ]);
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
