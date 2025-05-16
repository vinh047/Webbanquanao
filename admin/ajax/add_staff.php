<?php
require_once '../../database/DBConnection.php';
header('Content-Type: application/json; charset=utf-8');

// Nhận dữ liệu từ form
$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$status   = isset($_POST['status']) ? (int)$_POST['status'] : 1;

$province       = trim($_POST['province'] ?? '');
$district       = trim($_POST['district'] ?? '');
$ward           = trim($_POST['ward'] ?? '');
$address_detail = trim($_POST['address_detail'] ?? '');

$province_name  = trim($_POST['province_name'] ?? '');
$district_name  = trim($_POST['district_name'] ?? '');
$ward_name      = trim($_POST['ward_name'] ?? '');
$role_id = isset($_POST['role_id']) ? (int)$_POST['role_id'] : 4;

// Validate bắt buộc
if (
    $name === '' || $email === '' || $password === '' || $phone === '' ||
    $province === '' || $district === '' || $ward === '' || $address_detail === ''
) {
    echo json_encode([
        'success' => false,
        'message' => 'Vui lòng cung cấp đầy đủ tất cả các trường thông tin.'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Email không hợp lệ.'
    ]);
    exit;
}

$db = DBConnect::getInstance();

try {
    // Kiểm tra email trùng
    $exists = $db->select("SELECT COUNT(*) as count FROM users WHERE email = ?", [$email]);
    if ($exists[0]['count'] > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Email này đã được sử dụng.'
        ]);
        exit;
    }

    // Hash mật khẩu
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Thêm user vào bảng users
    $ok = $db->execute(
        'INSERT INTO users (name, email, password, phone, status, role_id)
            VALUES (?, ?, ?, ?, ?, ?)',
        [$name, $email, $hashedPassword, $phone, $status, $role_id]
    );

    if (!$ok) {
        echo json_encode([
            'success' => false,
            'message' => 'Không thể thêm nhân viên.'
        ]);
        exit;
    }

    // Lấy user_id vừa thêm
   // Lấy mảng các user_id, sắp xếp tăng dần
    $userIds = $db->select(
        "SELECT user_id 
        FROM users 
        ORDER BY user_id ASC"
    );

    // Nếu bạn chỉ cần ID lớn nhất (giá trị AUTO_INCREMENT gần nhất)
    $row = $db->selectOne(
        "SELECT MAX(user_id) AS user_id 
        FROM users"
    );
    $maxUserId = $row['user_id'];

    $user_id = $maxUserId;

    // Ghép tên tỉnh, quận, phường vào address_detail
    $full_address = trim($address_detail);
    if ($ward_name) $full_address .= ", $ward_name";
    if ($district_name) $full_address .= ", $district_name";
    if ($province_name) $full_address .= ", $province_name";

    // Thêm địa chỉ mặc định cho user vào bảng user_addresses
    $db->execute(
        'INSERT INTO user_addresses (user_id, province, district, ward, address_detail, is_default)
         VALUES (?, ?, ?, ?, ?, 1)',
        [$user_id, $province, $district, $ward, $full_address]
    );

    echo json_encode([
        'success' => true,
        'message' => 'Thêm nhân viên thành công.'
    ]);
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        echo json_encode([
            'success' => false,
            'message' => 'Nhân viên này đã tồn tại.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi hệ thống: ' . $e->getMessage()
        ]);
    }
}
