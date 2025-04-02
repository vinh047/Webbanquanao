<?php

$connection = mysqli_connect("localhost","root","","db_web_quanao",3307);
if(!$connection)
{
    echo 'Không thể kết nối đến database';
    exit;
}

mysqli_set_charset($connection, 'utf8');

require_once('product_filter_sort.php');

$limit = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

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

$productSQL = "SELECT * FROM products 
               JOIN product_variants ON products.product_id = product_variants.product_id 
               $loc $sapxep
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
            <div class="xacdinhZ col-md-3 col-6 mt-3 effect_hover">
                    <div class="border rounded-1">
                        <a href="#" class="text-decoration-none text-dark ">
                            <img src="/Webbanquanao/assets/img/sanpham/sp1.jpg" alt="" class="img-fluid">
                        </a>
                            <div class="mt-2 p-2 pt-1">
                                <div class="">
                                    <p class="mb-0 fw-lighter">Nam</p>
                                    <p class="mb-0">' . $gia . ' VNĐ</p>   
                                    <p class="mb-0">' . $name . '</p>
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

if($totalPage > 1)
{
    echo '

    <section class="phantrang py-4">

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-3 text-center d-flex flex-wrap justify-content-center gap-2"">';

                for ($i = 1; $i <= $totalPage; $i++) {
                    $active = ($i == $page) ? 'style="font-weight:bold;"' : '';
                    echo '<a href="?page=' . $i . '" class = "border p-2 px-3 text-decoration-none text-dark effect_hover" ' . $active . '> ' . $i . '</a> ';

                }

    echo '
                </div>
            </div>
        </div>

    </section>

    ';
}




mysqli_close($connection);
?>