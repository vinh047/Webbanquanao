<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/product_detail.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">

</head>
<body>
    

<?php 
    require_once '../database/DBConnection.php';
    $db = DBConnect::getInstance();

    $product = $db->selectOne("SELECT * FROM products WHERE product_id = ?", [6]);

    $category = $db->selectOne("SELECT * FROM categories WHERE category_id = ?", [$product['category_id']]);

    $product_variants = $db->select("SELECT * FROM product_variants WHERE product_id = ?", [$product['product_id']]);

    $suggest_products = $db->select("SELECT * FROM products WHERE category_id = ? LIMIT 10", [$product['category_id']]);


    function formatToK($number) {
        if ($number < 1000) {
            return $number;
        }
    
        // Chia cho 1000 và làm tròn đến 1 chữ số thập phân
        $short = $number / 1000;
    
        // Nếu có phần thập phân, giữ 1 số sau dấu chấm
        $formatted = number_format($short, ($short - floor($short) > 0 ? 1 : 0));
    
        return $formatted . 'k';
    }

    function getColorById($color_id) {
        global $db;
        return $db->selectOne("SELECT * FROM colors WHERE color_id = ?", [$color_id]);
    }

    

?>

    <?php include 'header.php' ?>
    <div class="wrap py-4">
        <div class="container">
            <!-- Đường dẫn tới chi tiết sản phẩm -->
            <div class="mb-3 d-flex align-items-center justify-content-center">
    
                <a class="fs-6 pb-1 underline-animate" style="cursor: pointer; text-decoration: none;" href="#">sagkuto</a>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" style="width: 12px; height: 12px;" class="mx-2"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/></svg>
                
                <a class="fs-6 pb-1 underline-animate" style="cursor: pointer; text-decoration: none;" href="#"><?= $category['name']; ?></a>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" style="width: 12px; height: 12px;" class="mx-2"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/></svg>
                
                <p class="m-0"><?= $product['name']; ?></p>
            
            </div>
    
            <!-- Chi tiết sản phẩm -->
            <div>
                <div class="row border bg-white">
                    <div class="col-md-5  p-xl-4 p-md-2  h-auto d-flex justify-content-center">
                        <img class="img-main img-fluid object-fit-contain" src="https://down-vn.img.susercontent.com/file/f8c32b5b019a4c6b9d56a9950d481830@resize_w450_nl.webp" alt="Sản phẩm" class="img-fluid object-fit-contain"></img>
    
                    </div>
        
                    <div class="col-md-7">
                        <div class="p-xl-4 p-md-2 ps-sm-4 mt-2 mt-md-0">
                            <!-- Tên của sản phẩm -->
                            <div class="fs-3 fs-md-3"><?= $product['name']; ?></div>
            
                            <!-- Đánh giá sao + số lượt đánh giá + số lượt bán-->
                            <div class="d-inline-flex justify-content-center align-items-stretch ms-1">
                                <!-- Đánh giá -->
                                <div class="d-inline-flex justify-content-center align-items-center gap-1 border-end pe-3">
                                    <p class="m-0 fs-5"><?= $product['rating_avg'] ?></p>
            
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" style="width: 12px;"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                        <path fill="#FFD43B" 
                                        d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"/></svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" style="width: 12px;"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                        <path fill="#FFD43B" 
                                        d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"/></svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" style="width: 12px;"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                        <path fill="#FFD43B" 
                                        d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"/></svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" style="width: 12px;"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                        <path fill="#FFD43B" 
                                        d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"/></svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" style="width: 12px;"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                        <path fill="#FFD43B" 
                                        d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"/></svg>
                                    
                                </div>
            
                                <!-- Số lượt đánh giá -->
                                <p class="m-0 px-3 border-end"><?= formatToK($product['rating_count']); ?> Đánh Giá</p>
        
                                <p class="m-0 px-3"><?= formatToK($product['sold_count']); ?> Lượt Bán</p>
            
                            </div>
        
                            <!-- Giá -->         
                            <div class="fs-4 fw-medium mt-2" style="font-size: 16px;"><?= number_format($product['price']); ?>đ</div>
        
                            <div class="row gap-1">
                                <!-- Màu -->
                                <div class="mt-4 d-flex col-12 align-items-center">
                                    <p class="lb m-0 text-secondary">Màu Sắc</p>
            
                                    <!--  -->
                                    <div class="d-flex flex-wrap gap-3">
                                        <?php 
                                        foreach($product_variants as $variant):
                                            $color = getColorById($variant['color_id']);
                                        ?>
                                            <div class="color-option border px-2 py-1">
                                                <img class="object-fit-contain" src="<?= '../assets/img/sampham/' . $variant['image'] ?>" alt="<?= 'Ảnh '. $product['name'] . ' màu ' . $color['name'] ?>" 
                                                width="25" height="25">
                                                <span><?= $color['name'] ?></span>
                                            </div>
                                            
                                        <?php endforeach; ?>
                                        <!-- <div class="color-option border px-2 py-1">
                                            <img class="object-fit-contain" src="https://evashopping.vn/public/storage/editor/thumbs/3973/non-nua-dau-vanh-rong-chong-nang-n38-h1.webp" alt="" 
                                            width="25" height="25">
                                            <span>Đen</span>
        
                                            
                                        </div> -->
            
                                    </div>
            
                                </div>
            
                                <!-- Size -->
                                <div class="mt-4 d-flex col-12 align-items-center">
                                    <p class="lb m-0 text-secondary">Size</p>
            
                                    <!--  -->
                                    <div class="d-flex gap-3 flex-wrap">
                                        <?php foreach($product_variants as $variant): ?>
                                            <div class="size-option border py-2 px-3"><?= $variant['size'] ?></div>
                                        <?php endforeach; ?>
                                            
                                        <!-- <div class="size-option border py-2 px-3">M</div> -->
                                    </div>
                                </div>
            
            
                                <!-- Số lượng -->
                                <div class="mt-4 d-flex col-12 align-items-center">
                                    <p class="lb m-0 text-secondary pt-2">Số lượng</p>
        
                                    <!--  -->
                                    <div class="d-flex align-items-center border py-1 px-4 gap-2 justify-content-end">
                                        <div class="down text-center">-</div>
                                        <input class="input-qty border-0 text-center" min="1" value="1" type="number" id="count">
                                        <div class="up text-center">+</div>
                                    </div>
                                </div>
        
                                <!-- Nút mua ngay + thêm vào giỏ hàng -->
                                <div class="d-inline-flex row gap-2 mt-4 g-0 pe-sm-4">
        
                                    <a href="#" class="buynow col-12 col-md-5  btn border-black">Mua ngay</a>
        
                                    <p href="#" class="add-to-cart col-12 col-xl-5 col-md-6 bg-black d-flex justify-content-center align-items-center rounded m-md-0" style="cursor: pointer;">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" style="width: 16px; height: 16px;" class="me-2"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="currentColor" d="M0 24C0 10.7 10.7 0 24 0L69.5 0c22 0 41.5 12.8 50.6 32l411 0c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3l-288.5 0 5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5L488 336c13.3 0 24 10.7 24 24s-10.7 24-24 24l-288.3 0c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5L24 48C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"/></svg>
                                        Thêm vào giỏ hàng
        
                                    </p>
                                </div>
    
                                <div class="notice-add-to-cart position-absolute top-50 start-50 d-flex flex-column justify-content-center align-items-center p-5 rounded w-auto opacity-0" style="background-color: rgba(0, 0, 0, 0.8);">
                                    <i class="fa-solid fa-circle-check fa-3x mb-2" style="color: #ffffff;"></i>
                                    <span class="text-white text-center">Đã thêm vào giỏ hàng</span>
                                </div>
        
                            </div>
        
                        </div>
        
        
        
                    </div>
        
        
                    
                </div>
            </div>
    
            <!-- Gợi ý sản phẩm -->
            <div class="row">
                <div class="mt-4 row border bg-white g-0">
    
                    <div class="d-flex justify-content-between align-items-center ps-4 pe-5 pt-3">
                        <h3 class="text-decoration-underline mb-4 pt-3 ps-4">Có thể bạn quan tâm</h3>
                        <a href="#" class="view-all text-primary text-decoration-none text-black d-none d-sm-block" style="font-size: 14px;">
                          Xem tất cả
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="width: 14px; height: 14px;" class="ms-1">
                            <path fill="currentColor" d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/>
                          </svg>
                        </a>
                    </div>  
    
                    <div class="user-select-none">
                        <div class="position-relative">
                            <!-- Nút trái -->
                            <button class="scroll-btn scroll-left" onclick="scrollSuggestProducts(-1)" style="height: 50px; width: 50px;">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" style="width: 14px;"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 246.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"/></svg>
                            </button>
                        
                            <!-- Nút phải -->
                            <button class="scroll-btn scroll-right" onclick="scrollSuggestProducts(1)" style="height: 50px; width: 50px;">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" style="width: 14px;"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/></svg>
                            </button>
                      
                            <!-- Slider sản phẩm -->
                            <div class="suggest-products-scroll d-flex px-2 py-3">
                                <?php foreach($suggest_products as $p): ?>
                                    <div class="product-item border">
                                        <img src="<?= '../assets/img/sanpham/'. $p['image'] ?>" draggable="false" alt="<?= $p['name'] ?>">
                                        <div>
                                            <p class="ellipsis-1-line mt-3 mb-1 px-4 text-decoration-none text-secondary" style="font-size: 15px;"><?= $p['name'] ?></p>
                                            <p><?= number_format($p['price']); ?>đ</p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
    
                                <!-- <div class="product-item border">
                                    <img src="https://evashopping.vn/public/storage/editor/thumbs/4353/bikini-3-manh-dang-vay-di-bien-bkn1086.webp" draggable="false">
                                    <div>
                                        <p class="ellipsis-1-line mt-3 mb-1 px-4 text-decoration-none text-secondary" style="font-size: 15px;">Nón đi biển rộng vành</p>
                                        <p>120.000đ</p>
    
                                    </div>
                                </div> -->
    
                                
                                <!-- Thêm các sản phẩm khác tương tự -->
                            </div>
    
                          
    
    
    
                        </div>
                    </div>
    
                </div>
            </div>
    
     
    
    
            <!-- Mô tả sản phẩm -->
            <div class="mt-4 row">
                <div class="border bg-white py-3 px-4">
                    <h3 class="text-decoration-underline mb-4">Mô tả sản phẩm</h3>
    
                    <p style="white-space: pre-line; "><?= $product['description'] ?></p>
                
                </div>
            </div>
    
           <!-- Đánh giá sản phẩm -->
            <div class="mt-4 row">
                <div class="border bg-white pt-3 ps-4">
                    <h3 class="text-decoration-underline mb-4">Đánh giá sản phẩm</h3>
    
                    <!--  -->
                    <div class="border py-sm-4 py-2 px-2 px-sm-5 d-flex align-items-center flex-sm-row flex-column">
    
                        <div class="d-flex flex-column align-items-center me-sm-5 mb-3 mb-sm-1">
                            <div style="color: #FFD700;" class="fw-medium">
                                <span class="fs-3"><?= $product['rating_avg'] ?></span>
                                <span style="font-size: 1.125rem;"> trên 5</span>
                            </div>
    
                            <div class="star-rating mt-2" style="width: max-content;">
                                <!-- Nền sao xám -->
                                <div class="stars-back">
                                    <i class="fa-regular fa-star" style="color: #FFD43B;"></i>
                                    <i class="fa-regular fa-star" style="color: #FFD43B;"></i>
                                    <i class="fa-regular fa-star" style="color: #FFD43B;"></i>
                                    <i class="fa-regular fa-star" style="color: #FFD43B;"></i>
                                    <i class="fa-regular fa-star" style="color: #FFD43B;"></i>
                                </div>
                                <!-- Sao vàng phía trước được cắt theo phần trăm -->
                                <div class="stars-front" style="width: <?= $product['rating_avg'] / 5 * 100 ?>%;"> <!-- 3.6 sao = 72% -->
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </div>
                            </div>
                        </div>
    
                        <div class="flex-grow-1 d-flex flex-wrap justify-content-sm-start justify-content-center">
                            <div class="btn-star active border d-inline-block me-2 p-2 mb-2" style="cursor: pointer;" data-rating="all">Tất cả</div>
                            <?php
                                $rating_counts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
                                $results = $db->select("SELECT rating, COUNT(*) AS total FROM reviews WHERE product_id = ? GROUP BY rating", [$product['product_id']]);
                                foreach ($results as $row) {
                                    $rating_counts[$row['rating']] = $row['total'];
                                }
                                for ($i = 5; $i >= 1; $i--) {
                                    echo "<div class=\"btn-star border d-inline-block me-2 p-2 mb-2\" style=\"cursor: pointer;\" data-rating=\"$i\">$i sao ($rating_counts[$i])</div>";
                                }
                            ?>
                            
                        </div>
                    </div>
    
                    <!--  -->
                    <div class="row row-cols-12 g-0 pb-3">
    
                        <div class="review-list" data-product-id="<?= $product['product_id'] ?>">
    
                        </div>
    
                        
    
    
                        <!-- Phân trang -->
                        <div class="pagination_wrap">
    
                        </div>
                        
                            
                        
    
                    </div>
    
                </div>
            </div>
    
    
    
        </div>

    </div>

    <?php include 'footer.php' ?>
    

    <script src="../assets/js/product_detail.js"></script>
</body>
</html>