<?php
require_once('../../database/DBConnection.php');
require_once('../../layout/phantrang.php');

$db = DBConnect::getInstance();

// Tổng số chi tiết phiếu nhập
$total = $db->select("SELECT COUNT(*) AS total FROM importreceipt_details", []);
$totalItems = $total[0]['total'];

// Trang hiện tại
$page = isset($_GET['pageproduct']) ? (int)$_GET['pageproduct'] : 1;
$limit = 5;

$pagination = new Pagination($totalItems, $limit, $page);
$offset = $pagination->offset();

// Truy vấn dữ liệu chi tiết phiếu nhập
$data = $db->select("
    SELECT * FROM importreceipt_details 
    ORDER BY ImportReceipt_details_id ASC
    LIMIT $limit OFFSET $offset
", []);

ob_start();
foreach ($data as $row) {
    $id_ct = $row['ImportReceipt_details_id'];
    $id_pn = $row['ImportReceipt_id'];
    $id_sp = $row['product_id'];
    $total = number_format($row['total_price'], 0, ',', '.');
    $ngaylap = $row['created_at'];

    echo "
        <tr class='text-center'>
            <td class='hienthiid'>$id_ct</td>
            <td class='hienthiid'>$id_pn</td>
            <td class='hienthiid'>$id_sp</td>
            <td class='hienthigia'>$total VNĐ</td>
            <td class='tensp'>$ngaylap</td>
            <td>
                <div class='d-flex justify-content-center gap-3'>
                    <button class='btn btn-success btn-sua'
                        data-idct='$id_ct'
                        data-idpn='$id_pn'
                        data-idsp='$id_sp'
                        data-gia='{$row['total_price']}'
                        data-ngaylap='$ngaylap'
                        style='width:60px;'>Sửa</button>
                    <button class='btn btn-danger btn-xoa'
                        data-idct='$id_ct'
                        style='width:60px;'>Xóa</button>
                </div>
            </td>
        </tr>
    ";
}
$productHTML = ob_get_clean();

ob_start();
$pagination->render();
$paginationHTML = ob_get_clean();

// Trả về JSON
header('Content-Type: application/json');
echo json_encode([
    'products' => $productHTML,
    'pagination' => $paginationHTML
]);
