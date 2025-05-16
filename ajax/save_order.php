<?php
session_start();
require_once __DIR__ . '/../database/DBConnection.php';
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$db  = DBConnect::getInstance();
$pdo = $db->getConnection();

// Lấy payload JSON
$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

// Kiểm tra dữ liệu bắt buộc
if (
    !$data ||
    empty($data['cart']) ||
    empty($data['name']) ||
    empty($data['phone'])
) {
    echo json_encode(['success'=>false,'message'=>'Dữ liệu đơn hàng không hợp lệ']);
    exit;
}

try {
    $pdo->beginTransaction();

    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        throw new Exception("Bạn chưa đăng nhập");
    }

    // Ghép địa chỉ
    $addr = $data['address'] ?? [];
    $parts = array_filter([
      $addr['detail']   ?? null,
      $addr['ward']     ?? null,
      $addr['district'] ?? null,
      $addr['province'] ?? null
    ]);
    $shipping_address = implode(', ', $parts);

    $total_price = floatval($data['total_price']);
    $payment_method_id = intval($data['payment_method']);

    // Thêm đơn hàng
    $stmt = $pdo->prepare("
      INSERT INTO orders (
        user_id,
        total_price,
        shipping_address,
        note,
        created_at,
        payment_method_id,
        staff_id
      ) VALUES (
        ?, ?, ?, ?, NOW(), ?, NULL
      )
    ");
    $email = $data['email'] ?? ''; // lấy email từ payload
    $stmt->execute([
      $user_id,
      $total_price,
      $shipping_address,
      $email,
      $payment_method_id
    ]);
    $order_id = $pdo->lastInsertId();

    // Nếu địa chỉ mới thì lưu
    if (empty($addr['saved_id']) && !empty($addr['province'])) {
      $pdo->prepare("
        INSERT INTO user_addresses
          (user_id, address_detail, ward, district, province,
           is_default, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, 0, NOW(), NOW())
      ")->execute([
        $user_id,
        $addr['detail'],
        $addr['ward'],
        $addr['district'],
        $addr['province']
      ]);
    }

    // Thêm chi tiết đơn hàng & cập nhật tồn kho
    $stmtDetail = $pdo->prepare("
      INSERT INTO order_details
        (order_id, product_id, variant_id, price, quantity, total_price)
      VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmtCheckStock = $pdo->prepare("SELECT stock FROM product_variants WHERE variant_id = ?");
    $stmtUpdateStock = $pdo->prepare("
      UPDATE product_variants SET stock = stock - ? WHERE variant_id = ?
    ");
    $stmtUpdateSoldCount = $pdo->prepare("UPDATE products SET sold_count = sold_count + ? WHERE product_id = ?");

    foreach ($data['cart'] as $item) {
      $product_id = intval($item['product_id']);
      $variant_id = intval($item['variant_id']);
      $price      = floatval($item['price']);
      $qty        = intval($item['quantity']);

      // Kiểm tra tồn kho
      $stmtCheckStock->execute([$variant_id]);
      $remaining = intval($stmtCheckStock->fetchColumn());
      if ($remaining < $qty) {
        throw new Exception("Sản phẩm #$variant_id chỉ còn $remaining, bạn cần $qty");
      }

      // Lưu chi tiết
      $stmtDetail->execute([
        $order_id,
        $product_id,
        $variant_id,
        $price,
        $qty,
        $price * $qty
      ]);

      // Trừ kho
      $stmtUpdateStock->execute([$qty, $variant_id]);

      // Tăng số lượng đã bán
        $stmtUpdateSoldCount->execute([$qty, $product_id]);
    }

    $pdo->commit();

    echo json_encode(['success' => true, 'order_id' => $order_id]);
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    file_put_contents(__DIR__.'/log_order_error.txt',
      date('c')." - ".$e->getMessage()."\n", FILE_APPEND);
    echo json_encode(['success'=>false, 'message'=>$e->getMessage()]);
    exit;
}
