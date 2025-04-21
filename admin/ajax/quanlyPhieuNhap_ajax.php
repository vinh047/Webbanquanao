<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
require_once(__DIR__ . '/../../layout/phantrang.php');

$db = DBConnect::getInstance();

// Tổng sản phẩm
$total = $db->select("SELECT COUNT(*) AS total FROM importreceipt", []);
$totalItems = $total[0]['total'];

// Lấy trang hiện tại
$page = isset($_GET['pageproduct']) ? (int)$_GET['pageproduct'] : 1;
$limit = 5;

$pagination = new Pagination($totalItems, $limit, $page);
$offset = $pagination->offset();

// Truy vấn sản phẩm theo trang
$data = $db->select("SELECT *
                    FROM importreceipt
                    ORDER BY ImportReceipt_id ASC
                    LIMIT $limit OFFSET $offset",[]);

ob_start();
foreach ($data as $row) {
    $idpn = $row['ImportReceipt_id'];
    $idnv = $row['user_id'];
    $idncc = $row['supplier_id'];
    $gia = number_format($row['total_price'], 0, ',', '.');
    $ngaylap = $row['created_at'];
    $hideBtn = $row['status'] == 0 ? 'display:none;' : '';
    echo "
    <tr class='text-center'>
        <td class='hienthiid'>$idpn</td>
        <td class='hienthiid'>$idnv</td>
        <td class='hienthiid'>$idncc</td>
        <td class='hienthigia'>$gia VNĐ</td>
        <td class='tensp'>$ngaylap</td>
        <td class='tensp'>
            " . ($row['status'] == 1
                ? "<button class='btn btn-warning btn-sm btn-toggle-status rounded-4 fs-6' data-idpn='$idpn'><i class='fa-solid fa-hourglass-half'></i> Chờ Xác nhận</button>"
                : "<span class='badge bg-success'><i class='fa-regular fa-circle-check'></i> Đã xác nhận</span>") . "
        </td>
        <td>
            <div class='d-flex justify-content-center gap-3'>
                <div>
<button class='btn btn-success btn-sua'
    data-idpn='$idpn'
    data-idnv='$idnv'
    data-idncc='$idncc'
    data-gia='{$row['total_price']}'
    data-ngaylap='$ngaylap'
    style='width:90px; $hideBtn'><i class='fa-regular fa-pen-to-square'></i> Sửa</button>
                </div>
                <div>
                    <button class='btn btn-danger btn-xoa'
    data-idpn='$idpn'
    style='width:90px; $hideBtn'><i class='fa-regular fa-trash-can'></i> Xóa</button>
                </div>
                <div>                    <button class='btn btn-info btn-xemchitietPN' data-idpn='$idpn' style='width:90px;margin-left:1px;'><i class='fa-regular fa-eye'></i> chi tiết</button>
</div>
            </div>
        </td>
    </tr>";

}
$productHTML = ob_get_clean(); // lấy nội dung ra và dừng buffer

ob_start();
$pagination->render();
$paginationHTML = ob_get_clean();

// Trả ra 1 JSON gói 2 phần
header('Content-Type: application/json'); // ✅ thêm dòng này

echo json_encode([
    'products' => $productHTML,
    'pagination' => $paginationHTML
]);

?>