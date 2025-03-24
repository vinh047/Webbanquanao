
    <!-- header -->
    <?php
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <title>sagkuto</title>
        <link rel="icon" type="./Images/png" href="/Webbanquanao/assets/img/logo_favicon/favicon.png">
        <link rel="stylesheet" href="/Webbanquanao/assets/fonts/font.css">
        <link rel="stylesheet" href="/Webbanquanao/assets/css/product.css">
        <link rel="stylesheet" href="/Webbanquanao/assets/css/footer.css">

    </head>';
    ?>




    <!-- bộ lọc -->
    <?php
    echo '<section class="pt-4 pb-3">

    <div class="container-md">
            
        <div class="border py-2 px-4 d-flex align-items-center">
            <div class="me-auto">
                <p class="mb-0">
                    <a href="/Webbanquanao/" class="text-decoration-none link-primary aHover">
                        Trang chủ
                    </a>
                    <span class="mx-2">
                        <i class="fa-solid fa-angle-right"></i>
                    </span>
                    <span class="text-dark">
                    <a href="?page=sanpham" class="text-decoration-none link-primary aHover">
                        Sản phẩm
                    </a>
                    </span>
                    <span class="mx-2">
                        <i class="fa-solid fa-angle-right"></i>
                    </span>
                    <span class="text-dark">
                        Áo khoác
                    </span>
                </p>
            </div>


            <div class="timkiemnangcao">
                    <div class="boloc">
                        <div class="position-relative">
                            <span class="fs-3"><i class="fa-solid fa-filter boloc_icon" id="filter-icon"></i></span>
                            <div class="filter_loc position-absolute text-bg-light end-md-100 end-0 rounded-1">
                                <form action="/webbanquanao/index.php" method="GET">
                                <input type="hidden" name="page" value="aokhoac">
                                    <div class="p-3">
                                        <p class="mb-2">Bộ lọc</p>
                                        <p class="mb-2">Màu : </p>
                                        <div class="px-2">
                                            <div class="row gap-3 justify-content-center">
                                                <div class="col-2 border selectable color-option" data-color-id="1" style="height: 35px;width: 35px;background-color:#282A2B ;" title="Đen"></div>
                                                <div class="col-2 border selectable color-option" data-color-id="2" style="height: 35px;width: 35px;background-color:#DBD1BC ;" title="Be"></div>
                                                <div class="col-2 border selectable color-option" data-color-id="3" style="height: 35px;width: 35px;background-color:#90713B ;" title="Nâu"></div>
                                                <div class="col-2 border selectable color-option" data-color-id="4" style="height: 35px;width: 35px;background-color:#9FA9A9 ;" title="Xám nhạt"></div>
                                                <div class="col-2 border selectable color-option" data-color-id="5" style="height: 35px;width: 35px;background-color:#D07771 ;" title="Hồng nhạt"></div>
                                                <div class="col-2 border selectable color-option" data-color-id="6" style="height: 35px;width: 35px;background-color:#95987B ;" title="Xanh rêu"></div>
                                                <div class="col-2 border selectable color-option" data-color-id="7" style="height: 35px;width: 35px;background-color:#4F5C7C ;" title="Xanh biển đậm"></div>
                                                <div class="col-2 border selectable color-option" data-color-id="8" style="height: 35px;width: 35px;background-color:#F5F1E6 ;" title="Trắng"></div>
                                                <div class="col-2 border selectable color-option" data-color-id="9" style="height: 35px;width: 35px;background-color:#A5051D ;" title="Đỏ"></div>
                                                <div class="col-2 border selectable color-option" data-color-id="10" style="height: 35px;width: 35px;background-color:#59564F ;" title="Olive"></div>
                                                <div class="col-2 border selectable color-option" data-color-id="11" style="height: 35px;width: 35px;background-color:#387EA0 ;" title="Xanh biển nhạt"></div>
                                                <div class="col-2 border selectable color-option" data-color-id="12" style="height: 35px;width: 35px;background-color:#3C4252 ;" title="Navy"></div>
                                                <div class="col-2 border selectable color-option" data-color-id="13" style="height: 35px;width: 35px;background-color:#391D2B ;" title="Rượu vang"></div>
                                                <div class="col-2 border selectable color-option" data-color-id="14" style="height: 35px;width: 35px;background-color:#B58F6C ;" title="Be đậm"></div>
                                            </div>
                                        </div>
                                        <p class="my-2">
                                            Size : 
                                        </p>
                                        <div class=" ps-4">
                                            <div class="row text-center gap-3">
                                                <div class="bg-white col-2 border d-flex align-items-center justify-content-center selectable size-option" title="XS" style="height:35px; width: 45px;">
                                                    <p class="mb-0">XS</p>
                                                </div>
                                                <div class="bg-white col-2 border d-flex align-items-center justify-content-center selectable size-option" title="S" style="height:35px; width: 45px;">
                                                    <p class="mb-0">S</p>
                                                </div>
                                                <div class="bg-white col-2 border d-flex align-items-center justify-content-center selectable size-option" title="M" style="height:35px; width: 45px;">
                                                    <p class="mb-0">M</p>
                                                </div>
                                                <div class="bg-white col-2 border d-flex align-items-center justify-content-center selectable size-option" title="L" style="height:35px; width: 45px;">
                                                    <p class="mb-0">L</p>
                                                </div>
                                                <div class="bg-white col-2 border d-flex align-items-center justify-content-center selectable size-option" title="XL" style="height:35px; width: 45px;">
                                                    <p class="mb-0">XL</p>
                                                </div>
                                                <div class="bg-white col-2 border d-flex align-items-center justify-content-center selectable size-option" title="2XL" style="height:35px; width: 45px;">
                                                    <p class="mb-0">2XL</p>
                                                </div>
                                            </div>
                                        </div>

                                        <p class="my-2">
                                            <label for="selectTheloai">Thể loại : </label>
                                        </p>
                                        <div class="mt-1">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <select name="selectTheloai" id="selectTheloai" class="form-select">
                                                        <option value="">Chọn thể loại</option>
                                                        <option value="Áo">Áo</option>
                                                        <option value="Quần">Quần</option>
                                                        <option value="Áo Sơmi">Áo Sơmi</option>
                                                        <option value="Áo Polo">Áo Polo</option>
                                                        <option value="Áo khoác">Áo khoác</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
        
                                        <div class="mt-3">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="input-group">
                                                        <span class="input-group-text rounded-start-1" style="width: 80px;">Giá min : </span>
                                                        <input type="text" name="giamin" class="form-control rounded-end-1 form-control-sm">
                                                    </div>
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <div class="input-group">
                                                        <span class="input-group-text rounded-start-1" style="width: 80px;">Giá max : </span>
                                                        <input type="text" name="giamax" class="form-control rounded-end-1 form-control-sm">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
        
                                        <div class="mt-3">
                                            <div class="row justify-content-center">
                                                <div class="col-3">
                                                    <button class="btn btn-light btn-sm d-flex justify-content-center align-items-center border border-3" type="submit" style="width:80px; height: 38px;">
                                                        <span>Lọc</span>
                                                    </button>
                                                </div>
                                                <div class="col-3">
                                                    <button class="btn btn-dark btn-sm d-flex justify-content-center align-items-center border" type="reset" style="width:80px; height: 38px;">
                                                        <span>Xóa</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>      
                        </div>
                    </div>
            </div>

            <div class="sort-box ms-4">
                <div class="position-relative">
                    <span class="fs-3">
                        <i class="fa-solid fa-bars-staggered" id="sort-icon"></i>
                    </span>
            
                    <div class="xacdinhZ_max sort-menu position-absolute text-bg-light end-100 rounded-1" id="sort-menu" style="width: 200px;">
                        <div class="p-3">
                            <p class="mb-0 fw-bold">Sắp xếp theo</p>
                            <a href="?page=aokhoac&sapxep=giamdan">
                             <button class="btn btn-outline-secondary btn-sm mt-2 fs-6 w-100">
                                <span class="me-2"><i class="fa-solid fa-arrow-trend-down"></i></span> Giá giảm dần
                            </button>
                            </a>
                            <br>
                            <a href="?page=aokhoac&sapxep=tangdan">
                            <button class="btn btn-outline-secondary btn-sm mt-2 fs-6 w-100">
                                <span class="me-2"><i class="fa-solid fa-arrow-trend-up"></i></span> Giá tăng dần
                            </button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            

        </div>

    </div>

