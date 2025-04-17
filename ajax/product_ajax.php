<?php
$connection = mysqli_connect("localhost","root","","db_web_quanao");
if(!$connection)
{
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

// ✅ Lọc thêm is_deleted = 0 và stock > 0
$whereCondition = "product_variants.is_deleted = 0 AND product_variants.stock > 0";
if (!empty($loc)) {
    // locSanPham() đã trả về câu WHERE rồi, nên nối bằng AND
    $loc = str_replace("WHERE", "WHERE $whereCondition AND", $loc);
} else {
    $loc = "WHERE $whereCondition";
}

// ✅ Câu đếm
$countSQL = "SELECT COUNT(DISTINCT products.product_id) AS total
             FROM products
             JOIN product_variants ON products.product_id = product_variants.product_id
             $loc";
$countResult = mysqli_query($connection, $countSQL);
$totalRow = mysqli_fetch_assoc($countResult);
$totalItems = $totalRow['total'];
$totalPage = ceil($totalItems / $limit);

if ($totalItems == 0) {
    echo 'REDIRECT_TO_HOME';
    exit;
}

$pagination = new Pagination($totalItems, $limit, $page);
$offset = $pagination->offset();

// ✅ Truy vấn sản phẩm
$productSQL = "SELECT products.*, MIN(product_variants.image) as image
               FROM products 
               JOIN product_variants ON products.product_id = product_variants.product_id
               $loc
               GROUP BY products.product_id
               $sapxep
               LIMIT $limit OFFSET $offset";

$result = mysqli_query($connection, $productSQL);

while($row = mysqli_fetch_assoc($result))
{
    $id = $row['product_id'];
    $name = $row['name'];
    $description = $row['description'];
    $category_id  = $row['category_id'];
    $gia = number_format($row['price'], 0, ',', '.');        
    $rating_avg = $row['rating_avg'];
    $rating_count = $row['rating_count'];
    $sold_count = $row['sold_count'];
    $img = $row['image'];
    echo '
            <div class="xacdinhZ col-md-3 col-6 mt-3 effect_hover p-md-2 p-1">
                    <div class="border rounded-1">
                        <a href="../layout/product_detail.php?product_id='.$id.'" class="text-decoration-none text-dark ">
                            <img src="../assets/img/sanpham/' . $img .'" alt="" class="img-fluid product-img">
                        </a>
                            <div class="mt-2 p-2 pt-1">
                                <div class="">
                                    <p class="mb-0 fw-lighter">Nam</p>
                                    <p class="mb-0">' . $gia . ' VNĐ</p>   
                                    <p class="mb-0 limit-text">' . $name . '</p>
                                    <button class="btn btn-dark btn-sm mt-2 w-100"
                                       onclick="addToCart(' . $id . ', \'' . $name . '\', ' . $row['price'] . ')">
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