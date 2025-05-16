<?php
require_once '../../database/DBConnection.php';
header('Content-Type: application/json; charset=utf-8');

$db = DBConnect::getInstance();

// Lấy thông tin từ POST
$user_id  = $_POST['user_id'] ?? '';
$name     = trim($_POST['name'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$status   = isset($_POST['status']) ? (int)$_POST['status'] : 1;
$password = trim($_POST['password'] ?? '');

$province       = trim($_POST['province'] ?? '');
$district       = trim($_POST['district'] ?? '');
$ward           = trim($_POST['ward'] ?? '');
$address_detail = trim($_POST['address_detail'] ?? '');
$role_id  = isset($_POST['role_id']) ? (int)$_POST['role_id'] : null;

$province_name  = trim($_POST['province_name'] ?? '');
$district_name  = trim($_POST['district_name'] ?? '');
$ward_name      = trim($_POST['ward_name'] ?? '');

if ($user_id === '' || $name === '' || $phone === '' || $province === '' || $district === '' || $ward === '' || $address_detail === '' || $role_id === null) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin.']);
    exit;
}

try {
    // Nếu có mật khẩu thì cập nhật
    if ($password !== '') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $db->execute("UPDATE users SET name = ?, password = ?, phone = ?, status = ?, role_id = ? WHERE user_id = ?", [
            $name, $hashedPassword, $phone, $status, $role_id, $user_id
        ]);
    } else {
        $db->execute("UPDATE users SET name = ?, phone = ?, status = ?, role_id = ? WHERE user_id = ?", [
            $name, $phone, $status,$role_id, $user_id
        ]);
    }

    // Ghép tên tỉnh, quận, phường vào address_detail
    $full_address = trim($address_detail);
    if ($ward_name) $full_address .= ", $ward_name";
    if ($district_name) $full_address .= ", $district_name";
    if ($province_name) $full_address .= ", $province_name";

    // Cập nhật địa chỉ
    $exists = $db->select("SELECT COUNT(*) as total FROM user_addresses WHERE user_id = ?", [$user_id]);
    if ($exists[0]['total'] > 0) {
        $db->execute("UPDATE user_addresses SET province = ?, district = ?, ward = ?, address_detail = ? WHERE user_id = ?", [
            $province, $district, $ward, $full_address, $user_id
        ]);
    } else {
        $db->execute("INSERT INTO user_addresses (user_id, province, district, ward, address_detail, is_default) VALUES (?, ?, ?, ?, ?, 1)", [
            $user_id, $province, $district, $ward, $full_address
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Cập nhật nhân viên thành công.']);
} catch (Exception $e) {
    error_log("Lỗi cập nhật nhân viên: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>