<?php
require_once '../../database/DBConnection.php';

// Trả về JSON và charset UTF-8
header('Content-Type: application/json; charset=utf-8');

// 1. Lấy và trim dữ liệu từ form
$bank_code      = trim($_POST['bank_code']      ?? '');
$account_number = trim($_POST['account_number'] ?? '');
$account_name   = trim($_POST['account_name']   ?? '');
$is_active      = isset($_POST['is_active']) 
                  ? (int) $_POST['is_active'] 
                  : 1;

// 2. Validate bắt buộc
if ($bank_code === '' || $account_number === '' || $account_name === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Vui lòng cung cấp đầy đủ Mã ngân hàng, Số tài khoản và Chủ tài khoản.'
    ]);
    exit;
}

$db = DBConnect::getInstance();

try {
    // 3. Thực thi INSERT với đúng thứ tự tham số
    $ok = $db->execute(
        'INSERT INTO bank_account (bank_code, account_number, account_name, is_active)
         VALUES (?, ?, ?, ?)',
        [
            $bank_code,
            $account_number,
            $account_name,    // <-- trước là nhầm thành $account_number
            $is_active
        ]
    );

    if ($ok) {
        echo json_encode([
            'success' => true,
            'message' => 'Thêm tài khoản ngân hàng thành công.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Không thể thêm tài khoản ngân hàng.'
        ]);
    }
} catch (PDOException $e) {
    // 4. Bắt duplicate key (SQLSTATE 23000) nếu đã tồn tại
    if ($e->getCode() === '23000') {
        echo json_encode([
            'success' => false,
            'message' => 'Tài khoản ngân hàng này đã tồn tại.'
        ]);
    } else {
        // Log lỗi server, trả về message chung
        error_log('DB Error in add_bankaccount: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi hệ thống. Vui lòng thử lại sau.'
        ]);
    }
}
