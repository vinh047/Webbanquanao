<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
require_once(__DIR__ . '/../../layout/phantrang.php');
require_once('functionLoc.php');
$db = DBConnect::getInstance();
$connection = $db->getConnection();
// Lọc
$locRaw = locBienThe($connection);
$loc = "WHERE product_variants.is_deleted = 0";
if (!empty($locRaw)) {
    // Nếu locBienThe trả về điều kiện, gộp thêm AND
    $loc .= " AND " . ltrim($locRaw, "WHERE ");
}

// Phân trang
$total = $db->select("    
    SELECT COUNT(*) AS total
    FROM product_variants
    JOIN colors c ON product_variants.color_id = c.color_id
    JOIN sizes s ON product_variants.size_id = s.size_id
    JOIN products pr ON product_variants.product_id = pr.product_id
    $loc AND product_variants.is_deleted = 0", []);
$totalItems = $total[0]['total'];
$page = isset($_POST['pageproduct']) ? (int)$_POST['pageproduct'] : 1;
$limit = 10;

$pagination = new Pagination($totalItems, $limit, $page);
$offset = $pagination->offset();

// Truy vấn danh sách biến thể sản phẩm
$data = $db->select("
    SELECT product_variants.*, c.name AS tenMau, s.name AS tenSize, d.importreceipt_details_id
    FROM product_variants
    JOIN colors c ON product_variants.color_id = c.color_id
    JOIN products pr ON product_variants.product_id = pr.product_id
    JOIN sizes s ON s.size_id = product_variants.size_id
    LEFT JOIN importreceipt_details d 
        ON product_variants.variant_id = d.variant_id
        AND d.importreceipt_details_id = (
            SELECT idd.importreceipt_details_id 
            FROM importreceipt_details idd 
            WHERE idd.variant_id = product_variants.variant_id 
            ORDER BY created_at DESC 
            LIMIT 1
        )
    $loc AND product_variants.is_deleted = 0
    ORDER BY product_variants.variant_id ASC
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
                    <div class='d-flex justify-content-center gap-2'>
                    <div>
                    <button class='btn btn-success btn-sm btn-sua' 
                    data-idct='$idctpn'
                    data-idvr='$idvr'
                    data-idsp='$idsp'
                    data-anh='$anh'
                    data-size='$id_size'
                    data-soluong='$soluong'
                    data-mau='$id_mau'
                    style='width:80px;'><i class='fa-regular fa-pen-to-square'></i> Sửa</button>
                    </div>
                    <div>
                    <button class='btn btn-danger btn-sm btn-xoa' data-id='$idvr' style='width:80px;'><i class='fa-regular fa-trash-can'></i> Xóa</button>
                    </div>
                    </div>
            </td>
        </tr>
    ";
}
$productHTML = ob_get_clean();

$paginationHTML = '';
if ($totalItems > $limit) {
    ob_start();
    $pagination->render();
    $paginationHTML = ob_get_clean();
}

echo json_encode([
    'products' => $productHTML,
    'pagination' => $paginationHTML
]);
?>
