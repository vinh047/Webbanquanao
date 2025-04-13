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
    $gia = number_format($row['price_sale'], 0, ',', '.');        
    $rating_avg = $row['rating_avg'];
    $rating_count = $row['rating_count'];
    $sold_count = $row['sold_count'];
    $img = $row['image'];
    $imgPath = './assets/img/sanpham/' . $img;
echo '
    <div class="xacdinhZ col-md-3 col-6 mt-3 effect_hover p-md-2 p-1">
        <div class="border rounded-1">
            <a href="#" class="text-decoration-none text-dark ">
                <img src="' . $imgPath . '" alt="" class="img-fluid product-img">
            </a>
            <div class="mt-2 p-2 pt-1">
                <div class="">
                    <p class="mb-0 fw-lighter">Nam</p>
                    <p class="mb-0">' . $gia . ' VNĐ</p>   
                    <p class="mb-0 limit-text">' . $name . '</p>
                    <button class="btn btn-dark btn-sm mt-2 w-100"
                        onclick="addToCart(' . $id . ', \'' . addslashes($name) . '\', ' . $row['price'] . ', \'' . $imgPath . '\')">
                        <i class="fa fa-cart-plus me-1"></i> Thêm vào giỏ
                    </button> 
                </div>
            </div>
        </div>
    </div>
';

}

// Nếu chỉ có 1 trang thì cho nó cái padding trên dưới 3 để kh bị xấu :v
if($totalPage == 1)
{
    $paddingTest = 'py-3';
}else
{
    $paddingTest = 'py-0';
}
echo '<div class = "' . $paddingTest . '">

    </div>
';


if ($pagination->totalPages > 1) {
    $pagination->render(['page' => 'sanpham']);
}

mysqli_close($connection);
?>