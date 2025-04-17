<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý cửa hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../../assets/fonts/font.css">
    <link rel="stylesheet" href="./assets/css/sanpham.css">
    <?php
    require_once(__DIR__ . '/../../database/DBConnection.php');
    $db = DBConnect::getInstance();
    $color = $db->select("SELECT * FROM colors",[]);
    $size = $db->select("SELECT * FROM sizes ORDER BY size_id ASC",[]);
    ?>
</head>
<body>


            <div class="sanpham py-3" style="font-size: 19px;">

                <div class="row">
                    <div class="col-md-4">
                <form action="./ajax/insertCTPhieuNhap.php" method="POST" id="formNhapSPbienThe" enctype="multipart/form-data">

                    <div class="pt-3">
                        <label for="txtMaPN">Mã phiếu nhập : </label>
                        <input type="text" name="txtMaPN" id="txtMaPN" placeholder="Mã của sản phẩm" class="form-control ">
                    </div>

                    <div class="pt-3">
                        <label for="txtMa">Mã sản phẩm : </label>
                        <input type="text" name="txtMa" id="txtMa" placeholder="Mã của sản phẩm" class="form-control ">
                    </div>
    
                    <div class="pt-3 pb-2">
                        <label for="fileAnh">Hình ảnh : </label>
                        <input type="file" name="fileAnh" id="fileAnh" class="form-control">
                        <div class="pt-2" style="max-width:170px;max-height: 200px;" id="hienthianh">
                            <img src="" alt="" class="img-fluid" style="width: 170px; height: 200px; object-fit: contain; display: none;">
                        </div>
                    </div>
    
                    <div class="">
                        <label for="cbMau">Màu : </label>
                        <select name="cbMau" id="cbMau" class="form-select">
                            <option value="">Chọn màu sản phẩm</option>
                            <?php foreach($color as $cl): ?>

                                <option value="<?=$cl['color_id']?>"><?=$cl['name']?></option>


                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="pt-3">
                        <label for="cbSize">Size : </label>
                        <select name="cbSize" id="cbSize" class="form-select ">
                            <option value="">Chọn size sản phẩm</option>
                            <?php foreach($size as $s): ?>
                            <option value="<?=$s['size_id']?>"><?=$s['name']?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
    
                    <div class="pt-3">
                        <label for="txtSl">Số lượng sản phẩm : </label>
                        <input type="text" name="txtSl" id="txtSl" class="form-control " placeholder="Số lượng của sản phẩm">
                    </div>

    
                    <div class="pt-3 d-flex gap-2 justify-content-center">
                        <button class="btn btn-outline-secondary" id="add_product" type="button">Thêm CT phiếu nhập</button>
                        <button class="btn btn-outline-primary"  type="submit">Lưu CT phiếu nhập</button>
                        <button class="btn btn-outline-success" id="block_product" type="button">Mở khóa</button>

                    </div>
                </form>
                    </div>

                <!-- Hiển thị sản phẩm trong hàng đợi -->
                    <div class="col-md-8">
                    <div class="hienthi-tamluu pt-3">
                <div class="d-flex justify-content-center border border-3 border-bottom-0 p-2 bg-light">
                            <p class="mb-0 fs-3">
                                Xử lý hàng chờ chi tiết phiếu nhập
                            </p>
                        </div>
                    <table class="table table-dark table-striped table-sm">
                        <thead>
                        <tr class="text-center">
                            <th class="hienthiid">STT</th>
                            <th class="hienthiid">ID PN</th>
                            <th class="hienthiid">ID SP</th>
                            <th class="hienthianh">Hình ảnh</th>
                            <th class="hienthisize">Size</th>
                            <th class="hienthigia">Số lượng</th>
                            <th class="hienthimau">Màu</th>
                            <th class="hienthibtn-ne">Xử lý</th>
                        </tr>
                        </thead>
                        <tbody id="product-list-tamluu">
                        </tbody>
                    </table>
                </div>
                    </div>
                </div>

                                <hr class="my-5">

                                <!-- Hiện thông tin phiếu nhập -->
                    <div class="hienthi">
                        <div class="d-flex justify-content-center border border-3 border-bottom-0 p-2 bg-light">
                            <p class="mb-0 fs-3">
                                Xử lý chi tiết phiếu nhập
                            </p>
                        </div>
                    <table class="table table-dark table-striped table-sm">
                        <thead>
                            <tr class="text-center">
                                <th class="hienthiid">ID CTPN</th>
                                <th class="hienthiid">ID PN</th>
                                <th class="hienthiid">ID SP</th>
                                <th class="hienthiid">ID BT</th>
                                <th class="hienthigia">Tổng tiền</th>
                                <th class="tensp">Ngày lập</th>
                                <th class="hienthibtn-ne">Xử lý</th>
                            </tr>
                        </thead>
                        <tbody id="product-list">
                        </tbody>
                    </table>
                </div>  
                <div id="pagination"></div>


            </div>
        </div>

        <div class="formSua border container-md p-4">
            <div class="" style="font-size: 16px;">
            <p class="mb-0 text-center fs-4">Sửa thông tin sản phẩm</p>

            <form action="" method="POST" id="formNhapSPbienThe" enctype="multipart/form-data">

                    <div class="">
                        <label for="txtSTT">Số thứ tự : </label>
                        <input type="text" name="txtSTT" id="txtSTT" placeholder="Mã của sản phẩm" class="form-control ">
                    </div>

                    <div class="d-flex">
                    <div class="pt-3 me-auto">
                        <label for="txtMaPNSua">Mã phiếu nhập : </label>
                        <input type="text" name="txtMaPNSua" id="txtMaPNSua" placeholder="Mã của sản phẩm" class="form-control ">
                    </div>

                    <div class="pt-3">
                        <label for="txtMa">Mã sản phẩm : </label>
                        <input type="text" name="txtMaSua" id="txtMaSua" placeholder="Mã của sản phẩm" class="form-control ">
                    </div>
                    </div>
    
                    <div class="pt-3 pb-2">
                        <label for="fileAnhSua">Hình ảnh : </label>
                        <input type="file" name="fileAnhSua" id="fileAnhSua" class="form-control">
                        <div class="d-flex">
                        <div class="pt-2" style="max-width:170px;max-height: 200px;" id="hienthianhSua">
                            <img src="" alt="" class="img-fluid" style="width: 170px; height: 200px; object-fit: contain; display: none;">
                        </div>
                        <div id="tenFileAnhSua" class="text-muted small fst-italic mt-1 ms-2"></div>
                        </div>
                    </div>
    
                    <div class="d-flex">
                    <div class="pt-3 me-auto">
                        <label for="cbSizeSua">Size : </label>
                        <select name="cbSizeSua" id="cbSizeSua" class="form-select ">
                            <option value="">Chọn size sản phẩm</option>
                            <?php foreach($size as $s): ?>
                            <option value="<?=$s['size_id']?>"><?=$s['name']?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="pt-3">
                        <label for="cbMau">Màu : </label>
                        <select name="cbMauSua" id="cbMauSua" class="form-select">
                            <option value="">Chọn màu sản phẩm</option>
                            <?php foreach($color as $cl): ?>

                                <option value="<?=$cl['color_id']?>"><?=$cl['name']?></option>


                            <?php endforeach ?>
                        </select>
                    </div>
                    </div>
    
                    <div class="pt-3">
                        <label for="txtSlSua">Số lượng sản phẩm : </label>
                        <input type="text" name="txtSlSua" id="txtSlSua" class="form-control " placeholder="Số lượng của sản phẩm">
                    </div>

                    <div class="d-flex pt-3 gap-3">
                        <button type="button" id="btn_add_product_sua" class="btn btn-outline-secondary">Xác nhận sửa</button>
                        <button class="btn btn-outline-primary" type="button">Đóng</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="formSuaPN border container-md p-4">
        <p class="mb-0 text-center fs-4">Sửa thông tin sản phẩm</p>
            <div class="" style="font-size: 16px;">
            <form action="./ajax/updateCTPhieuNhap.php" id="formSuaPN"> 
                    <!-- Chọn nhà cung cấp -->
                    <div class="d-flex">
                    <div class="pt-3 me-auto">
                        <label for="txtMaCTPNsua">Mã CTPN: </label>
                        <input type="text" name="txtMaCTPNsua" id="txtMaCTPNsua" readonly class="form-control">
                    </div>
                    <div class="pt-3">
                        <label for="txtMaPNsua">Mã PN: </label>
                        <input type="text" name="txtMaPNsua" id="txtMaPNsua"  class="form-control">
                    </div>
                    </div>
                    <div class="d-flex">
                    <div class="pt-3 me-auto">
                        <label for="txtMaSPsua">Mã SP: </label>
                        <input type="text" name="txtMaSPsua" id="txtMaSPsua"  class="form-control">
                    </div>

                    <div class="pt-3">
                        <label for="txtMaBTsua">Mã BT: </label>
                        <input type="text" name="txtMaBTsua" id="txtMaBTsua"  class="form-control">
                    </div>
                    </div>

                    <div class="pt-3">
                        <label for="txtSlsuaTon">Số lượng: </label>
                        <input type="text" name="txtSlsuaTon" id="txtSlsuaTon"  class="form-control">
                    </div>

                    <div class="pt-3">
                        <label for="txtGiaNhap">Giá nhập ban đầu: </label>
                        <input type="text" name="txtGiaNhap" id="txtGiaNhap" readonly class="form-control">
                    </div>

                    <div class="pt-3">
                        <label for="txtTongGT">Tổng giá trị: </label>
                        <input type="text" name="txtTongGT" id="txtTongGT" class="form-control" readonly >
                    </div>

                    <div class="pt-3">
                        <label for="txtNgayLap">Ngày lập: </label>
                        <input type="text" name="txtNgayLap" id="txtNgayLap" class="form-control" readonly >
                    </div>

                    <div class="d-flex pt-3 gap-3">
                        <button type="button" id="btn_sua_pn" class="btn btn-outline-secondary">Xác nhận sửa</button>
                        <button class="btn btn-outline-primary" id="btn_dong" type="button">Đóng</button>
                    </div>
                </form>
            </div>
        </div>

      
        <div class="thongBaoXoa rounded-2">
    <p class="mb-0 fs-5 text-center">
        Bạn có chắc chắn muốn xóa CT phiếu nhập hay không?       
    </p>
    <div class="d-flex justify-content-center gap-3 mt-2">
        <div>
            <button class="btn btn-danger" style="width:80px;">Có</button>
        </div>
        <div>
            <button class="btn btn-primary" style="width:80px;">Không</button>
        </div>
    </div>
</div>
        <div class="thongbaoUpdateThanhCong  bg-success me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
                Cập nhật thông tin thành công
            </p>
        </div>
        <div class="thongbaoUpdateKhongThanhCong  bg-danger me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
                Cập nhật thông tin thất bại
            </p>
        </div>
<div class="thongbaoXoaThanhCong bg-success me-3 mt-3 p-3 rounded-2">
    <p class="mb-0 text-white">       
    Xóa CT phiếu nhập thành công
    </p>
</div>
        <div class="thongbaoXoaKhongThanhCong  bg-danger me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
                Xóa CT phiếu nhập thất bại
            </p>
        </div>

        <div class="thongbaoLuuKhongThanhCong  bg-danger me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
                Lưu chi tiết phiếu nhập thất bại
            </p>
        </div>
        <div class="thongbaoLuuThanhCong bg-success me-3 mt-3 p-3 rounded-2">
    <p class="mb-0 text-white">       
    Lưu chi tiết phiếu nhập thành công
    </p>
</div>
<div class="overlay"></div>
<div class="thongbaoLoi  bg-danger me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
            </p>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <script src="./assets/js/fetch_ctphieunhap.js"></script>
</body>
</html>