<?php
$connection = mysqli_connect("localhost", "root", "", "db_web_quanao");
if (!$connection) {
    echo 'Không thể kết nối đến database';
    exit;
}
mysqli_set_charset($connection, 'utf8');

require_once('product_filter_sort.php');
require_once('../layout/phantrang.php');

$limit = 8;
$page = isset($_GET['pageproduct']) ? (int)$_GET['pageproduct'] : 1;

$loc = locSanPham($connection);

$sapxep = sapXepSanPham();
$sapxep = str_replace("products.", "p.", $sapxep);

// Alias "v" sẽ dùng cho product_variants
$whereCondition = "v.is_deleted = 0 AND v.stock > 0";
if (!empty($loc)) {
    // Replace alias đúng cho subquery
    $loc = str_replace("product_variants.", "v.", $loc);
    $loc = str_replace("WHERE", "WHERE $whereCondition AND", $loc);
} else {
    $loc = "WHERE $whereCondition";
}

// ✅ Câu đếm (dùng subquery an toàn)
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
    echo "❌ Lỗi truy vấn đếm sản phẩm: " . mysqli_error($connection);
    echo "<br><pre>$countSQL</pre>";
    exit;
}
$totalRow = mysqli_fetch_assoc($countResult);
$totalItems = $totalRow['total'];
$totalPage = ceil($totalItems / $limit);

if ($totalItems == 0) {
    echo 'REDIRECT_TO_HOME';
    exit;
}

$pagination = new Pagination($totalItems, $limit, $page);
$offset = $pagination->offset();

// ✅ Truy vấn danh sách sản phẩm
$productSQL = "
SELECT p.*, v.variant_id, v.image
FROM products p
JOIN (
    SELECT * FROM product_variants
    WHERE is_deleted = 0 AND stock > 0
    GROUP BY product_id
) v ON p.product_id = v.product_id
$loc
$sapxep
LIMIT $limit OFFSET $offset
";

$result = mysqli_query($connection, $productSQL);
if (!$result) {
    echo "❌ Lỗi truy vấn sản phẩm: " . mysqli_error($connection);
    echo "<br><pre>$productSQL</pre>";
    exit;
}

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['product_id'];
    $name = $row['name'];
    $description = $row['description'];
    $category_id = $row['category_id'];
    $gia = number_format($row['price_sale'], 0, ',', '.');
    $rating_avg = $row['rating_avg'];
    $rating_count = $row['rating_count'];
    $sold_count = $row['sold_count'];
    $img = $row['image'];
    $imagePath = 'assets/img/sanpham/' . $img;
    $variant_id = $row['variant_id'];

    echo '
        <div class="xacdinhZ col-md-3 col-6 mt-3 effect_hover p-md-2 p-1">
            <div class="border rounded-1">
                <a href="../layout/product_detail.php?product_id=' . $id . '" class="text-decoration-none text-dark">
                    <img src="../' . $imagePath . '" alt="" class="img-fluid product-img">
                </a>
                <div class="mt-2 p-2 pt-1">
                    <div class="">
                        <p class="mb-0 fw-lighter">Nam</p>
                        <p class="mb-0">' . $gia . ' VNĐ</p>   
                        <p class="mb-0 limit-text">' . $name . '</p>
                        <button class="btn btn-dark btn-sm mt-2 w-100"
                           onclick="addToCart(' . $id . ', \'' . addslashes($name) . '\', ' . $row['price'] . ', \'' . $imagePath . '\', ' . $variant_id . ')">
                            <i class="fa fa-cart-plus me-1"></i> Thêm vào giỏ
                        </button> 
                    </div>
                </div>
            </div>
        </div>
    ';
}

// Padding nếu chỉ có 1 trang
$paddingTest = ($totalPage == 1) ? 'py-3' : 'py-0';
echo '<div class="' . $paddingTest . '"></div>';

// Phân trang
if ($pagination->totalPages > 1) {
    $pagination->render(['page' => 'sanpham']);
}

mysqli_close($connection);
?>