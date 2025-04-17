<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
require_once(__DIR__ . '/../../layout/phantrang.php');

$db = DBConnect::getInstance();

// Tổng sản phẩm
$total = $db->select("SELECT COUNT(*) AS total FROM products", []);
$totalItems = $total[0]['total'];

// Lấy trang hiện tại
$page = isset($_GET['pageproduct']) ? (int)$_GET['pageproduct'] : 1;
$limit = 10;

$pagination = new Pagination($totalItems, $limit, $page);
$offset = $pagination->offset();

// Truy vấn sản phẩm theo trang
$data = $db->select("SELECT p.*, c.name AS tenloai 
                    FROM products p 
                    JOIN categories c ON p.category_id = c.category_id 
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
                data-loaiid='{$row['category_id']}' style='width:60px;' >Sửa</button></div>
                <div>
                <button class='btn btn-danger btn-xoa' data-id='$id' style='width:60px;'>Xóa</button>
                </div>
                </div>
            </td>
        </tr>
    ";
}
$productHTML = ob_get_clean(); // lấy nội dung ra và dừng buffer

ob_start();
$pagination->render();
$paginationHTML = ob_get_clean();

// Trả ra 1 JSON gói 2 phần
echo json_encode([
    'products' => $productHTML,
    'pagination' => $paginationHTML
]);
?>
