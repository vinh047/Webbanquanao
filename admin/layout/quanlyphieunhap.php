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
// Bắt đầu session để truy cập thông tin người dùng đã đăng nhập
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Chỉ gọi session_start() nếu session chưa được bắt đầu
}
// Kiểm tra xem người dùng đã đăng nhập chưa và lấy role_id từ session
$user_id = $_SESSION['user_id'] ?? null;
$role_id = $_SESSION['role_id'] ?? null;

if ($user_id) {
    // Kết nối đến cơ sở dữ liệu và lấy thông tin người dùng nếu cần
    require_once(__DIR__ . '/../../database/DBConnection.php');
    $db = DBConnect::getInstance();
    
    // Truy vấn để lấy tên người dùng dựa trên user_id
    $stmt = $db->select("SELECT username FROM users WHERE user_id = ?", [$user_id]);
    
    if ($stmt) {
        $username = $stmt[0]['username']; // Gán tên người dùng vào biến
    } else {
        $username = "Không tìm thấy người dùng";
    }
} else {
    // Nếu không có user_id trong session, người dùng chưa đăng nhập
    $username = "Chưa đăng nhập";
}

if ($role_id) {
    // Kết nối đến cơ sở dữ liệu và lấy quyền của người dùng
    require_once(__DIR__ . '/../../database/DBConnection.php');
    $db = DBConnect::getInstance();

    // Truy vấn để lấy tất cả quyền của người dùng với permission_id = 1
    $permissions = $db->select("SELECT action, permission_id FROM role_permission_details WHERE role_id = ? AND permission_id = 4", [$role_id]);

    // Lưu các quyền vào mảng permissions trong session
    $permissionsArray = [];
    foreach ($permissions as $permission) {
        $permissionsArray[] = $permission['action']; // Lưu các hành động vào mảng permissions
    }

    // Lưu các quyền vào session
    $_SESSION['permissions'] = $permissionsArray; // Lưu danh sách quyền vào session
}

// Truyền quyền vào thẻ HTML
$permissionsJson = json_encode($_SESSION['permissions'] ?? []);
        $categories = $db->select("SELECT * FROM categories", []);
        $suppliers = $db->select("SELECT * FROM supplier",[]);
        $tensp = $db->select("SELECT * FROM products",[]);
        $color = $db->select("SELECT * FROM colors",[]);
        $size = $db->select("SELECT * FROM sizes ORDER BY size_id ASC",[]);
        $nhanvien = $db->select("SELECT * FROM users u JOIN roles r ON u.role_id = r.role_id WHERE u.role_id > 1",[]);
    ?>
</head>
<body> 

    <!-- Thẻ ẩn để chứa giá trị role_id -->
    <div id="permissions" data-permissions='<?= $permissionsJson ?>' style="display:none;"></div>

<div class="sanpham py-3" style="font-size: 19px;">

<form action="./ajax/insertPhieuNhap.php" id="formNhapPhieuNhap" class="p-4 bg-white rounded-3 border">
    <h5 class="mb-3 fw-bold">Thông tin phiếu nhập</h5>

    <div class="row g-3">
        <!-- Nhà cung cấp -->
        <div class="col-md-4">
            <label for="supplier_id" class="form-label">Nhà cung cấp</label>
            <select name="supplier_id" id="supplier_id" class="form-select">
                <option value="">-- Chọn nhà cung cấp --</option>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?= $supplier['supplier_id'] ?>"><?= $supplier['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Mã nhân viên -->
        <div class="col-md-2">
            <label for="user_id" class="form-label">Tên nhân viên</label>
<!-- Trường hiển thị tên người dùng -->
<input type="text" name="username_display" id="username_display" value="<?= htmlspecialchars($username) ?>" readonly class="form-control bg-light">

