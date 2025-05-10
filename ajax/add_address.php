<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
    exit;
}

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

require_once __DIR__ . '/../database/DBConnection.php';
$db = DBConnect::getInstance();

$data = json_decode(file_get_contents('php://input'), true);

$address_detail = trim($data['address_detail'] ?? '');
$province       = trim($data['province'] ?? '');
$district       = trim($data['district'] ?? '');
$ward           = trim($data['ward'] ?? '');
$is_default     = isset($data['is_default']) ? (int)$data['is_default'] : 0;
$user_id        = $_SESSION['user_id'];

// Validate dữ liệu đầu vào
if (!$address_detail || !$province || !$district || !$ward) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin địa chỉ']);
    exit;
}

// Nếu là mặc định thì phải set các địa chỉ khác về 0
if ($is_default === 1) {
    $db->update('user_addresses', ['is_default' => 0], 'user_id = ?', [$user_id]);
}

// Thêm địa chỉ mới
$insertSuccess = $db->insert('user_addresses', [
    'user_id'        => $user_id,
    'address_detail' => $address_detail,
    'province'       => $province,
    'district'       => $district,
    'ward'           => $ward,
    'is_default'     => $is_default,
]);

if ($insertSuccess) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi thêm địa chỉ']);
}
