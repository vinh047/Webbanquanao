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

// Truy vấn dữ liệu chi tiết phiếu nhập (tính luôn total_price)
$data = $db->select("
    SELECT 
        ImportReceipt_details_id, ImportReceipt_id, product_id, variant_id, quantity, import_price, 
        (quantity * import_price) AS total_price, created_at
    FROM importreceipt_details 
    ORDER BY ImportReceipt_details_id ASC
    LIMIT $limit OFFSET $offset
", []);

ob_start();
foreach ($data as $row) {
    $id_ct = $row['ImportReceipt_details_id'];
    $id_pn = $row['ImportReceipt_id'];
    $id_sp = $row['product_id'];
    $variant_id = $row['variant_id'];
    $quantity = $row['quantity'];
    $import_price = number_format($row['import_price'], 0, ',', '.');
    $total_price = number_format($row['total_price'], 0, ',', '.');
    $ngaylap = $row['created_at'];

    echo "
        <tr class='text-center'>
            <td class='hienthiid'>$id_ct</td>
            <td class='hienthiid'>$id_pn</td>
            <td class='hienthiid'>$id_sp</td>
            <td class='hienthiid'>$variant_id</td>
            <td class='hienthigia'>$total_price VNĐ</td>
            <td class='tensp'>$ngaylap</td>
            <td>
                <div class='d-flex justify-content-center gap-3'>
                    <button class='btn btn-success btn-sua'
                        data-idct='$id_ct'
                        data-idpn='$id_pn'
                        data-idsp='$id_sp'
                        data-variant='$variant_id'
                        data-soluong='$quantity'
                        data-gia='{$row['import_price']}'
                        data-ngaylap='$ngaylap'
                        data-tongtien='{$row['total_price']}'
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
