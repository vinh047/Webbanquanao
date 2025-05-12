<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
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
        $variant_id = $product['variant_id'] ?? null;

        if (!file_exists($imagePath)) continue;

        // Lấy giá nhập
        $stmt0 = $pdo->prepare("SELECT price FROM products WHERE product_id = ?");
        $stmt0->execute([$product_id]);
        $price = $stmt0->fetchColumn();
        if (!$price) $price = 0;

        // Nếu chưa có biến thể → tạo mới
        if (empty($variant_id)) {
            $stmt1 = $pdo->prepare("INSERT INTO product_variants (product_id, image, size_id, stock, color_id) VALUES (?, ?, ?, ?, ?)");
            $stmt1->execute([$product_id, $image, $size_id, $quantity, $color_id]);
            $variant_id = $pdo->lastInsertId();
        } else {
            // Nếu đã có → cộng thêm tồn kho
            $stmt2 = $pdo->prepare("UPDATE product_variants SET stock = stock + ? WHERE variant_id = ?");
            $stmt2->execute([$quantity, $variant_id]);
        }

        // Lưu chi tiết phiếu nhập
        $stmt3 = $pdo->prepare("
            INSERT INTO importreceipt_details 
            (ImportReceipt_id, product_id, variant_id, quantity, unit_price, total_price)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt3->execute([
            $import_receipt_id,
            $product_id,
            $variant_id,
            $quantity,
            $price,
            $quantity * $price
        ]);

        $successCount++;

        
    }

    // Sau khi thêm tất cả chi tiết, cập nhật lại tổng giá trị của phiếu nhập
$stmt4 = $pdo->prepare("
UPDATE importreceipt 
SET total_price = (
    SELECT SUM(total_price) 
    FROM importreceipt_details 
    WHERE importreceipt_id = ?
) 
WHERE importreceipt_id = ?
");
$stmt4->execute([$import_receipt_id, $import_receipt_id]);


    echo json_encode([
        'success' => $successCount > 0,
        'message' => $successCount > 0
            ? "Đã lưu $successCount sản phẩm"
            : "Không lưu được sản phẩm nào"
    ]);
}
?>