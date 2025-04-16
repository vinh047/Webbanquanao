<?php
require_once('../../database/DBConnection.php');
header('Content-Type: application/json');

$pdo = DBConnect::getInstance()->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $products = $_POST['products'] ?? [];

    if (empty($products)) {
        echo json_encode(['success' => false, 'message' => 'Không có sản phẩm nào được gửi']);
        exit;
    }

    $successCount = 0;

    foreach ($products as $jsonProduct) {
        $product = json_decode($jsonProduct, true);
        if (!$product) continue;

        $product_id = $product['product_id'];
        $import_receipt_id = $product['import_receipt_id'];
        $size_id = $product['size_id'];
        $color_id = $product['color_id'];
        $quantity = intval($product['quantity']);
        $image = basename($product['image']);
        $imagePath = "../../assets/img/sanpham/" . $image;

        if (!file_exists($imagePath)) continue;

        // Lấy giá sản phẩm
        $stmt0 = $pdo->prepare("SELECT price FROM products WHERE product_id = ?");
        $stmt0->execute([$product_id]);
        $price = $stmt0->fetchColumn();
        if (!$price) $price = 0;

        $total_price = $price * $quantity;

        // Thêm vào bảng importreceipt_details
        $stmtInsert = $pdo->prepare("INSERT INTO importreceipt_details (ImportReceipt_id, product_id, total_price) VALUES (?, ?, ?)");
        $stmtInsert->execute([$import_receipt_id, $product_id, $total_price]);
        $import_detail_id = $pdo->lastInsertId();

        // Thêm vào bảng product_variants
        $stmt2 = $pdo->prepare("INSERT INTO product_variants 
            (product_id, image, size_id, stock, color_id, importreceipt_details_id) 
            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt2->execute([
            $product_id,
            $image,
            $size_id,
            $quantity,
            $color_id,
            $import_detail_id
        ]);

        $successCount++;
    }

    if ($successCount > 0) {
        echo json_encode([
            'success' => true,
            'message' => "Đã lưu $successCount sản phẩm"
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => "Không lưu được sản phẩm nào"
        ]);
    }
}
