<?php
require_once '../../database/DBConnection.php';
$db = DBConnect::getInstance();

header('Content-Type: application/json');

// Hàm helper tính trạng thái dựa trên ngày
function calculateStatus(string $startDate, string $endDate): string {
    $today = date('Y-m-d');
    if ($endDate < $today) {
        return 'inactive'; // Hết hạn
    }
    if ($startDate > $today) {
        return 'inactive'; // Chưa hiệu lực (cũng xem như inactive)
    }
    return 'active'; // Hiệu lực
}

// Thêm voucher
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_voucher'])) {
    $code      = trim($_POST['code'] ?? '');
    $discount  = floatval($_POST['discount'] ?? 0);
    $startDate = $_POST['start_date'] ?? '';
    $endDate   = $_POST['end_date'] ?? '';

    // Kiểm tra trùng code
    $exists = $db->select("SELECT * FROM vouchers WHERE code = ?", [$code]);

    // Kiểm tra ngày
    $isInvalidDate = strtotime($endDate) < strtotime($startDate);

    if ($exists) {
        echo json_encode(['success' => false, 'message' => 'Mã voucher đã tồn tại!']);
        exit;
    }
    if ($isInvalidDate) {
        echo json_encode(['success' => false, 'message' => 'Ngày kết thúc phải lớn hơn ngày bắt đầu!']);
        exit;
    }

    // Tính trạng thái
    $status = calculateStatus($startDate, $endDate);

    $db->insert('vouchers', [
        'code'       => $code,
        'discount'   => $discount,
        'start_date' => $startDate,
        'end_date'   => $endDate,
        'status'     => $status
    ]);

    echo json_encode(['success' => true]);
    exit;
}

// Cập nhật voucher
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_voucher'])) {
    $voucherId = intval($_POST['voucher_id'] ?? 0);
    $code      = trim($_POST['code'] ?? '');
    $discount  = floatval($_POST['discount'] ?? 0);
    $startDate = $_POST['start_date'] ?? '';
    $endDate   = $_POST['end_date'] ?? '';

    // Kiểm tra trùng code (trừ chính nó)
    $exists = $db->select("SELECT * FROM vouchers WHERE code = ? AND voucher_id != ?", [$code, $voucherId]);

    // Kiểm tra ngày
    $isInvalidDate = strtotime($endDate) < strtotime($startDate);

    if ($exists) {
        echo json_encode(['success' => false, 'message' => 'Mã voucher đã tồn tại!']);
        exit;
    }
    if ($isInvalidDate) {
        echo json_encode(['success' => false, 'message' => 'Ngày kết thúc phải lớn hơn ngày bắt đầu!']);
        exit;
    }

    // Tính trạng thái
    $status = calculateStatus($startDate, $endDate);

    $db->execute(
        "UPDATE vouchers SET code = ?, discount = ?, start_date = ?, end_date = ?, status = ? WHERE voucher_id = ?",
        [$code, $discount, $startDate, $endDate, $status, $voucherId]
    );

    echo json_encode(['success' => true]);
    exit;
}
//xóa
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $voucher_id = intval($_GET['delete']);
    
    $result = $db->execute("DELETE FROM vouchers WHERE voucher_id = ?", [$voucher_id]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Xóa voucher thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Xóa voucher thất bại']);
    }
    exit;
}




