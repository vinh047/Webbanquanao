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

        foreach ($productList as $index => $product) {
            $product_id = intval($product['product_id']);
            $color_id = intval($product['color_id']);
            $size_id = intval($product['size_id']);
            $quantity = intval($product['quantity']);

            $originalName = isset($_FILES['images']['name'][$index]) ? basename($_FILES['images']['name'][$index]) : null;

            $uniqueName = time() . '_' . $originalName;
            $targetPath = '../../assets/img/sanpham/' . $uniqueName;

            // Kiểm tra xem biến thể đã tồn tại với tên ảnh đã đổi chưa
            $stmtVar = $conn->prepare("SELECT variant_id FROM product_variants WHERE product_id = ? AND color_id = ? AND size_id = ? AND image = ?");
            $stmtVar->execute([$product_id, $color_id, $size_id, $uniqueName]);
            $variant = $stmtVar->fetch();

            if (!$variant) {
                if ($originalName && isset($_FILES['images']['tmp_name'][$index])) {
                    if (move_uploaded_file($_FILES['images']['tmp_name'][$index], $targetPath)) {
                        $stmtNewVar = $conn->prepare("INSERT INTO product_variants (product_id, color_id, size_id, stock, image) VALUES (?, ?, ?, ?, ?)");
                        $stmtNewVar->execute([$product_id, $color_id, $size_id, $quantity, $uniqueName]);
                        $variant_id = $conn->lastInsertId();
                    } else {
                        throw new Exception("Không thể lưu ảnh sản phẩm!");
                    }
                } else {
                    throw new Exception("Thiếu ảnh sản phẩm để tạo biến thể mới!");
                }
            } else {
                $variant_id = $variant['variant_id'];
                $stmtUpdateStock = $conn->prepare("UPDATE product_variants SET stock = stock + ? WHERE variant_id = ?");
                $stmtUpdateStock->execute([$quantity, $variant_id]);
            }

            $stmtPrice = $conn->prepare("SELECT price FROM products WHERE product_id = ?");
            $stmtPrice->execute([$product_id]);
            $row = $stmtPrice->fetch();
            $import_price = $row ? floatval($row['price']) : 0;

            $total_price_all += $quantity * $import_price;

            $stmtCT = $conn->prepare("INSERT INTO importreceipt_details (importreceipt_id, product_id, variant_id, quantity) VALUES (?, ?, ?, ?)");
            $stmtCT->execute([$importreceipt_id, $product_id, $variant_id, $quantity]);
        }

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