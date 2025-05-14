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

    // 1) Ghép địa chỉ thành 1 chuỗi
    $addr = $data['address'] ?? [];
    $parts = array_filter([
      $addr['detail']   ?? null,
      $addr['ward']     ?? null,
      $addr['district'] ?? null,
      $addr['province'] ?? null
    ]);
    $shipping_address = implode(', ', $parts);

    // 2) Thêm đơn hàng, để MySQL dùng DEFAULT status = 'Chờ xác nhận'
    $stmt = $pdo->prepare("
      INSERT INTO orders (
        user_id,
        total_price,
        shipping_address,
        created_at,
        payment_method_id,
        staff_id
      ) VALUES (
        ?, ?, ?, NOW(), ?, NULL
      )
    ");
    $stmt->execute([
      $user_id,
      floatval($data['total_price']),
      $shipping_address,
      intval($data['payment_method'])
    ]);
    $order_id = $pdo->lastInsertId();

    // 3) Nếu là địa chỉ mới thì lưu vào user_addresses
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

    // 4) Chuẩn bị chèn chi tiết và trừ kho
    $stmtDetail     = $pdo->prepare("
      INSERT INTO order_details
        (order_id, product_id, variant_id, price, quantity, total_price)
      VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmtCheckStock = $pdo->prepare("
      SELECT stock FROM product_variants WHERE variant_id = ?
    ");
    $stmtUpdateStock = $pdo->prepare("
      UPDATE product_variants
      SET stock = stock - ?
      WHERE variant_id = ?
    ");

    foreach ($data['cart'] as $item) {
      $product_id = intval($item['product_id']);
      $variant_id = intval($item['variant_id']);
      $price      = floatval($item['price']);
      $qty        = intval($item['quantity']);

      // Kiểm tra tồn kho
      $stmtCheckStock->execute([$variant_id]);
      $remaining = intval($stmtCheckStock->fetchColumn());
      if ($remaining < $qty) {
        throw new Exception("Sản phẩm #{$variant_id} chỉ còn {$remaining}, bạn cần {$qty}");
      }

      // Chèn chi tiết
      $stmtDetail->execute([
        $order_id,
        $product_id,
        $variant_id,
        $price,
        $qty,
        $price * $qty
      ]);

      // Cập nhật trừ kho
      $stmtUpdateStock->execute([$qty, $variant_id]);
    }

    $pdo->commit();

    echo json_encode(['success'=>true]);
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    // Ghi log nếu cần
    file_put_contents(__DIR__.'/log_order_error.txt',
      date('c')." - ".$e->getMessage()."\n", FILE_APPEND);
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    exit;
}
