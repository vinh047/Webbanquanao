<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
header('Content-Type: application/json');

try {
    $db = DBConnect::getInstance()->getConnection();

    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $category_id = $_POST['category_id'] ?? null;
    $ptgg = floatval($_POST['ptgg'] ?? 0);

    // ✅ Xử lý thiếu dữ liệu
    if (!$name || !$category_id) {
        echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu']);
        exit;
    }

    // ✅ Tạm gán price_sale = 0, bạn sẽ cập nhật sau khi nhập biến thể
    $price_sale = 0;

    $stmt = $db->prepare("INSERT INTO products (name, description, category_id, price_sale, pttg) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $description, $category_id, $price_sale, $ptgg]);

    $product_id = $db->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Thêm thành công',
        'product_id' => $product_id,
        'name' => $name
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

?>