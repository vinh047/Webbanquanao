
<!DOCTYPE html>
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
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=shopping_cart" />
        <?php
            require_once './database/DBConnection.php';
            $db = DBConnect::getInstance();
            $product_color  = $db->select("SELECT * FROM colors",[]);
            $product_theloai = $db->select("SELECT * FROM categories",[]);

        ?>
        <style>
            /* Ẩn mũi tên trên Chrome, Safari, Edge */
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Ẩn mũi tên trên Firefox */
input[type=number] {
    -moz-appearance: textfield;
}
.product-img {
    width: 100%;
    height: 390px;        /* 👈 chỉnh chiều cao tùy ý */
    object-fit: cover;    /* Cắt ảnh nhưng giữ tỷ lệ */
}


        </style>
    </head>





    <!-- bộ lọc -->

    <section class="pt-4 pb-3">

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
                        Sản phẩm
                    </span>
                </p>
            </div>
            <div class="timkiemnangcao">
                    <div class="boloc">
                        <div class="position-relative">
                            <span class="fs-3"><i class="fa-solid fa-filter boloc_icon" id="filter-icon"></i></span>
                            <div class="filter_loc position-absolute text-bg-light end-md-100 end-0 rounded-1">
                                <form action="/webbanquanao/index.php" method="GET">
                                <input type="hidden" name="page" value="sanpham">
                                    <div class="p-3">
                                        <p class="mb-2">Bộ lọc</p>
                                        <p class="mb-2">Màu : </p>
                                        <div class="px-2">
                                            <div class="row gap-3 justify-content-center">                                           
                                            <?php foreach($product_color as $dl):  ?>
                                           
                                           
                                                <div class="col-2 border selectable color-option" data-color-id="<?=$dl['color_id']?>" style="height: 35px;width: 35px;background-color:<?=$dl['hex_code']?> ;" title="<?=$dl['name']?>"></div>


                                            <?php endforeach ?>
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

                                                    <?php foreach($product_theloai as $dl): ?>
                                                        <option value="<?=$dl['category_id']?>"><?=$dl['name']?></option>

                                                    <?php endforeach?>

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
                             <button  class="sort-btn btn btn-outline-secondary btn-sm mt-2 fs-6 w-100" data-sort="giamdan">
                                <span class="me-2"><i class="fa-solid fa-arrow-trend-down"></i></span> Giá giảm dần
                            </button>
                            <br>
                            <button  class="sort-btn btn btn-outline-secondary btn-sm mt-2 fs-6 w-100" data-sort="tangdan">
                                <span class="me-2"><i class="fa-solid fa-arrow-trend-up"></i></span> Giá tăng dần
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="ms-3" style="margin-top: 10px;">
                <a href="/Webbanquanao/layout/cart.php" title="Giỏ hàng" class="text-dark">
                        <span class="material-symbols-outlined" style="font-size: 34px;">
                             shopping_cart
                        </span>
                    </a>
            </div>

        </div>

    </div>

</section>


    <section>
    <div class="container-md h-100">
        <div class="row" id="product-list">
            <!-- Sản phẩm sẽ được load ở đây qua AJAX -->

        </div>


        
    </div>
    </section>











    <!-- footer -->


        <script src="/Webbanquanao/assets/js/ajaxLoc_phantrang.js"></script>        
        <script src="/Webbanquanao/assets/js/xulyFIlter.js"></script>
        <script src="/Webbanquanao/assets/js/addToCart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
    </html>