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
        $categories = $db->select("SELECT * FROM categories", []);
        $suppliers = $db->select("SELECT * FROM supplier",[]);
    ?>
</head>
<body> 


<!-- <section class="d-flex position-relative">
    <nav class="nav-left">
        <ul class="list-group">
            <li class="list-group-item">
                <img src="../../assets/img/logo_favicon/logo.png" alt="logo" class="img-fluid" style="height:80px;width:100%;">
            </li>

            <li class="list-group-item">
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Quản lý phiếu nhập
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="index.php?page=phieunhap">Phiếu nhập</a></li>
                        <li><a class="dropdown-item" href="index.php?page=ctphieunhap">Chi tiết phiếu nhập</a></li>
                    </ul>
                </div>

                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Quản lý sản phẩm
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="index.php?page=sanpham">Sản phẩm</a></li>
                        <li><a class="dropdown-item" href="index.php?page=bienthe">Biến thể sản phẩm</a></li>
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
        </div> -->

            <div class="sanpham py-3" style="font-size: 19px;">

                <form action="./ajax/insertPhieuNhap.php" id="formNhapPhieuNhap"> 
                    <!-- Chọn nhà cung cấp -->
                    <div class="pt-3">
                        <label for="supplier_id">Chọn nhà cung cấp: </label>
                        <select name="supplier_id" id="supplier_id" class="form-select">
                            <option value="">Chọn nhà cung cấp</option>
                            <?php foreach($suppliers as $supplier): ?>
                                <option value="<?=$supplier['supplier_id']?>"><?=$supplier['name']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Mã nhân viên -->
                    <div class="pt-3">
                        <label for="user_id">Mã nhân viên: </label>
                        <input type="text" name="user_id" id="user_id" value="3" readonly class="form-control">
                    </div>

                    <!-- Thêm sản phẩm -->
                    <div class="pt-3">
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
                            <?php foreach($categories as $loai): ?>
                                <option value="<?=$loai['category_id']?>"><?=$loai['name']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="pt-3">
                        <label for="txtGia">Giá sản phẩm : </label>
                        <input type="text" name="txtGia" id="txtGia" class="form-control " placeholder="Giá của sản phẩm">
                    </div>

                    <div class="pt-3">
                        <label for="txtPT">Phần trăm giá sản phẩm : </label>
                        <input type="text" name="txtPT" id="txtPT" class="form-control " placeholder="Phần trăm giá sản phẩm" value="30">
                    </div>

                    <div class="d-flex pt-3 gap-3">
                        <button type="button" id="add_product" class="btn btn-outline-secondary">Thêm phiếu nhập</button>
                        <button class="btn btn-outline-primary" type="submit">Lưu phiếu nhập</button>
                    </div>
                </form>

                <!-- Hiển thị sản phẩm trong hàng đợi -->
                <div class="hienthi-tamluu pt-3">
                <div class="d-flex justify-content-center border border-3 border-bottom-0 p-2 bg-light">
                            <p class="mb-0 fs-3">
                                Xử lý hàng chờ phiếu nhập
                            </p>
                        </div>
                    <table class="table table-secondary table-striped table-sm">
                        <thead>
                            <tr class="text-center">
                                <th class="bg-secondary text-white hienthiid">STT</th>
                                <th class="bg-secondary text-white hienthiid">ID NV</th>
                                <th class="bg-secondary text-white hienthiid">ID NCC</th>
                                <th class="bg-secondary text-white tensp">Tên sản phẩm</th>
                                <th class="bg-secondary text-white hienthiloai">Loại</th>
                                <th class="bg-secondary text-white mota">Mô tả</th>
                                <th class="bg-secondary text-white hienthigia">Giá</th>
                                <th class="bg-secondary text-white hienthigia">PT sản phẩm</th>
                                <th class="bg-secondary text-white hienthibtn-ne">Xử lý</th>
                            </tr>
                        </thead>
                        <tbody id="product-list-tamluu">
                        </tbody>
                    </table>
                </div>

                                <hr class="my-5">

                                <!-- Hiện thông tin phiếu nhập -->
                    <div class="hienthi">
                        <div class="d-flex justify-content-center border border-3 border-bottom-0 p-2 bg-light">
                            <p class="mb-0 fs-3">
                                Xử lý phiếu nhập
                            </p>
                        </div>
                    <table class="table table-secondary table-striped table-sm">
                        <thead>
                            <tr class="text-center">
                                <th class="bg-secondary text-white hienthiid">ID PN</th>
                                <th class="bg-secondary text-white hienthiid">ID NV</th>
                                <th class="bg-secondary text-white hienthiid">ID NCC</th>
                                <th class="bg-secondary text-white hienthigia">Tổng tiền</th>
                                <th class="bg-secondary text-white tensp">Ngày lập</th>
                                <th class="bg-secondary text-white hienthibtn-ne">Xử lý</th>
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
            <form action="" id="formSua"> 
                    <!-- Chọn nhà cung cấp -->
                    <div class="pt-3">
                        <label for="stt">Số TT: </label>
                        <input type="text" name="stt" id="stt" readonly class="form-control">
                    </div>
                    <div class="pt-3">
                        <label for="supplier_idSua">Chọn nhà cung cấp: </label>
                        <select name="supplier_idSua" id="supplier_idSua" class="form-select" required>
                            <option value="">Chọn nhà cung cấp</option>
                            <?php foreach($suppliers as $supplier): ?>
                                <option value="<?=$supplier['supplier_id']?>"><?=$supplier['name']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Mã nhân viên -->
                    <div class="pt-3">
                        <label for="user_idSua">Mã nhân viên: </label>
                        <input type="text" name="user_idSua" id="user_idSua" value="3" readonly class="form-control">
                    </div>

                    <!-- Thêm sản phẩm -->
                    <div class="pt-3">
                        <label for="txtTenSua">Tên sản phẩm : </label>
                        <input type="text" name="txtTenSua" id="txtTenSua" placeholder="Tên của sản phẩm" class="form-control ">
                    </div>
                    <div class="pt-3">
                        <label for="txtMotaSua">Mô tả sản phẩm : </label>
                        <textarea name="txtMota" id="txtMotaSua" class="form-control" placeholder="Mô tả"></textarea>
                    </div>
                    <div class="pt-3">
                        <label for="cbLoaiSua">Loại sản phẩm : </label>
                        <select name="cbLoaiSua" id="cbLoaiSua" class="form-select ">
                            <option value="">Chọn loại sản phẩm</option>
                            <?php foreach($categories as $loai): ?>
                                <option value="<?=$loai['category_id']?>"><?=$loai['name']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="pt-3">
                        <label for="txtGiaSua">Giá sản phẩm : </label>
                        <input type="text" name="txtGiaSua" id="txtGiaSua" class="form-control " placeholder="Giá của sản phẩm">
                    </div>

                    <div class="pt-3">
                        <label for="txtPTSua">Phần trăm tăng giá sản phẩm : </label>
                        <input type="text" name="txtPTSua" id="txtPTSua" class="form-control " placeholder="Phần trăm giá sản phẩm" value="30%">
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
            <form action="../ajax/updatePhieuNhap.php" id="formSuaPN"> 
                    <!-- Chọn nhà cung cấp -->
                    <div class="pt-3">
                        <label for="txtMaPNsua">Mã PN: </label>
                        <input type="text" name="txtMaPNsua" id="txtMaPNsua" readonly class="form-control">
                    </div>
                    <div class="pt-3">
                        <label for="supplier_idSuaPN">Chọn nhà cung cấp: </label>
                        <select name="supplier_idSuaPN" id="supplier_idSuaPN" class="form-select" required>
                            <option value="">Chọn nhà cung cấp</option>
                            <?php foreach($suppliers as $supplier): ?>
                                <option value="<?=$supplier['supplier_id']?>"><?=$supplier['name']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Mã nhân viên -->
                    <div class="pt-3">
                        <label for="user_idSuaPN">Mã nhân viên: </label>
                        <input type="text" name="user_idSuaPN" id="user_idSuaPN" value="3" readonly class="form-control">
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
                        <button class="btn btn-outline-primary" type="button">Đóng</button>
                    </div>
                </form>
            </div>
        </div>

      
        <div class="thongBaoXoa rounded-2">
    <p class="mb-0 fs-5 text-center">
        Bạn có chắc chắn muốn xóa phiếu nhập hay không?       
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
        Xóa sản phẩm thành công
    </p>
</div>
        <div class="thongbaoXoaKhongThanhCong  bg-danger me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
                Xóa sản phẩm thất bại
            </p>
        </div>

        <div class="thongbaoLuuKhongThanhCong  bg-danger me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
                Lưu sản phẩm thất bại
            </p>
        </div>
        <div class="thongbaoLuuThanhCong bg-success me-3 mt-3 p-3 rounded-2">
    <p class="mb-0 text-white">       
        Lưu sản phẩm thành công
    </p>
</div>
<div class="overlay"></div>
<div class="thongbaoLoi  bg-danger me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
            </p>
        </div>
    </section>

    <script src="./assets/js/fetch_phieuNhap.js"></script>
</body>
</html>
