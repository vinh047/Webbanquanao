<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý cửa hàng</title>
    <link rel="icon" type="./Images/png" href="/assets/img/logo_favicon/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../../assets/fonts/font.css">
    <link rel="stylesheet" href="./assets/css/sanpham.css"> 
    <?php
    require_once(__DIR__ . '/../../database/DBConnection.php');
    $db = DBConnect::getInstance();
        $categories = $db->select("SELECT * FROM categories", []);
        $product = $db->select("SELECT products.*, categories.name as category_name FROM products JOIN categories ON products.category_id = categories.category_id ORDER BY products.product_id ASC", []);
        ?>
</head>
<body>
<section class="py-3">
                <div class="boloc ms-5 position-relative">
                    <span class="fs-3"><i class="fa-solid fa-filter filter-icon" title="Lọc biến thể"></i> <span class="fs-5">Lọc danh sách sản phẩm</span> </span>
                    <div class="filter-loc position-absolute bg-light p-2 rounded-2 d-none" style="width:270px;">
                        <form action="" method="POST" id="formLoc">
                            <label for="txtIDSP">Mã sản phẩm : </label>
                            <input type="text" name="txtIDSP" id="txtIDSP" class="form-control form-control-sm">
                            <label for="txtTensp">Tên sản phẩm</label>
                            <input type="text" name="txtTensp" id="txtTensp" class="form-control form-control-sm">
                            <label for="cbTheLoai">Thể loại : </label>
                            <select name="cbTheLoai" id="cbTheLoai" class="form-select">
                                <option value="">Chọn thể loại</option>
                                <?php foreach($categories as $theloai): ?>
                                <option value="<?=$theloai['category_id']?>"><?=$theloai['name']?></option>
                                <?php endforeach ?>
                            </select>
                            <div class="d-flex gap-2">
                                <div class="">
                                <label for="txtGiaMin">Giá min : </label>
                                <input type="text" name="txtGiaMin" id="txtGiaMin" class="form-control form-control-sm">
                                </div>
                                <div class="">
                                <label for="txtGiaMax">Giá max : </label>
                                <input type="text" name="txtGiaMax" id="txtGiaMax" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="d-flex justify-content-center gap-2 pt-2">
                                <button class="btn btn-primary" style="width:70px;" type="submit">Lọc</button>
                                <button class="btn btn-danger"  style="width:70px;" type="reset">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>


            <div class="sanpham py-3" style="font-size: 19px;display:none;">


                <form action="../ajax/insertSanPham.php" method="POST" id="formNhapSP">
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
                <table class="table table-secondary table-striped table-sm">
                    <thead>
                        <tr class="text-center">
                            <th class="bg-secondary text-white hienthiid">ID</th>
                            <th class="bg-secondary text-white tensp">Tên sản phẩm</th>
                            <th class="bg-secondary text-white hienthiloai">Loại</th>
                            <th class="bg-secondary text-white mota">Mô tả Sản phẩm</th>
                            <th class="bg-secondary text-white hienthigia">Giá nhập</th>
                            <th class="bg-secondary text-white hienthigia">Giá bán</th>
                            <th class="bg-secondary text-white hienthibtn">Xử lý</th>
                        </tr>
                    </thead>
                    <tbody id="product-list">

                    </tbody>    
                    
                </table>
            </div>
            
            <div id="pagination"></div>


        </div>


        <div class="thongbaoLoi  bg-danger me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
            </p>
        </div>

        <div class="thongbaoThanhCong  bg-success me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
            </p>
        </div>

        <div class="thongbaoUpdateThanhCong  bg-success me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
                Cập nhật thông tin thành công
            </p>
        </div>

        <div class="thongbaoXoaThanhCong  bg-success me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
                Xóa sản phẩm thành công
            </p>
        </div>
        <div class="thongbaoXoaKhongThanhCong  bg-danger me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
                Xóa sản phẩm thất bại
            </p>
        </div>

        <div class="thongBaoLoiGia   bg-success me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
                Giá sản phẩm không hợp lệ
            </p>
        </div>
        <div class="overlay"></div>
        <div class="thongbaoXoaThatBai  bg-danger me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
                Xóa biến thể thất bại
            </p>
            </div>
        <div class="thongBaoXoa rounded-2">
            <p class="mb-0 fs-5 text-center">
                Bạn có chắc chắn muốn xóa hay không?       
            </p>
            <div class="d-flex justify-content-center gap-3 mt-2">
                <div class="">
                <button class="btn btn-danger" style="width:80px;">Có</button>
                </div>
                <div class="">
                <button class="btn btn-primary" style="width:80px;">Không</button>

                </div>
            </div>
        </div>    
        
        <div class="thongBaoGia rounded-2">
            <p class="mb-0 fs-5 text-center">
                Giá bán đang nhỏ hơn giá nhập!     
            </p>
            <p class="mb-0 fs-5 text-center">
                Bạn có chắc chắn không? 
            </p>
            <div class="d-flex justify-content-center gap-3 mt-2">
                <div class="">
                <button class="btn btn-danger btn-xacnhan-gia" style="width:80px;">Có</button>
                </div>
                <div class="">
                <button class="btn btn-primary btn-khong-gia" style="width:80px;">Không</button>

                </div>
            </div>
        </div>   
        
        <div class="formSua border container-md p-3">
            <div class="" style="font-size: 17px;">
                <p class="mb-0 text-center fs-4">Sửa thông tin sản phẩm</p>
                <form action="../ajax/updateSanPham.php" method="POST" id="formSua">
    <div class="">
        <label for="txtId">ID sản phẩm : </label>
        <!-- name phải là 'id' -->
        <input type="text" class="form-control" name="id" id="txtId" readonly>
    </div>      

    <div class="py-3">
        <label for="txtTen">Tên sản phẩm : </label>
        <input type="text" name="ten" id="txtTenSua" class="form-control">
    </div>

    <div class="pt-3">
        <label for="txtMota">Mô tả sản phẩm : </label>
        <textarea name="mota" id="txtMotaSua" class="form-control"></textarea>
    </div>

    <div class="pt-3">
        <label for="cbLoai">Loại sản phẩm : </label>
        <select name="loai" id="cbLoaiSua" class="form-select">
            <option value="">Chọn loại sản phẩm</option>
            <?php foreach($categories as $loai): ?>
                <option value="<?=$loai['category_id']?>"><?=$loai['name']?></option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="pt-3">
        <label for="txtGia">Giá sản phẩm : </label>
        <input type="text" name="gia" id="txtGiaSua" class="form-control">
    </div>

    <div class="pt-3">
        <label for="txtPttg">Phần trăm tăng giá : </label>
        <input type="text" name="pttg" id="txtPttg" class="form-control">
    </div>

    <div class="pt-3">
        <label for="txtGiaBanSua">Giá sản phẩm : </label>
        <input type="text" name="giaban" id="txtGiaBanSua" class="form-control">
    </div>

                    
                    <div class="pt-3 d-flex justify-content-center gap-3">
                        <div class="">
                            <button class="btn btn-success" style="width:80px;">Xác nhận</button>
                        </div>
                        <div class="">
                            <button class="btn btn-danger" style="width:80px;">Hủy</button>
                        </div>
                    </div>
            </form>

                
            </div>
            </div>
            </div>

    </section>




    <script src="./assets/js/xulyFormNhapSanPham.js"></script>
</body>
</html>