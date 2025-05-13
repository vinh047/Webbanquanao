<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
require_once(__DIR__ . '/../../layout/phantrang.php');
require_once('functionLoc.php');
$db = DBConnect::getInstance();
$conn = $db->getConnection();
$locRaw = locPhieuNhap($conn);
$loc = $locRaw ?: "";
// Tổng sản phẩm
$total = $db->select("SELECT COUNT(*) AS total FROM importreceipt im $loc", []);
$totalItems = $total[0]['total'];

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
//Lấy id nhân viên
$permissions = $_SESSION['permissions'] ?? [];

$hasReadPermission = in_array('read', $permissions);
$hasWritePermission = in_array('write', $permissions);
$hasDeletePermission = in_array('delete', $permissions);

// Kiểm tra nếu có bất kỳ quyền nào
$hasAnyActionPermission = $hasReadPermission || $hasWritePermission || $hasDeletePermission;


// Lấy trang hiện tại
$page = isset($_POST['pageproduct']) ? (int)$_POST['pageproduct'] : 1;
$limit = 5;

$pagination = new Pagination($totalItems, $limit, $page);
$offset = $pagination->offset();

// Truy vấn sản phẩm theo trang
$data = $db->select("SELECT 
    im.ImportReceipt_id,
    im.created_at,
    im.status,
    im.supplier_id,
    im.user_id,
    im.total_price,
    s.name AS ncc_name,
    u.name AS nv_name
FROM importreceipt im
JOIN supplier s ON im.supplier_id = s.supplier_id 
JOIN users u ON im.user_id = u.user_id
$loc
ORDER BY im.ImportReceipt_id ASC
LIMIT $limit OFFSET $offset", []);


ob_start();
foreach ($data as $row) {
    $idpn = $row['ImportReceipt_id'];
    $idnv = $row['user_id'];
    $idncc = $row['supplier_id'];
    $gia = number_format($row['total_price'], 0, ',', '.');
    $ngaylap = $row['created_at'];
    $hideBtn = $row['status'] == 0 ? 'display:none;' : '';
    $hideGap = $row['status'] ==0 ? 'gap-0' : 'gap-3';
    $tennv = $row['nv_name'];
    $tenncc = $row['ncc_name'];
    echo "
    <tr class='text-center'>
        <td class='hienthiid'>$idpn</td>
        <td class='hienthigia giaodienmb'>$tennv</td>
        <td class='tensp giaodienmb'>$tenncc</td>
        <td class='tensp giaodienmb'>$gia VNĐ</td>
        <td class='tensp giaodienmb'>$ngaylap</td>
        <td class='tensp'>
            " . ($row['status'] == 1
                ? "<button class='btn btn-warning btn-sm btn-toggle-status rounded-4 fs-6 text-dark' data-idpn='$idpn'><i class='fa-solid fa-hourglass-half'></i> Chờ Xác nhận</button>"
                : "<span class='badge bg-success'><i class='fa-regular fa-circle-check'></i> Đã xác nhận</span>") . "
        </td>
" . ($hasAnyActionPermission ? "
<td>
    <div class='d-flex justify-content-center $hideGap'>
        " . ($hasWritePermission ? "
        <div>
            <button class='btn btn-success btn-sua'
                data-idpn='$idpn'
                data-idnv='$idnv'
                data-idncc='$idncc'
                data-tennv='$tennv'
                data-gia='{$row['total_price']}'
                data-ngaylap='$ngaylap'
                style='width:90px; $hideBtn'><i class='fa-regular fa-pen-to-square'></i> Sửa</button>
        </div>
        " : "") . "

        " . ($hasDeletePermission ? "
        <div>
            <button class='btn btn-danger btn-xoa'
                data-idpn='$idpn'
                style='width:90px; $hideBtn'><i class='fa-regular fa-trash-can'></i> Xóa</button>
        </div>
        " : "") . "

        " . ($hasReadPermission ? "
        <div>
            <button class='btn btn-info text-white btn-xemchitietPN text-white' data-idpn='$idpn' style='width:90px;margin-left:1px;'>
                <i class='fa-regular fa-eye'></i> chi tiết
            </button>
        </div>
        " : "") . "
    </div>
</td>
" : "") . "

    </tr>";

}
$productHTML = ob_get_clean(); // ❗ THIẾU DÒNG NÀY

ob_start();
$pagination->render();
$paginationHTML = ob_get_clean();

if ($pagination->getTotalPages() <= 1) {
    $paginationHTML = ''; // không hiển thị nếu chỉ có 1 trang
}
// Trả ra 1 JSON gói 2 phần
header('Content-Type: application/json'); // ✅ thêm dòng này

echo json_encode([
    'products' => $productHTML,
    'pagination' => $paginationHTML
]);

?>