</section>';
    ?>



    <!-- phần product -->
    <?php

    $connection = mysqli_connect("localhost","root","","db_web_quanao");
    if(!$connection)
    {
        echo 'Không kết nối được với database';
        exit();
    }

    //Sắp xếp tăng,giảm
    $sort = isset($_GET['sapxep']) ? $_GET['sapxep'] : '';
    $qrySort = '';
    if($sort == 'tangdan')
    {
        $qrySort = 'ORDER BY price ASC';
    }else
    {
        $qrySort = 'ORDER BY price DESC';
    }

    // Phân trang
    mysqli_set_charset($connection, 'utf8');

    $limit = 8;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;

    $offset = ($page - 1) * $limit;






    // Tìm kiếm nâng cao
    // Lọc dữ liệu từ GET
$where = ["products.category_id = 5"];

if (!empty($_GET['selectTheloai'])) {
    $theloai = mysqli_real_escape_string($connection, $_GET['selectTheloai']);
    // Giả sử bạn có thể lấy ID từ tên loại
    $categoryQuery = mysqli_query($connection, "SELECT category_id FROM categories WHERE name LIKE '%$theloai%'");
    if ($row = mysqli_fetch_assoc($categoryQuery)) {
        $catId = $row['category_id'];
        $where[] = "products.category_id = $catId";
    }
}

