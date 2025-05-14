<?php
// ajax/cancel_order.php
session_start();
require_once __DIR__ . '/../database/DBConnection.php';

$pdo     = DBConnect::getInstance()->getConnection();
$user_id = $_SESSION['user_id'] ?? null;
$order_id= intval($_POST['order_id'] ?? 0);

// Chỉ xử lý POST từ form
if (!$user_id || !$order_id) {
  header("Location: /index.php?page=donhang");
  exit;
}

// Kiểm tra quyền & trạng thái
$stmt = $pdo->prepare("
  SELECT status 
  FROM orders 
  WHERE order_id = ? 
    AND user_id = ?
");
$stmt->execute([$order_id, $user_id]);
$status = $stmt->fetchColumn();

if ($status === 'Chờ xác nhận') {
  // Cập nhật sang Đã huỷ
  $up = $pdo->prepare("
    UPDATE orders 
    SET status = 'Đã huỷ' 
    WHERE order_id = ?
  ");
  $up->execute([$order_id]);
}

// Quay về danh sách đơn
header("Location: /index.php?page=donhang");
exit;
