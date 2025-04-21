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

        // Tạo phiếu nhập
        $stmt = $conn->prepare("INSERT INTO importreceipt (supplier_id, user_id, total_price) VALUES (?, ?, 0)");
        $stmt->execute([$supplier_id, $user_id]);
        $importreceipt_id = $conn->lastInsertId();

        $total_price_all = 0;

        foreach ($productList as $index => $product) {
            $product_id = intval($product['product_id']);
            $color_id = intval($product['color_id']);
            $size_id = intval($product['size_id']);
            $quantity = intval($product['quantity']);
            $original_image = $product['image_name'] ?? null;

            $uploadDir = __DIR__ . '/../../assets/img/sanpham/';
            $originalName = basename($original_image);
            $targetPath = $uploadDir . $originalName;

            $uniqueName = $originalName;

            // ✅ Nếu file chưa tồn tại → giữ tên, nếu trùng thì tạo tên mới
            if (file_exists($targetPath)) {
                // File đã tồn tại → KHÔNG tạo lại
            } else {
                // Tạo tên mới nếu bị trùng
                $pathInfo = pathinfo($originalName);
                $base = $pathInfo['filename'];
                $ext = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
                $i = 1;
                while (file_exists($uploadDir . $uniqueName)) {
                    $uniqueName = $base . '_' . $i . $ext;
                    $i++;
                }
                $targetPath = $uploadDir . $uniqueName;
            }

            // Kiểm tra biến thể
            $stmtVar = $conn->prepare("SELECT variant_id FROM product_variants WHERE product_id = ? AND color_id = ? AND size_id = ?");
            $stmtVar->execute([$product_id, $color_id, $size_id]);
            $variant = $stmtVar->fetch();

            if ($variant) {
                $variant_id = $variant['variant_id'];
                $stmtUpdateStock = $conn->prepare("UPDATE product_variants SET stock = stock + ? WHERE variant_id = ?");
                $stmtUpdateStock->execute([$quantity, $variant_id]);
            } else {
                // ✅ Lưu ảnh nếu chưa tồn tại
                if (isset($_FILES['images']['tmp_name'][$index]) && $_FILES['images']['tmp_name'][$index]) {
                    $tmpPath = $_FILES['images']['tmp_name'][$index];
                    $fileType = mime_content_type($tmpPath);
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

                    if (!in_array($fileType, $allowedTypes)) {
                        throw new Exception("Ảnh không đúng định dạng (jpg, png, webp)");
                    }

                    if (!file_exists($targetPath)) {
                        if (!move_uploaded_file($tmpPath, $targetPath)) {
                            throw new Exception("Không thể lưu ảnh sản phẩm vào thư mục!");
                        }
                    }
                } else {
                    throw new Exception("Thiếu file ảnh trong request!");
                }

                // Thêm biến thể mới
                $stmtNewVar = $conn->prepare("INSERT INTO product_variants (product_id, color_id, size_id, stock, image) VALUES (?, ?, ?, ?, ?)");
                $stmtNewVar->execute([$product_id, $color_id, $size_id, $quantity, $uniqueName]);
                $variant_id = $conn->lastInsertId();
            }

            // Lấy giá
            $stmtPrice = $conn->prepare("SELECT price FROM products WHERE product_id = ?");
            $stmtPrice->execute([$product_id]);
            $row = $stmtPrice->fetch();
            $unit_price = $row ? floatval($row['price']) : 0;
            $total_price = $unit_price * $quantity;
            $total_price_all += $total_price;

            // Thêm chi tiết phiếu nhập
            $stmtCT = $conn->prepare("INSERT INTO importreceipt_details (importreceipt_id, product_id, variant_id, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtCT->execute([$importreceipt_id, $product_id, $variant_id, $quantity, $unit_price, $total_price]);

            $finalImageNames[] = $uniqueName;

        }

        // Cập nhật tổng tiền
        $stmtTotal = $conn->prepare("UPDATE importreceipt SET total_price = ? WHERE importreceipt_id = ?");
        $stmtTotal->execute([$total_price_all, $importreceipt_id]);

        // $image_names_result = array_map(function ($item) {
        //     return $item['final_image_name'] ?? ''; // bạn cần lưu biến này ở foreach bên trên nếu dùng
        // }, $productList);
        

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Lưu phiếu nhập thành công!',
            'importreceipt_id' => $importreceipt_id,
            'total_price' => number_format($total_price_all, 0, ',', '.') . ' VNĐ',
            'image_names' => $finalImageNames // ✅ đúng mảng tên ảnh thực tế
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