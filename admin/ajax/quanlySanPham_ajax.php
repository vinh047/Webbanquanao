<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
require_once(__DIR__ . '/../../layout/phantrang.php');
require_once('functionLoc.php');
$db = DBConnect::getInstance();
$connection = $db->getConnection();

$locRaw = locSanPham($connection);
$loc = $locRaw ?: ""; // dùng luôn WHERE nếu có, không tự thêm AND
// Tổng sản phẩm
$total = $db->select("SELECT COUNT(*) AS total 
                      FROM products p 
                      JOIN categories c ON p.category_id = c.category_id 
                      $loc", []);
$totalItems = $total[0]['total'];

// Lấy trang hiện tại
$page = isset($_POST['pageproduct']) ? (int)$_POST['pageproduct'] : 1;
$limit = 10;

$pagination = new Pagination($totalItems, $limit, $page);
$offset = $pagination->offset();

// Truy vấn sản phẩm theo trang
$data = $db->select("SELECT p.*, c.name AS tenloai 
                    FROM products p 
                    JOIN categories c ON p.category_id = c.category_id 
                    $loc
                    ORDER BY p.product_id ASC 
                    LIMIT $limit OFFSET $offset", []);

ob_start();
foreach ($data as $row) {
    $id = $row['product_id'];
    $ten = $row['name'];
    $loai = $row['tenloai'];
    $mota = $row['description'];
    $gia = number_format($row['price'], 0, ',', '.');
    $giaban = number_format($row['price_sale'],0,',','.');
    $pttg = $row['pttg'];
    echo "
        <tr class='text-center'>
            <td class='hienthiid'>$id</td>
            <td class='tensp'>$ten</td>
            <td class='hienthiloai'>$loai</td>
            <td class='mota'>$mota</td>
            <td class='hienthigia'>$gia VNĐ</td>
            <td class='hienthigia'>$giaban VNĐ</td>
            <td>
                <div class='d-flex justify-content-center gap-3'>
                <div>
                <button class='btn btn-success btn-sua'
                data-id='$id'
                data-ten=\"$ten\"
                data-mota=\"$mota\"
                data-gia='{$row['price']}'
                data-giaban='{$row['price_sale']}'
                data-pttg = \"$pttg\"
                data-loaiid='{$row['category_id']}' style='width:90px;'><i class='fa-regular fa-pen-to-square'></i> Sửa</button></div>
                <div>
                <button class='btn btn-danger btn-xoa' data-id='$id' style='width:90px;'><i class='fa-regular fa-trash-can'></i> Xóa</button>
                </div>
                <div>
                <button class='btn btn-info btn-xemchitietPN' data-idpn='$id' style='width:90px;margin-left:1px;'><i class='fa-regular fa-eye'></i> chi tiết</button>
                </div>
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


// Trả ra 1 JSON gói 2 phần
echo json_encode([
    'products' => $productHTML,
    'pagination' => $paginationHTML
]);
?>
