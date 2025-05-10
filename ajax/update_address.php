<?php
require_once __DIR__ . '/../database/DBConnection.php';
session_start(); // BẮT BUỘC để dùng $_SESSION

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
  echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
  exit;
}

$address_id     = $data['address_id']     ?? null;
$address_detail = $data['address_detail'] ?? '';
$province       = $data['province']       ?? '';
$district       = $data['district']       ?? '';
$ward           = $data['ward']           ?? '';
$is_default     = $data['is_default']     ?? 0;

if (!$address_id || !$address_detail || !$province || !$district || !$ward) {
  echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
  exit;
}

$db = DBConnect::getInstance();
$conn = $db->getConnection();

try {
  $conn->beginTransaction();

  // ✅ Kiểm tra quyền sở hữu địa chỉ
  $stmt = $conn->prepare("SELECT user_id FROM user_addresses WHERE address_id = ?");
  $stmt->execute([$address_id]);
  $owner = $stmt->fetchColumn();

  if (!$owner || $owner != $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'Không có quyền sửa địa chỉ này']);
    exit;
  }

  // ✅ Nếu là mặc định → cập nhật các địa chỉ khác về 0
  if ($is_default) {
    $stmt = $conn->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?");
    $stmt->execute([$owner]);
  }

  // ✅ Cập nhật địa chỉ
  $stmt = $conn->prepare("
    UPDATE user_addresses
    SET address_detail = ?, province = ?, district = ?, ward = ?, is_default = ?, updated_at = NOW()
    WHERE address_id = ?
  ");
  $stmt->execute([$address_detail, $province, $district, $ward, $is_default, $address_id]);

  $conn->commit();
  echo json_encode(['success' => true]);
} catch (Exception $e) {
  $conn->rollBack();
  echo json_encode(['success' => false, 'message' => 'Lỗi máy chủ: ' . $e->getMessage()]);
}
