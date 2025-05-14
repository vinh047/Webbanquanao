<?php
require_once '../../database/DBConnection.php';
$db = DBConnect::getInstance();

header('Content-Type: application/json');

// Thêm voucher
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_voucher'])) {
    $code      = trim($_POST['code'] ?? '');
    $discount  = floatval($_POST['discount'] ?? 0);
    $startDate = $_POST['start_date'] ?? '';
    $endDate   = $_POST['end_date'] ?? '';
    $status    = $_POST['status'] ?? 'active';

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
    $status    = $_POST['status'] ?? 'inactive';

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

    $db->execute(
        "UPDATE vouchers SET code = ?, discount = ?, start_date = ?, end_date = ?, status = ? WHERE voucher_id = ?",
        [$code, $discount, $startDate, $endDate, $status, $voucherId]
    );

    echo json_encode(['success' => true]);
    exit;
}

// Xoá voucher
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $voucherId = intval($_GET['delete']);
    if ($voucherId > 0) {
        $db->execute("DELETE FROM vouchers WHERE voucher_id = ?", [$voucherId]);
    }
    echo json_encode(['success' => true]);
    exit;
}
