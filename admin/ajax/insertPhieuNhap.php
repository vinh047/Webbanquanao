<?php
require_once('../../database/DBConnection.php');
header('Content-Type: application/json'); // Đảm bảo trả về dữ liệu JSON

$db = DBConnect::getInstance();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $supplier_id = $_POST['supplier_id'];
        $user_id = $_POST['user_id'];
        $total_price = 0;

        // Xử lý các sản phẩm
        $products = $_POST['products'];
        $productList = json_decode($products, true);

        foreach ($productList as $product) {
            $name = $product['name'];
            $description = $product['description'];
            $category_id = $product['category_id'];
            $price = $product['price'];
            $price_sale = $price * 1.2;  // Tính giá bán

            // Lưu sản phẩm vào bảng 'products'
            $sql = "INSERT INTO products (name, description, category_id, price, price_sale) VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->getConnection()->prepare($sql);
            $stmt->execute([$name, $description, $category_id, $price, $price_sale]);  // Thêm price_sale vào đây

            $total_price += $price; // Tính tổng giá trị
        }

        // Lưu phiếu nhập vào bảng 'importreceipt'
        $sql = "INSERT INTO importreceipt (supplier_id, user_id, total_price) VALUES (?, ?, ?)";
        $stmt = $db->getConnection()->prepare($sql);
        $stmt->execute([$supplier_id, $user_id, $total_price]);

        echo json_encode(['success' => true, 'message' => 'Phiếu nhập và sản phẩm đã được lưu thành công!', 'total_price' => number_format($total_price, 0, ',', '.') . ' VNĐ']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Chỉ hỗ trợ POST']);
}
?>
