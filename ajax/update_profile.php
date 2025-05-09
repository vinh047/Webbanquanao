<?php
session_start();
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../database/DBConnection.php';
$db = DBConnect::getInstance();

// Đọc input
$data  = json_decode(file_get_contents('php://input'), true);
$name  = trim($data['name']  ?? '');
$phone = trim($data['phone'] ?? '');

// Giữ lại server-side validation tối thiểu
if ($name === '' || $phone === '') {
    echo json_encode(['success'=> false, 'message'=> 'Tên và số điện thoại không được để trống']);
    exit;
}

// Thực hiện UPDATE và kiểm tra có bao nhiêu row bị ảnh hưởng
$success = $db->execute(
    "UPDATE users SET name = ?, phone = ? WHERE user_id = ?",
    [$name, $phone, $_SESSION['user_id']]
  );
  
  if ($success) {
      echo json_encode(['success' => true]);
  } else {
      echo json_encode([
        'success' => false,
        'message' => 'Không có thay đổi nào được lưu'
      ]);
  }
  exit;
  
if ($rows > 0) {
    // Lấy lại bản ghi vừa cập nhật để verify
    $user = $db->selectOne(
        "SELECT name, phone FROM users WHERE user_id = ?",
        [$_SESSION['user_id']]
    );
    echo json_encode([
        'success' => true,
        'updated_rows' => $rows,
        'user' => $user
    ]);
} else {
    // Không có bản ghi nào thay đổi (có thể dữ liệu giống cũ)
    echo json_encode([
        'success' => false,
        'message' => 'Không có thay đổi nào được lưu'
    ]);
}
exit;
