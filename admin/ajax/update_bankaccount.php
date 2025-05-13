<?php
require_once '../../database/DBConnection.php';

header('Content-Type: application/json; charset=utf-8');
$db = DBConnect::getInstance();

// 1. Lấy và trim dữ liệu
$account_id     = isset($_POST['account_id'])     ? (int) $_POST['account_id']     : 0;
$bank_code      = trim($_POST['bank_code']      ?? '');
$account_number = trim($_POST['account_number'] ?? '');
$account_name   = trim($_POST['account_name']   ?? '');
$is_active      = isset($_POST['is_active'])     ? (int) $_POST['is_active']     : 1;
$is_default     = isset($_POST['is_default'])    ? 1 : 0; // checkbox: 1 nếu được chọn

// 2. Validate
if ($account_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID tài khoản không hợp lệ.']);
    exit;
}
if ($bank_code === '' || $account_number === '' || $account_name === '') {
    echo json_encode(['success' => false, 'message' => 'Vui lòng cung cấp Mã ngân hàng, Số tài khoản và Chủ tài khoản.']);
    exit;
}

try {
    // 3. Nếu chọn làm mặc định → reset các tài khoản khác
    if ($is_default) {
        $db->execute("UPDATE bank_account SET is_default = 0 WHERE account_id != ?", [$account_id]);
    }
    if ($is_active === 0 && $is_default === 1) {
        echo json_encode([
            'success' => false,
            'message' => 'Không thể đặt tài khoản ngừng hoạt động làm mặc định.'
        ]);
        exit;
    }
    

    // 4. Cập nhật tài khoản
    $ok = $db->execute(
        'UPDATE bank_account
         SET bank_code      = ?,
             account_number = ?,
             account_name   = ?,
             is_active      = ?,
             is_default     = ?
         WHERE account_id   = ?',
        [
            $bank_code,
            $account_number,
            $account_name,
            $is_active,
            $is_default,
            $account_id
        ]
    );

    if ($ok) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật thông tin tài khoản thành công.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy tài khoản hoặc không có gì thay đổi.']);
    }

} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        echo json_encode(['success' => false, 'message' => 'Tài khoản ngân hàng này đã tồn tại.']);
    } else {
        error_log('DB Error in update_bankaccount.php: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống. Vui lòng thử lại sau.']);
    }
}
