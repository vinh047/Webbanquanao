<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <title>Tìm kiếm</title>
    <link rel="icon" type="./Images/png" href="../assets/img/logo_favicon/favicon.png">
    <link rel="stylesheet" href="./assets/fonts/font.css">
    <link rel="stylesheet" href="./assets/css/product.css">
    <link rel="stylesheet" href="./assets/css/footer.css">
    <link rel="stylesheet" href="./assets/css/mini_cart.css">

    <?php
    require_once __DIR__ . '/../database/DBConnection.php';
    $keyword = trim($_GET['q'] ?? '');
    $db = DBConnect::getInstance();
    $product_color  = $db->select("SELECT * FROM colors", []);
    $product_theloai = $db->select("SELECT * FROM categories", []);
    $size = $db->select("SELECT * FROM sizes ORDER BY size_id ASC", []);
    ?>
    <style>
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
        #noticeAddToCart {
            transition: opacity 0.3s ease;
            pointer-events: none;
        }
    </style>
</head>

<body>

<!-- bộ lọc -->
<section class="pt-4 pb-3">
    <div class="container-md">
        <div class="border py-2 px-4 d-flex align-items-center">
            <div class="me-auto">
                <p class="mb-0">
                    <a href="index.php" class="text-decoration-none link-primary aHover">Trang chủ</a>
                    <span class="mx-2"><i class="fa-solid fa-angle-right"></i></span>
                    <span class="text-dark">Tìm kiếm</span>
                </p>
            </div>
            <div class="timkiemnangcao">
                <div class="boloc">
                    <div class="position-relative">
                        <span class="fs-3"><i class="fa-solid fa-filter boloc_icon" id="filter-icon"></i></span>
                        <div class="filter_loc position-absolute text-bg-light end-md-100 end-0 rounded-1">
                            <form action="search.php" method="GET">
                                <input type="hidden" name="page" value="timkiem">
                                <div class="p-3">
                                    <p class="mb-2">Tìm kiếm</p>
                                    <div class="px-2">
                                        <div class="row">
                                            <div class="col-md px-1">
                                                <div class="input-group">
                                                    <span class="input-group-text rounded-start-1" style="width: 80px;">Tên SP :</span>
                                                    <input type="text" name="tensp" class="form-control rounded-end-1 form-control-sm">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mb-2">Màu :</p>
                                    <div class="px-2">
                                        <div class="filter-wrap row gap-3 justify-content-center">
                                            <?php foreach ($product_color as $dl): ?>
                                                <div class="col-2 border selectable color-option" data-color-id="<?= $dl['color_id'] ?>" style="height: 35px;width: 35px;background-color:<?= $dl['hex_code'] ?>;" title="<?= $dl['name'] ?>"></div>
                                                <input type="checkbox" name="colors[]" value="<?= $dl['color_id'] ?>" class="d-none color-checkbox">
                                            <?php endforeach ?>
                                        </div>
                                    </div>
                                    <p class="my-2">Size :</p>
                                    <div class="ps-4">
                                        <div class="row text-center gap-3">
                                            <?php foreach ($size as $s): ?>
                                                <div class="bg-white col-2 border d-flex align-items-center justify-content-center selectable size-option" data-size-id="<?= $s['size_id'] ?>" title="<?= $s['name'] ?>" style="height:35px; width: 45px;">
                                                    <p class="mb-0"><?= $s['name'] ?></p>
                                                </div>
                                                <input type="checkbox" name="sizes[]" value="<?= $s['size_id'] ?>" class="d-none size-checkbox">
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <p class="my-2"><label for="selectTheloai">Thể loại :</label></p>
                                    <div class="mt-1">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <select name="selectTheloai" id="selectTheloai" class="form-select">
                                                    <option value="">Chọn thể loại</option>
                                                    <?php foreach ($product_theloai as $dl): ?>
                                                        <option value="<?= $dl['category_id'] ?>"><?= $dl['name'] ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="input-group">
                                                    <span class="input-group-text rounded-start-1" style="width: 80px;">Giá min :</span>
                                                    <input type="text" name="giamin" class="form-control rounded-end-1 form-control-sm">
                                                </div>
                                            </div>
                                            <div class="col-12 mt-2">
                                                <div class="input-group">
                                                    <span class="input-group-text rounded-start-1" style="width: 80px;">Giá max :</span>
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

            <!-- Sắp xếp -->
            <div class="sort-box ms-4">
                <div class="position-relative">
                    <span class="fs-3">
                        <i class="fa-solid fa-bars-staggered" id="sort-icon"></i>
                    </span>

                    <div class="xacdinhZ_max sort-menu position-absolute text-bg-light end-100 rounded-1" id="sort-menu" style="width: 200px;">
                        <div class="p-3">
                            <p class="mb-0 fw-bold">Sắp xếp theo</p>
                            <button class="sort-btn btn btn-outline-secondary btn-sm mt-2 fs-6 w-100" data-sort="giamdan">
                                <span class="me-2"><i class="fa-solid fa-arrow-trend-down"></i></span> Giá giảm dần
                            </button>
                            <br>
                            <button class="sort-btn btn btn-outline-secondary btn-sm mt-2 fs-6 w-100" data-sort="tangdan">
                                <span class="me-2"><i class="fa-solid fa-arrow-trend-up"></i></span> Giá tăng dần
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="container-md h-100">
        <div class="row" id="product-list">
            <!-- Kết quả sản phẩm từ AJAX -->
        </div>
    </div>
</section>

<!-- JS -->
<script src="./assets/js/ajaxLoc_timkiem.js"></script>
<script src="./assets/js/addToCart.js"></script>
<script src="./assets/js/product_color_size.js"></script>
<script src="./assets/js/xulyFIlter.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
