<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý cửa hàng</title>
    <link rel="icon" type="./Images/png" href="/assets/img/logo_favicon/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/webbanquanao/assets/fonts/font.css">
    <link rel="stylesheet" href="/webbanquanao/admin/assets/css/sanpham.css">
    <?php
    require_once('../../database/DBConnection.php');
        $db = DBConnect::getInstance();
        $categories = $db->select("SELECT * FROM categories", []);
        $product = $db->select("SELECT products.*, categories.name as category_name FROM products JOIN categories ON products.category_id = categories.category_id ORDER BY products.product_id ASC", []);
        ?>
</head>
<body>
    

    <section class="d-flex position-relative">


        <nav class="nav-left">
                <ul class="list-group">

                    <li class="list-group-item">
                        <img src="/webbanquanao/assets/img/logo_favicon/logo.png" alt="logo" class="img-fluid" style="height:80px;width:100%;">
                    </li>

                    <li class="list-group-item">
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Quản lý sản phẩm
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Sản phẩm</a></li>
                                <li><a class="dropdown-item" href="#">Biến thể sản phẩm</a></li>
                            </ul>
                        </div>
                        

                    </li>
                </ul>
        </nav>

        <div class="quanlysp container-md">
            <div class="infouser row p-2" style="background-color: #f8f9fa;">
                <div class="col-md text-end">
                    <p class="mb-0 fs-3"><i class="fa-solid fa-user"></i></p>
                </div>
            </div>


            <div class="sanpham py-3" style="font-size: 19px;">


                <form action="/webbanquanao/admin/ajax/insertSanPham.php" method="POST" id="formNhapSP">
                    <div class="">
                        <label for="txtTen">Tên sản phẩm : </label>
                        <input type="text" name="txtTen" id="txtTen" placeholder="Tên của sản phẩm" class="form-control ">
                    </div>
    
                    <div class="pt-3">
                        <label for="txtMota">Mô tả sản phẩm : </label>
                        <textarea name="txtMota" id="txtMota" class="form-control " placeholder="Mô tả"></textarea>
                    </div>
    
                    <div class="pt-3">
                        <label for="cbLoai">Loại sản phẩm : </label>
                        <select name="cbLoai" id="cbLoai" class="form-select ">
                            <option value="">Chọn loại sản phẩm</option>
                            <?php foreach( $categories as $loai ): ?>
                            <option value="<?=$loai['category_id']?>"><?=$loai['name']?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
    
                    <div class="pt-3">
                        <label for="txtGia">Giá sản phẩm : </label>
                        <input type="text" name="txtGia" id="txtGia" class="form-control " placeholder="Giá của sản phẩm">
                    </div>
    
                    <div class="pt-3">
                        <button class="btn btn-outline-primary" type="submit">Thêm sản phẩm</button>
                    </div>
                </form>

            </div>

            <div class="hienthi">
                <table class="table table-dark table-striped table-sm">
                    <thead>
                        <tr class="text-center">
                            <th class="hienthiid">ID</th>
                            <th class="tensp">Tên sản phẩm</th>
                            <th class="hienthiloai">Loại</th>
                            <th class="mota">Mô tả Sản phẩm</th>
                            <th class="hienthigia">Giá</th>
                            <th class="hienthibtn">Xử lý</th>
                        </tr>
                    </thead>
                    <tbody>
        <?php foreach ($product as $ds): ?>
            <tr class="text-center">
                <td><?= $ds['product_id'] ?></td>
                <td><?= $ds['name'] ?></td>
                <td><?= $ds['category_name'] // Loại sản phẩm từ bảng categories ?></td>
                <td><?= $ds['description'] ?></td>
                <td><?= number_format($ds['price'], 0, ',', '.') ?> VNĐ</td>
                <td>
                    <button class="btn btn-success">Sửa</button>
                    <button class="btn btn-danger">Xóa</button>
                </td>
            </tr>
        <?php endforeach; ?>
                </tbody>    
                    
                </table>
            </div>
            

        </div>


        <div class="thongbaoLoi position-absolute top-0 end-0 bg-danger me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
            </p>
        </div>

        <div class="thongbaoThanhCong position-absolute top-0 end-0 bg-success me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
            </p>
        </div>

    </section>




      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
      <script src="/webbanquanao/admin/assets/js/xulyFormNhapSanPham.js"></script>

</body>
</html>