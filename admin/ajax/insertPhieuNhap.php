<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
header('Content-Type: application/json');

$db = DBConnect::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $supplier_id = $_POST['supplier_id'] ?? null;
        $user_id = $_POST['user_id'] ?? null;
        $products = $_POST['products'] ?? null;

        if (empty($supplier_id) || empty($user_id) || empty($products)) {
            echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu cần thiết!']);
            exit;
        }

        $productList = json_decode($products, true);
        if (!is_array($productList)) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu sản phẩm không hợp lệ']);
            exit;
        }

        $conn = $db->getConnection();
        $conn->beginTransaction();

        // $stmt = $conn->prepare("INSERT INTO importreceipt (supplier_id, user_id, total_price) VALUES (?, ?, 0)");
        $stmt = $conn->prepare("INSERT INTO importreceipt (supplier_id, user_id, total_price, status) VALUES (?, ?, 0, 1)");

        $stmt->execute([$supplier_id, $user_id]);
        $importreceipt_id = $conn->lastInsertId();

        $total_price_all = 0;

        foreach ($productList as $product) {
            $product_id = intval($product['product_id']);
            $color_id = intval($product['color_id']);
            $size_id = intval($product['size_id']);
            $quantity = intval($product['quantity']);
            $unit_price = floatval($product['unit_price']);
        
            // Lấy pttg từ bảng products
            $stmtPT = $conn->prepare("SELECT pttg FROM products WHERE product_id = ?");
            $stmtPT->execute([$product_id]);
            $pttg = $stmtPT->fetchColumn();
            $pttg = $pttg > 0 ? floatval($pttg) : 1;
        
            // Tính giá bán = unit_price × pttg
            $price_sale = $unit_price * (1 + $pttg / 100);        
            // Tính tổng tiền chi tiết
            $total_price = $unit_price * $quantity;
            $total_price_all += $total_price;
        
            // Kiểm tra biến thể
            $stmtVar = $conn->prepare("SELECT variant_id FROM product_variants WHERE product_id = ? AND color_id = ? AND size_id = ?");
            $stmtVar->execute([$product_id, $color_id, $size_id]);
            $variant = $stmtVar->fetch();
        
            // if ($variant) {
            //     $variant_id = $variant['variant_id'];
            //     $stmtUpdateStock = $conn->prepare("UPDATE product_variants SET stock = stock + ? WHERE variant_id = ?");
            //     $stmtUpdateStock->execute([$quantity, $variant_id]);
            // } else {
            //     $stmtNewVar = $conn->prepare("INSERT INTO product_variants (product_id, color_id, size_id, stock) VALUES (?, ?, ?, ?)");
            //     $stmtNewVar->execute([$product_id, $color_id, $size_id, $quantity]);
            //     $variant_id = $conn->lastInsertId();
            // }
            $variant_id = $variant['variant_id'] ?? null;

        
            // Lưu chi tiết phiếu nhập
            $stmtCT = $conn->prepare("INSERT INTO importreceipt_details (importreceipt_id, product_id, variant_id, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtCT->execute([$importreceipt_id, $product_id, $variant_id, $quantity, $unit_price, $total_price]);
        
            // Cập nhật price_sale trong bảng products
            $stmtUpdatePrice = $conn->prepare("UPDATE products SET price_sale = ? WHERE product_id = ?");
            $stmtUpdatePrice->execute([$price_sale, $product_id]);
        }
        

        $stmtTotal = $conn->prepare("UPDATE importreceipt SET total_price = ? WHERE importreceipt_id = ?");
        $stmtTotal->execute([$total_price_all, $importreceipt_id]);

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Lưu phiếu nhập thành công!',
            'importreceipt_id' => $importreceipt_id,
            'total_price' => number_format($total_price_all, 0, ',', '.') . ' VNĐ'
        ]);

    } catch (Exception $e) {
        if ($conn && $conn->inTransaction()) {
            $conn->rollBack();
        }
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Chỉ hỗ trợ POST']);
}
