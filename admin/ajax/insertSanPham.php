<?php
require_once('../../database/DBConnection.php');
header('Content-Type: application/json'); // ← Thêm dòng này

$db = DBConnect::getInstance();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Lấy dữ liệu từ form
        $name = $_POST['txtTen'];
        $description = $_POST['txtMota'];
        $category_id = $_POST['cbLoai'];
        $price = $_POST['txtGia'];

        // Thêm sản phẩm vào cơ sở dữ liệu
        $sql = "INSERT INTO products (name, description, category_id, price) VALUES (?, ?, ?, ?)";
        $stmt = $db->getConnection()->prepare($sql);
        $stmt->execute([$name, $description, $category_id, $price]);

        // Lấy ID sản phẩm vừa thêm
        $product_id = $db->getConnection()->lastInsertId();

        // Lấy tên loại sản phẩm
        $category = $db->select("SELECT name FROM categories WHERE category_id = ?", [$category_id]);
        $category_name = $category[0]['name'];

        // Trả về dữ liệu sản phẩm vừa thêm
        echo json_encode([
            'success' => true,
            'product_id' => $product_id,
            'name' => $name,
            'category_name' => $category_name,
            'description' => $description,
            'price' => number_format($price, 0, ',', '.') . ' VNĐ'
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Chỉ hỗ trợ POST'
    ]);
}
?>