<!-- Trường ẩn chứa giá trị user_id (không hiển thị cho người dùng, nhưng gửi đi khi submit) -->
<input type="hidden" name="user_id" id="user_id" value="<?= htmlspecialchars($user_id) ?>" readonly class="form-control bg-light">
        </div>
    </div>

    <hr class="my-4">

    <h6 class="fw-bold">Sản phẩm nhập</h6>
    <div class="row g-3 align-items-end">
        <!-- Tên sản phẩm -->
        <div class="col-md-5">
            <label for="cbTen" class="form-label">Tên sản phẩm</label>
            <div class="d-flex">
                <select name="cbTen" id="cbTen" class="form-select w-auto">
                    <option value="">-- Chọn sản phẩm --</option>
                    <?php foreach ($tensp as $ten): ?>
                        <option value="<?= $ten['product_id'] ?>"><?= $ten['product_id'] ?> - <?= $ten['name'] ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-outline-primary ms-2" type="button" id="btnMoForm">Thêm SP</button>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <div class="row g-3">
    <h6 class="fw-bold mb-0">Chi tiết sản phẩm</h6>
                <!-- Ảnh -->
        <div class="col-md-3">
            <label for="fileAnh" class="form-label">Hình ảnh</label>
            <input type="file" name="fileAnh" id="fileAnh" class="form-control">
            <div class="pt-2" style="max-width: 150px;" id="hienthianh">
                <img src="" alt="preview" class="img-thumbnail" id="hienthiimg" style="height: 130px; object-fit: contain; display: none;">
            </div>
        </div>
        <!-- Màu -->
        <div class="col-md-3">
            <label for="cbMau" class="form-label">Màu</label>
            <select name="cbMau" id="cbMau" class="form-select">
                <option value="">-- Chọn màu --</option>
                <?php foreach ($color as $cl): ?>
                    <option value="<?= $cl['color_id'] ?>"><?= $cl['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Size -->
        <div class="col-md-3">
            <label for="cbSize" class="form-label">Size</label>
            <select name="cbSize" id="cbSize" class="form-select">
                <option value="">-- Chọn size --</option>
                <?php foreach ($size as $s): ?>
                    <option value="<?= $s['size_id'] ?>"><?= $s['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Số lượng -->
        <div class="col-md-3">
            <label for="txtSl" class="form-label">Số lượng</label>
            <input type="text" name="txtSl" id="txtSl" class="form-control" placeholder="Nhập số lượng">
        </div>
    </div>

    <div class="mt-4 d-flex gap-3">
        <button type="button" id="add_product" class="btn btn-outline-secondary">Thêm vào hàng chờ</button>
        <button type="button" id="resetFormProduct" class="btn btn-danger">Reset chi tiết</button>
        <button type="submit" class="btn btn-primary">Lưu phiếu nhập</button>
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
                                <th class="bg-secondary text-white hienthiloai">Ảnh</th>
                                <th class="bg-secondary text-white hienthiid">Size, màu</th>
                                <th class="bg-secondary text-white hienthigia">Số lượng</th>
                                <th class="bg-secondary text-white hienthibtn-ne">Xử lý</th>
                            </tr>
                        </thead>
                        <tbody id="product-list-tamluu">
                        </tbody>
                    </table>
                </div>

                                <hr class="mt-5">

                                <!-- Hiện thông tin phiếu nhập -->

                                <!-- Phần xử lý bộ lọc -->
                                <section class="pb-4 pt-2">
                <div class="boloc ms-5 position-relative">
                    <span class="fs-3"><i class="fa-solid fa-filter filter-icon" id="filter-icon" title="Lọc phiếu nhập"></i> <span class="fs-5">Lọc danh sách CTPN</span> </span>
                    <div class="filter-loc position-absolute bg-light p-3 rounded-2 d-none" style="z-index : 2000;border:1px solid black;">
                        <form action="" method="POST" id="formLoc">
                        <div class="d-flex">
                                <div class="me-auto">
                                    <h5>Lọc PN</h5>
                                </div>
                                <div class="">
                                    <button class="btn btn-outline-secondary btn-sm border-0" id="tatFormLoc" >X</button>
                                </div>  
                            </div>
                            <label for="txtIDpn" class="mt-2">Mã PN : </label>
                            <input type="text" class="form-control form-control-sm" id="txtIDpn" name="txtIDpn">
                            <label for="txtIDncc" class="mt-3">Nhà cung cấp : </label>
                            <select name="txtIDncc" id="txtIDncc" class="form-select">
                                <option value="">Chọn nhà cung cấp</option>
                                <?php foreach($suppliers as $s): ?>
                                <option value="<?=$s['supplier_id']?>"><?=$s['name']?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="txtIDnv" class="mt-2">Nhân viên : </label>
                            <select name="txtIDnv" id="txtIDnv" class="form-select">
                                <option value="">Chọn nhân viên</option>
                                <?php foreach($nhanvien as $n): ?>
                                <option value="<?=$n['user_id']?>"><?=$n['username']?></option>
                                <?php endforeach ?>
                            </select>

                            <div class="d-flex gap-3 mt-2">
                                <div class="me-auto">
                                    <label for="dateNhap">Từ ngày : </label>
                                    <input type="date" class="form-control form-control-sm" id="dateNhap" name="dateNhap">
                                </div>
                                <div class="">
                                    <label for="dateKT">Đến ngày : </label>
                                    <input type="date" class="form-control form-control-sm" id="dateKT" name="dateKT">
                                </div>
                            </div>

                            <label for="txtTrangThai" class="mt-2">Trạng thái</label>
                            <select name="txtTrangThai" id="txtTrangThai" class="form-select">
                                <option value="">Chọn trạng thái</option>
                                <option value="0">Đã xác nhận</option>
                                <option value="1">Chờ xác nhận</option>
                            </select>

                            <div class="d-flex justify-content-center gap-2 pt-3">
                                <button class="btn btn-primary" style="width:70px;" type="submit">Lọc</button>
                                <button class="btn btn-danger"  style="width:70px;" type="reset">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
                                    <!-- phần xử lý danh sách phiêu nhập -->
                    <div class="hienthi">
                        <div class="d-flex justify-content-center border border-3 border-bottom-0 p-2 bg-light">
                            <p class="mb-0 fs-3">
                                Danh sách phiếu nhập
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
                                <th class="bg-secondary text-white tensp">Trạng thái</th>
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

                                    <!-- Xử lý form sửa danh sách chờ -->
        <div class="formSua border container-md p-4">
            <div class="" style="font-size: 16px;">
            <p class="mb-0 text-center fs-4">Sửa thông tin sản phẩm</p>
            <form action="" id="formSua"> 
                    <!-- Chọn nhà cung cấp -->
                    <div class="pt-3">
                        <label for="stt">Số TT: </label>
                        <input type="text" name="stt" id="stt" readonly class="form-control bg-light">
                    </div>
                    <div class="d-flex">
                    <div class="pt-3 me-auto">
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
                        <input type="text" name="user_idSua" id="user_idSua" value="3" readonly class="form-control bg-light">
                    </div>
                    </div>

                    <!-- Thêm sản phẩm -->
                    <div class="pt-3">
                        <label for="cbTenSua">Tên sản phẩm: </label>
                        <div class="d-flex">
                        <select name="cbTenSua" id="cbTenSua" class="form-select">
                            <option value="">Chọn tên sản phẩm</option>
                            <?php foreach( $tensp as $ten ): ?>
                            <option value="<?=$ten['product_id']?>"><?=$ten['product_id']?> - <?=$ten['name']?></option>
                            <?php endforeach ?>
                        </select>
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
    
                    <div class="d-flex gap-5">
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
                    <div class="pt-3">
                        <label for="txtSlSua">Số lượng sản phẩm : </label>
                        <input type="text" name="txtSlSua" id="txtSlSua" class="form-control " placeholder="Số lượng của sản phẩm">
                    </div>
                    </div>
    

                    <div class="d-flex pt-3 gap-3">
                        <button type="button" id="btn_add_product_sua" class="btn btn-outline-secondary">Xác nhận sửa</button>
                        <button class="btn btn-outline-primary" type="button">Đóng</button>
                    </div>
                </form>
            </div>
        </div>



                                <!-- Xử lý phần sửa phiếu nhập -->
        <div class="formSuaPN border container-md p-4">
        <p class="mb-0 text-center fs-4">Sửa thông tin sản phẩm</p>
            <div class="" style="font-size: 16px;">
            <form action="../ajax/updatePhieuNhap.php" id="formSuaPN"> 
                    <!-- Chọn nhà cung cấp -->
                    <div class="pt-3">
                        <label for="txtMaPNsua">Mã PN: </label>
                        <input type="text" name="txtMaPNsua" id="txtMaPNsua" readonly class="form-control bg-light">
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
                        <input type="text" name="user_idSuaPN" id="user_idSuaPN" value="3" readonly class="form-control bg-light">
                    </div>

                    <div class="pt-3">
                        <label for="txtTongGT">Tổng giá trị: </label>
                        <input type="text" name="txtTongGT" id="txtTongGT" class="form-control bg-light" readonly >
                    </div>

                    <div class="pt-3">
                        <label for="txtNgayLap">Ngày lập: </label>
                        <input type="text" name="txtNgayLap" id="txtNgayLap" class="form-control bg-light" readonly >
                    </div>

                    <div class="d-flex pt-3 gap-3">
                        <button type="button" id="btn_sua_pn" class="btn btn-outline-secondary">Xác nhận sửa</button>
                        <button class="btn btn-outline-primary" type="button">Đóng</button>
                    </div>
                </form>
            </div>
        </div>


                                <!-- Xử lý phần thêm 1 sản phẩm mới nếu như chưa có -->
        <div class="formNhapSanPham p-3">
                    <div class="pt-3 text-center">
                        <h3 class="mb-0">Thêm sản phẩm</h3>
                    </div>
                                <!-- Thêm sản phẩm -->
                    <div class="pt-1">
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
                        <label for="txtPT">Tỉ lệ phần trăm tăng giá bán : </label>
                        <input type="text" name="txtPT" id="txtPT" class="form-control " placeholder="Phần trăm giá sản phẩm" value="30">
                    </div>

                    <div class="d-flex justify-content-center pt-3 gap-3">
                        <button class="btn btn-outline-success" id="btnLuuSanPham" style="width:120px;">Lưu sản phẩm</button>
                        <button class="btn btn-outline-danger"  id="btnDongSanPham" style="width:120px;">Hủy</button>
                    </div>
        </div>

      
        <!-- Toàn bộ đa số dưới đây là thông báo thôi -->
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
        Xóa phiếu nhập thành công
    </p>
</div>
        <div class="thongbaoXoaKhongThanhCong  bg-danger me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
                Xóa phiếu nhập thất bại
            </p>
        </div>

        <div class="thongbaoLuuKhongThanhCong  bg-danger me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
                Lưu phiếu nhập thất bại
            </p>
        </div>


        <!-- Này xử lý modal hiển thị chi tiết phiếu nhập -->
<div class="modal fade" id="modalChiTietPhieuNhap" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">Chi tiết phiếu đã nhập</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
          <p><strong>Nhà cung cấp:</strong> <span id="tenNCCPN"></span></p>
          <p><strong>Nhân viên lập phiếu:</strong> <span id="tenNVPN"></span></p>
          <p><strong>Tổng số lượng:</strong> <span id="tongSoLuongPN"></span></p>
          <p><strong>Tổng giá trị:</strong> <span id="tongGiaTriPN"></span> VNĐ</p>
        </div>

        <table class="table table-bordered" id="chitiet-phieunhap">
          <thead>
            <tr>
              <th>#</th>
              <th>Sản phẩm</th>
              <th>Size</th>
              <th>Màu</th>
              <th>Số lượng</th>
              <th>Tồn kho hiện tại</th>
            </tr>
          </thead>
          <tbody>
            <!-- JS sẽ render -->
          </tbody>
        </table>

        <!-- 👇 Phân trang -->
        <div id="modal-pagination" class="d-flex justify-content-center align-items-center gap-2 mt-3"></div>
        <!-- JS sẽ render nút -->
        </div>
      </div>

    </div>
  </div>
</div>


<!-- Lại là thông báo -->
<div id="boxTrungSP" class="thongBaoTrung rounded-2 bg-light p-3 border">
<p class="mb-0 fs-5 text-center" id="trungTenSP">Sản phẩm đã có trong hàng đợi!</p>
<p class="mb-0 fs-6 text-center" id="trungChiTiet">Bạn có muốn cộng dồn vào không?</p>

    <div class="d-flex justify-content-center gap-3 mt-2">
        <div>
            <button id="btnCoTrung" class="btn btn-danger" style="width:80px;">Có</button>
        </div>
        <div>
            <button id="btnKhongTrung" class="btn btn-primary" style="width:80px;">Không</button>
        </div>
    </div>
</div>

<div id="boxTrungBT" class="thongBaoTrung rounded-2 bg-light p-3 border">
  <p class="mb-0 fs-5 text-center" id="trungTenBT">Thông báo</p>
  <p class="mb-0 fs-6 text-center" id="trungCTBT">Bạn có muốn cộng dồn vào không?</p>

  <div class="d-flex justify-content-center gap-3 mt-2">
    <button id="btnXacNhanThem" class="btn btn-danger" style="width:80px;">Có</button>
    <button id="btnHuyThem" class="btn btn-primary" style="width:80px;">Không</button>
  </div>
</div>


<div id="xacNhanCho" class="thongBaoCho rounded-2 bg-light p-3 border">
    <p class="mb-0 fs-5 text-center">Khi chọn xác nhận sẽ không còn xử lý được nữa!</p>
    <p class="mb-0 fs-5 text-center">Bạn có chắc chắn không?</p>
    <div class="d-flex justify-content-center gap-3 mt-2">
        <div>
            <button id="btnXacNhan" class="btn btn-danger" style="width:80px;">Xác nhận</button>
        </div>
        <div>
            <button id="btnHuy" class="btn btn-primary" style="width:80px;">Hủy</button>
        </div>
    </div>
</div>

<div class="thongBaoQuyen bg-danger me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
                Bạn không có quyền thực hiện chức năng này
            </p>
        </div>

<div class="thongbaoXoaThatBai  bg-danger me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
                Xóa phiếu nhập thất bại
            </p>
        </div>
        <div class="thongbaoLuuThanhCong bg-success me-3 mt-3 p-3 rounded-2">
    <p class="mb-0 text-white">       
        Lưu phiếu nhập thành công
    </p>
</div>
<div class="thongbaoThemSp bg-success me-3 mt-3 p-3 rounded-2">
    <p class="mb-0 text-white">       
        Thêm sản phẩm mới thành công
    </p>
</div>
<div class="overlay"></div>
<div class="thongbaoLoi  bg-danger me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
            </p>
        </div>
    </section>
<!-- end -->
    <script src="./assets/js/fetch_phieuNhap.js"></script>
</body>
</html>
