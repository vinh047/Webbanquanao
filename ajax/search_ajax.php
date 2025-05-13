<?php
$connection = mysqli_connect("localhost", "root", "", "db_web_quanao");
if (!$connection) {
    echo 'Không thể kết nối đến database';
    exit;
}
mysqli_set_charset($connection, 'utf8');

require_once('product_filter_sort.php'); // Dùng để lọc nâng cao
require_once('../layout/phantrang.php');

// ✅ Gán q => tensp để locSanPham xử lý
if (isset($_GET['q']) && empty($_GET['tensp'])) {
    $_GET['tensp'] = $_GET['q'];
}

$limit = 8;
$page = isset($_GET['pageproduct']) ? (int)$_GET['pageproduct'] : 1;

// Lọc nâng cao từ product_filter_sort
$loc = locSanPham($connection);
$sapxep = str_replace("products.", "p.", sapXepSanPham());

// WHERE mặc định
$whereCondition = "v.is_deleted = 0 AND v.stock > 0";
$loc = $loc
    ? str_replace(["products.", "product_variants.", "WHERE"], ["p.", "v.", "WHERE $whereCondition AND"], $loc)
    : "WHERE $whereCondition";

// Đếm tổng
$countSQL = "
SELECT COUNT(*) AS total FROM (
    SELECT p.product_id
    FROM products p
    JOIN (
        SELECT * FROM product_variants
        WHERE is_deleted = 0 AND stock > 0
        GROUP BY product_id
    ) v ON p.product_id = v.product_id
    $loc
    GROUP BY p.product_id
) AS t
";

$countResult = mysqli_query($connection, $countSQL);
if (!$countResult) {
    echo "\u274c Lỗi truy vấn đếm sản phẩm: " . mysqli_error($connection);
    exit;
}
$totalItems = mysqli_fetch_assoc($countResult)['total'];

if ($totalItems == 0) {
    echo '<div class="col-12 text-center text-muted py-5">Không tìm thấy sản phẩm phù hợp.</div>';
    exit;
}

$pagination = new Pagination($totalItems, $limit, $page);
$offset = $pagination->offset();

// Truy vấn chính
$productSQL = "
SELECT p.*, v.variant_id, v.image
FROM products AS p
JOIN (
    SELECT * FROM product_variants
    WHERE is_deleted = 0 AND stock > 0
    GROUP BY product_id
) AS v ON p.product_id = v.product_id
$loc
$sapxep
LIMIT $limit OFFSET $offset
";

$result = mysqli_query($connection, $productSQL);
if (!$result) {
    echo "\u274c Lỗi truy vấn sản phẩm: " . mysqli_error($connection);
    exit;
}

// Hiển thị từng sản phẩm
while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['product_id'];
    $name = $row['name'];
    $gia = number_format($row['price_sale'], 0, ',', '.');
    $imagePath = 'assets/img/sanpham/' . $row['image'];

    // Load ảnh màu
    $color_query = "
        SELECT DISTINCT v.color_id, v.image, c.name AS color_name
        FROM product_variants v
        JOIN colors c ON v.color_id = c.color_id
        WHERE v.product_id = $id AND v.is_deleted = 0 AND v.stock > 0
    ";
    $color_result = mysqli_query($connection, $color_query);
    $color_images_html = '';
    while ($color = mysqli_fetch_assoc($color_result)) {
        $color_images_html .= '<img src="../assets/img/sanpham/' . $color['image'] . '" 
            data-product-id="' . $id . '" 
            data-color-id="' . $color['color_id'] . '"
            data-image="../assets/img/sanpham/' . $color['image'] . '"
            title="' . htmlspecialchars($color['color_name'], ENT_QUOTES) . '"
            class="color-thumb"
            style="width:28px;height:28px;object-fit:cover;border-radius:3px;border:1px solid #ccc;margin-right:4px;cursor:pointer;">';
    }

    echo '
    <div class="xacdinhZ col-md-3 col-6 mt-3 effect_hover p-md-2 p-1">
        <div class="border rounded-1 position-relative overflow-hidden product-item" 
             data-id="' . $id . '" style="background:#fff; cursor:pointer;">
            <div class="position-relative">
                <img id="main-image-' . $id . '" src="../' . $imagePath . '" alt=""
                    class="img-fluid product-img w-100" 
                    style="transition:transform 0.4s ease, opacity 0.4s ease;">
                <div class="size-group position-absolute start-0 end-0 d-flex justify-content-center gap-1 py-2 d-none"
                    style="bottom: 0; background: rgba(255, 255, 255, 0.9); z-index: 2;"></div>
            </div>
            <div class="mt-2 px-2">
                <div class="color-group d-flex justify-content-start">' . $color_images_html . '</div>
            </div>
            <div class="mt-2 p-2 pt-1">
                <p class="mb-0 fw-lighter">Nam</p>
                <p class="mb-0">' . $gia . ' VNĐ</p>
                <p class="mb-0 limit-text">' . $name . '</p>
                <button 
                    class="btn btn-dark btn-sm mt-2 w-100 add-to-cart-btn" 
                    data-product-id="' . $id . '"
                    data-product-name="' . htmlspecialchars($name, ENT_QUOTES) . '"
                    data-product-price="' . $row['price_sale'] . '" disabled>
                    <i class="fa fa-cart-plus me-1"></i>Thêm vào giỏ
                </button>
            </div>
        </div>
    </div>';
}

// Phân trang
$paddingTest = ($pagination->totalPages == 1) ? 'py-3' : 'py-0';
echo '<div class="' . $paddingTest . '"></div>';
if ($pagination->totalPages > 1) {
    $pagination->render(['page' => 'search']);
}