if (!empty($_GET['giamin'])) {
    $giamin = (int)$_GET['giamin'];
    $where[] = "products.price >= $giamin";
}
if (!empty($_GET['giamax'])) {
    $giamax = (int)$_GET['giamax'];
    $where[] = "products.price <= $giamax";
}

// Xử lý lọc màu (danh sách id)
if (!empty($_GET['colors'])) {
    $color_ids = array_map('intval', $_GET['colors']);
    $color_str = implode(",", $color_ids);
    $where[] = "product_variants.color_id IN ($color_str)";
}

// Xử lý lọc size
if (!empty($_GET['sizes'])) {
    $sizes = array_map(function ($s) use ($connection) {
        return "'" . mysqli_real_escape_string($connection, $s) . "'";
    }, $_GET['sizes']);
    $size_str = implode(",", $sizes);
    $where[] = "product_variants.size IN ($size_str)";
}

$filterQuery = "";
if (count($where) > 0) {
    $filterQuery = "WHERE " . implode(" AND ", $where);
}

    // Đếm tổng số sản phẩm sau khi lọc
$quryDemSL = "SELECT COUNT(DISTINCT products.product_id) AS total 
FROM products 
JOIN product_variants ON products.product_id = product_variants.product_id 
$filterQuery";
$demRs = mysqli_query($connection, $quryDemSL);
$totalRow = mysqli_fetch_assoc($demRs);
$totalSach = $totalRow['total'];
$totalPage = ceil($totalSach / $limit);

    // Truy vấn danh sách sách với LIMIT
    $strSQL = "SELECT * FROM products 
    JOIN product_variants ON products.product_id = product_variants.product_id 
    $filterQuery $qrySort 
    LIMIT $limit OFFSET $offset";
    $result = mysqli_query($connection, $strSQL);



    echo ' 
    <section>
    <div class="container-md">
    <div class="row">';
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
                                <div class="mt-2 p-2 pt-1">
                                    <div class="">
                                        <p class="mb-0 fw-lighter">Nam</p>
                                        <p class="mb-0">' . $gia . '</p>   
                                        <p class="mb-0">' . $name . ' VNĐ</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                </div>
    ';

    }

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




    mysqli_close($connection);
    ?>

    <?php
    echo '
    </div>
    </div>
    </section>';
    ?>









    <!-- footer -->

    <?php
    echo '    <script src="/Webbanquanao/assets/js/xulyFIlter.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
    </html>';
    ?>