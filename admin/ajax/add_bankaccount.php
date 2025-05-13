<?php
require_once '../../database/DBConnection.php';

$db = DBConnect::getInstance();

header('Content-Type: application/json');

// 1. Lấy dữ liệu từ form
$bankCode       = $_POST['bank_code'] ?? '';
$accountNumber  = $_POST['account_number'] ?? '';
$accountName    = $_POST['account_name'] ?? '';
$isActive       = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;
$isDefault      = isset($_POST['is_default']) ? 1 : 0; // checkbox: nếu có thì là 1

try {
    // 2. Nếu chọn làm mặc định → reset tất cả tài khoản khác về 0
    if ($isDefault) {
        $db->execute("UPDATE bank_account SET is_default = 0");
    }
    if ($is_active === 0 && $is_default === 1) {
        echo json_encode([
            'success' => false,
            'message' => 'Không thể đặt tài khoản ngừng hoạt động làm mặc định.'
        ]);
        exit;
    }
    

    // 3. Thêm tài khoản mới
    $success = $db->execute(
        "INSERT INTO bank_account (bank_code, account_number, account_name, is_active, is_default) 
         VALUES (?, ?, ?, ?, ?)",
        [$bankCode, $accountNumber, $accountName, $isActive, $isDefault]
    );

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Thêm tài khoản ngân hàng thành công.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể thêm tài khoản ngân hàng.']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
