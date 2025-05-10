<?php
require_once __DIR__ . '/../database/DBConnection.php';
$db = DBConnect::getInstance();

header('Content-Type: application/json');

$id = $_GET['id'] ?? '';
if (!$id || !is_numeric($id)) {
  echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
  exit;
}

$address = $db->selectOne("SELECT * FROM user_addresses WHERE address_id = ?", [$id]);
if (!$address) {
  echo json_encode(['success' => false, 'message' => 'Không tìm thấy địa chỉ']);
  exit;
}

echo json_encode(['success' => true, 'address' => $address]);
