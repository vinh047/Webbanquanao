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

        $stmt = $conn->prepare("INSERT INTO importreceipt (supplier_id, user_id, total_price) VALUES (?, ?, 0)");
        $stmt->execute([$supplier_id, $user_id]);
        $importreceipt_id = $conn->lastInsertId();

        $total_price_all = 0;

        foreach ($productList as $product) {
            $product_id = intval($product['product_id']);
            $color_id = intval($product['color_id']);
            $size_id = intval($product['size_id']);
            $quantity = intval($product['quantity']);

            // Kiểm tra biến thể đã tồn tại
            $stmtVar = $conn->prepare("SELECT variant_id FROM product_variants WHERE product_id = ? AND color_id = ? AND size_id = ?");
            $stmtVar->execute([$product_id, $color_id, $size_id]);
            $variant = $stmtVar->fetch();

            if ($variant) {
                $variant_id = $variant['variant_id'];
                $stmtUpdateStock = $conn->prepare("UPDATE product_variants SET stock = stock + ? WHERE variant_id = ?");
                $stmtUpdateStock->execute([$quantity, $variant_id]);
            } else {
                // Nếu chưa tồn tại → thêm mới
                $image_name = $product['image_name'] ?? null;
                $stmtNewVar = $conn->prepare("INSERT INTO product_variants (product_id, color_id, size_id, stock, image) VALUES (?, ?, ?, ?, ?)");
                $stmtNewVar->execute([$product_id, $color_id, $size_id, $quantity, $image_name]);
                $variant_id = $conn->lastInsertId();
            }

            // Lấy giá nhập của sản phẩm
            $stmtPrice = $conn->prepare("SELECT price FROM products WHERE product_id = ?");
            $stmtPrice->execute([$product_id]);
            $row = $stmtPrice->fetch();
            $unit_price = $row ? floatval($row['price']) : 0;
            $total_price = $unit_price * $quantity;
            $total_price_all += $total_price;

            // ✅ Thêm vào chi tiết phiếu nhập, có unit_price và total_price
            $stmtCT = $conn->prepare("INSERT INTO importreceipt_details (importreceipt_id, product_id, variant_id, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtCT->execute([$importreceipt_id, $product_id, $variant_id, $quantity, $unit_price, $total_price]);
        }

        // Cập nhật tổng tiền phiếu nhập
        $stmtTotal = $conn->prepare("UPDATE importreceipt SET total_price = ? WHERE ImportReceipt_id = ?");
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
?>
