<?php
require_once __DIR__ . '/../database/DBConnection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$address_id = $data['address_id'] ?? null;

if (!$address_id) {
  echo json_encode(['success' => false, 'message' => 'Thiếu mã địa chỉ']);
  exit;
}

$db = DBConnect::getInstance();
$result = $db->execute("DELETE FROM user_addresses WHERE address_id = ?", [$address_id]);

if ($result) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'message' => 'Không thể xoá địa chỉ']);
}
