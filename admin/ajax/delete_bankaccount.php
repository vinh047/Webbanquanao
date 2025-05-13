<?php
require_once '../../database/DBConnection.php';

header('Content-Type: application/json; charset=utf-8');
$db = DBConnect::getInstance();

// 1. Lấy và validate input
$account_id = isset($_POST['account_id']) ? (int) $_POST['account_id'] : 0;
if ($account_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'ID tài khoản không hợp lệ.'
    ]);
    exit;
}

try {
    // 2. Kiểm tra xem tài khoản có phải mặc định không
    $row = $db->selectOne("SELECT is_default FROM bank_account WHERE account_id = ?", [$account_id]);
    if (!$row) {
        echo json_encode([
            'success' => false,
            'message' => 'Tài khoản không tồn tại.'
        ]);
        exit;
    }

    if ((int)$row['is_default'] === 1) {
        echo json_encode([
            'success' => false,
            'message' => 'Không thể xóa tài khoản đang được chọn là mặc định.'
        ]);
        exit;
    }

    // 3. Cập nhật trạng thái (vô hiệu hóa)
    $ok = $db->execute(
        'UPDATE bank_account SET is_active = 0 WHERE account_id = ?',
        [$account_id]
    );

    if ($ok) {
        echo json_encode([
            'success' => true,
            'message' => 'Vô hiệu hóa tài khoản ngân hàng thành công.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy tài khoản hoặc không có gì thay đổi.'
        ]);
    }

} catch (PDOException $e) {
    error_log('DB Error in delete_bankaccount.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống, vui lòng thử lại sau.'
    ]);
}
