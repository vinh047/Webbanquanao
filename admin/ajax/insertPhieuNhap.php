<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
header('Content-Type: application/json'); // Đảm bảo trả về dữ liệu JSON

$db = DBConnect::getInstance();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $supplier_id = $_POST['supplier_id'];
        $user_id = $_POST['user_id'];
        $total_price = 0;

        if (empty($supplier_id)) {
            echo json_encode(['success' => false, 'message' => 'supplier_id đang bị rỗng']);
            exit;
        }

        // Xử lý các sản phẩm
        $products = $_POST['products'];
        $productList = json_decode($products, true);
        // file_put_contents("debug_products.json", print_r($productList, true));

        // ✅ Kiểm tra tất cả sản phẩm có cùng supplier_id
        foreach ($productList as $product) {
            if ($product['supplier_id'] != $supplier_id) {
                echo json_encode(['success' => false, 'message' => 'Tồn tại sản phẩm không cùng nhà cung cấp!']);
                exit;
            }
        }

        foreach ($productList as $product) {
            $name = $product['name'];
            $description = $product['description'];
            $category_id = $product['category_id'];
            $price = isset($product['price']) ? floatval($product['price']) : 0;
            $ptgg = isset($product['ptgg']) ? floatval($product['ptgg']) : 0;
            $price_sale = $price * (1 + $ptgg / 100);

            // Lưu sản phẩm vào bảng 'products'
            $sql = "INSERT INTO products (name, description, category_id, price, price_sale, pttg) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $db->getConnection()->prepare($sql);
            $stmt->execute([$name, $description, $category_id, $price, $price_sale, $ptgg]);

            // $total_price += $price; // Tính tổng giá trị
        }

        // Lưu phiếu nhập vào bảng 'importreceipt'
        $sql = "INSERT INTO importreceipt (supplier_id, user_id, total_price) VALUES (?, ?, ?)";
        $stmt = $db->getConnection()->prepare($sql);
        $stmt->execute([$supplier_id, $user_id, $total_price]);

        echo json_encode([
            'success' => true,
            'message' => 'Phiếu nhập và sản phẩm đã được lưu thành công!',
            'total_price' => number_format($total_price, 0, ',', '.') . ' VNĐ'
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Chỉ hỗ trợ POST']);
}
?>
