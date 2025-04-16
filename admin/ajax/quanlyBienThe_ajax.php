<?php
require_once('../../database/DBConnection.php');
require_once('../../layout/phantrang.php');

$db = DBConnect::getInstance();

// Phân trang
$total = $db->select("SELECT COUNT(*) AS total FROM product_variants WHERE is_deleted = 0", []);
$totalItems = $total[0]['total'];
$page = isset($_GET['pageproduct']) ? (int)$_GET['pageproduct'] : 1;
$limit = 10;

$pagination = new Pagination($totalItems, $limit, $page);
$offset = $pagination->offset();

// Truy vấn danh sách biến thể sản phẩm
$data = $db->select("
    SELECT p.*, c.name AS tenMau, s.name AS tenSize, d.importreceipt_details_id
    FROM product_variants p
    JOIN colors c ON p.color_id = c.color_id
    JOIN products pr ON p.product_id = pr.product_id
    JOIN sizes s ON s.size_id = p.size_id
    LEFT JOIN importreceipt_details d 
        ON p.variant_id = d.variant_id
        AND d.importreceipt_details_id = (
            SELECT idd.importreceipt_details_id 
            FROM importreceipt_details idd 
            WHERE idd.variant_id = p.variant_id 
            ORDER BY created_at DESC 
            LIMIT 1
        )
    WHERE p.is_deleted = 0
    ORDER BY p.variant_id ASC
    LIMIT $limit OFFSET $offset
", []);



ob_start();
foreach ($data as $row) {
    $idvr = $row['variant_id'];
    $idsp = $row['product_id'];
    $anh = $row['image'];
    $size = $row['tenSize'];
    $soluong = $row['stock'];
    $mau = $row['tenMau'];
    $id_mau = $row['color_id'];
    $id_size = $row['size_id'];
    $idctpn = $row['importreceipt_details_id'] ?? '';

    echo "
        <tr class='text-center'>
            <td class='hienthiidbt'>$idvr</td>
            <td class='hienthiidsp'>$idsp</td>
            <td class='hienthianh'><img src='../../assets/img/sanpham/$anh' style='width:100px;height:100px;object-fit:cover;' class='img-fluid'></td>
            <td class='hienthisize'>$size</td>
            <td class='hienthigia'>$soluong</td>
            <td class='hienthimau'>$mau</td>
            <td class='hienthibtn'>
                    <div class='d-flex justify-content-center gap-3'>
                    <div><button class='btn btn-success btn-sm btn-sua' 
                    data-idct='$idctpn'
                    data-idvr='$idvr'
                    data-idsp='$idsp'
                    data-anh='$anh'
                    data-size='$id_size'
                    data-soluong='$soluong'
                    data-mau='$id_mau'
                    style='width:60px;height:40px;font-size:17px;'>Sửa</button></div>
                    <div><button class='btn btn-danger btn-sm btn-xoa' data-id='$idvr' style='width:60px;height:40px;font-size:17px;'>Xóa</button></div>
                    </div>
            </td>
        </tr>
    ";
}
$productHTML = ob_get_clean();

ob_start();
$pagination->render();
$paginationHTML = ob_get_clean();

echo json_encode([
    'products' => $productHTML,
    'pagination' => $paginationHTML
]);
?>
