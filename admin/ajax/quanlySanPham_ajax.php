<?php
    require_once(__DIR__ . '/../../database/DBConnection.php');
    require_once(__DIR__ . '/../../layout/phantrang.php');
    require_once('functionLoc.php');
    $db = DBConnect::getInstance();
    $connection = $db->getConnection();

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

    $locRaw = locSanPham($connection);
    // $loc = $locRaw ?: ""; // dùng luôn WHERE nếu có, không tự thêm AND
    if (!empty($locRaw)) {
        $loc = $locRaw . " AND p.is_deleted = 0";
    } else {
        $loc = "WHERE p.is_deleted = 0";
    }

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
    $data = $db->select("
        SELECT 
            p.*, 
            c.name AS tenloai, 
            im.unit_price AS gianhap
        FROM products p
        JOIN categories c ON p.category_id = c.category_id
        LEFT JOIN (
            SELECT product_id, unit_price
            FROM importreceipt_details
            WHERE importreceipt_details_id IN (
                SELECT MAX(importreceipt_details_id)
                FROM importreceipt_details
                GROUP BY product_id
            )
        ) im ON p.product_id = im.product_id
        $loc
        ORDER BY p.product_id ASC 
        LIMIT $limit OFFSET $offset
    ", []);


    ob_start();
    foreach ($data as $row) {
        $id = $row['product_id'];
        $ten = $row['name'];
        $loai = $row['tenloai'];
        $mota = $row['description'];
        $gianhap = number_format($row['gianhap'],0,',','.');
        // $gia = number_format($row['price'], 0, ',', '.');
        $giaban = number_format($row['price_sale'],0,',','.');
        $pttg = $row['pttg'];
        echo "
            <tr class='text-center'>
                <td class='hienthiid align-middle'>$id</td>
                <td class='tensp giaodienmb align-middle'>$ten</td>
                <td class='hienthiloai giaodienmb align-middle'>$loai</td>
                <td class='mota giaodienmb align-middle'>$mota</td>
                <td class='hienthigia giaodienmb align-middle'>$giaban đ</td>
    " . ($hasAnyActionPermission ? "
    <td>
        <div class='d-flex justify-content-center gap-3'>
            " . ($hasWritePermission ? "
            <div>
                <button class='btn btn-success btn-sua'
                    data-id='$id'
                    data-ten=\"$ten\"
                    data-mota=\"$mota\"
                    data-gia='{$row['gianhap']}'
                    data-giaban='{$row['price_sale']}'
                    data-pttg = \"$pttg\"
                    data-loaiid='{$row['category_id']}' style='width:90px;'>
                    <i class='fa-regular fa-pen-to-square'></i> Sửa
                </button>
            </div>
            " : "") . "

            " . ($hasDeletePermission ? "
            <div>
                <button class='btn btn-danger btn-xoa' data-id='$id' style='width:90px;'>
                    <i class='fa-regular fa-trash-can'></i> Xóa
                </button>
            </div>
            " : "") . "

            " . ($hasReadPermission ? "
            <div>
                <button class='btn btn-info text-white btn-xemchitietPN' data-idpn='$id' style='width:90px;margin-left:1px;'>
                    <i class='fa-regular fa-eye'></i> chi tiết
                </button>
            </div>
            " : "") . "
        </div>
    </td>
    " : "") . "

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