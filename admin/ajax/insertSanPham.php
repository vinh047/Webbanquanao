<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
header('Content-Type: application/json');

try {
    $db = DBConnect::getInstance()->getConnection();

    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $category_id = $_POST['category_id'] ?? null;
    $price = floatval($_POST['price'] ?? 0);
    $ptgg = floatval($_POST['ptgg'] ?? 0);
    $price_sale = $price * (1 + $ptgg / 100);

    if (!$name || !$category_id || $price <= 0) {
        echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu']);
        exit;
    }

    $stmt = $db->prepare("INSERT INTO products (name, description, category_id, price, price_sale, pttg) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $description, $category_id, $price, $price_sale, $ptgg]);

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