<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
require_once(__DIR__ . '/../../layout/phantrang.php');
require_once('functionLoc.php');
$db = DBConnect::getInstance();
$conn = $db->getConnection();
$locRaw = locCTPN($conn);
// $loc = $locRaw ?: "";
$loc = $locRaw ? "$locRaw AND d.is_deleted = 0" : "WHERE d.is_deleted = 0";

// Tổng số chi tiết phiếu nhập
$total = $db->select("SELECT COUNT(*) AS total FROM importreceipt_details d $loc", []);
$totalItems = $total[0]['total'];

// Trang hiện tại
$page = isset($_POST['pageproduct']) ? (int)$_POST['pageproduct'] : 1;
$limit = 10;

$pagination = new Pagination($totalItems, $limit, $page);
$offset = $pagination->offset();

// Truy vấn dữ liệu chi tiết phiếu nhập (tính luôn total_price)
// $data = $db->select("
// SELECT 
//     importreceipt_details_id, importreceipt_id, product_id, variant_id, quantity, created_at, status
// FROM importreceipt_details
// $loc
// ORDER BY importreceipt_details_id ASC
// LIMIT $limit OFFSET $offset
// ", []);
$data = $db->select("
SELECT 
    d.importreceipt_details_id, d.importreceipt_id, d.product_id, d.variant_id, d.quantity, d.created_at, d.status,
    p.name AS product_name
FROM importreceipt_details d
JOIN products p ON d.product_id = p.product_id
$loc
ORDER BY d.importreceipt_details_id ASC
LIMIT $limit OFFSET $offset
", []);

ob_start();
foreach ($data as $row) {
    $id_ct = $row['importreceipt_details_id'];
    $id_pn = $row['importreceipt_id'];    
    $id_sp = $row['product_id'];
    $variant_id = $row['variant_id'];
    $quantity = $row['quantity'];
    $ngaylap = $row['created_at'];
    $hideBtn = $row['status'] == 0 ? 'style="display:none;"' : '';
    $product_name = $row['product_name'];



    echo "
        <tr class='text-center'>
            <td class='hienthiid'>$id_ct</td>
            <td class='hienthiid'>$id_pn</td>
            <td class='hienthiid'>$variant_id</td>
            <td class='tensp giaodienmb'>$product_name</td>
            <td class='tensp giaodienmb'>$ngaylap</td>
<td class='hienthigia giaodienmb'>
    " . ($row['status'] == 1 ? "
        <button class='btn btn-warning btn-sm btn-toggle-status fs-6 rounded-4' data-idct='$id_ct'><i class='fa-solid fa-hourglass-half'></i> Chờ Xác nhận</button>
    " : "<span class='badge bg-success'><i class='fa-regular fa-circle-check'></i> Đã xác nhận</span>") . "
</td>

            <td>
<div class='d-flex justify-content-center gap-3'>
    " . ($row['status'] == 1 ? "
        <button class='btn btn-success btn-sua'
            data-idct='$id_ct'
            data-idpn='$id_pn'
            data-idsp='$id_sp'
            data-variant='$variant_id'
            data-soluong='$quantity'
            data-ngaylap='$ngaylap'
            style='width:90px;'><i class='fa-regular fa-pen-to-square'></i> Sửa</button>
        <button class='btn btn-danger btn-xoa'
            data-idct='$id_ct'
            style='width:90px;'><i class='fa-regular fa-trash-can'></i> Xóa</button>
    " : "") . "
    <button class='btn btn-info btn-xemchitiet text-white'
        data-idct='$id_ct'
        style='width:100px;'><i class='fa-regular fa-eye'></i> chi tiết</button>
</div>

            </td>
        </tr>
    ";
}
$productHTML = ob_get_clean(); // ❗ THIẾU DÒNG NÀY

ob_start();
$pagination->render();
$paginationHTML = ob_get_clean();

if ($pagination->getTotalPages() <= 1) {
    $paginationHTML = ''; // không hiển thị nếu chỉ có 1 trang
}

// Trả về JSON
header('Content-Type: application/json');
echo json_encode([
    'products' => $productHTML,
    'pagination' => $paginationHTML
]);
