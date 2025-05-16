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

// Kiểm tra quyền của người dùng
// $user_id = $_SESSION['user_id'] ?? null;
$user_id = $_SESSION['admin_id'] ?? null;
$role_id = $_SESSION['role_id'] ?? null;

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

// ✅ Gán biến kiểm tra quyền để dùng cho HTML bên dưới
$hasReadPermission = in_array('read', $_SESSION['permissions'] ?? []);

// Truyền quyền vào thẻ HTML
$permissionsJson = json_encode($_SESSION['permissions'] ?? []);
    $color = $db->select("SELECT * FROM colors",[]);
    $size = $db->select("SELECT * FROM sizes ORDER BY size_id ASC",[]);
    ?>
</head>
<body>
        <!-- Thẻ ẩn để chứa giá trị role_id -->
        <div id="permissions" data-permissions='<?= $permissionsJson ?>' style="display:none;"></div>
        <section class="py-3">
                <div class="boloc ms-5 position-relative">
                    <span class="fs-3"><i class="fa-solid fa-filter filter-icon" id="filter-icon" title="Lọc chi tiết phiếu nhập"></i> <span class="fs-5">Lọc danh sách CTPN</span> </span>
                    <div class="filter-loc position-absolute bg-light p-3 rounded-2 d-none" style="z-index : 2000;border:1px solid black;">
                        <form action="" method="POST" id="formLoc">
                        <div class="d-flex">
                                <div class="me-auto">
                                    <h5>Lọc CTPN</h5>
                                </div>
                                <div class="">
                                    <button class="btn btn-outline-secondary btn-sm border-0" id="tatFormLoc" type="button">X</button>
                                </div>  
                            </div>
                            <label for="txtIDctpn" class="mt-2">Mã CTPN : </label>
                            <input type="text" class="form-control form-control-sm" id="txtIDctpn" name="txtIDctpn">
                            <label for="txtIDpn" class="mt-2">Mã PN : </label>
                            <input type="text" class="form-control form-control-sm" id="txtIDpn" name="txtIDpn">
                            <label for="txtIDsp" class="mt-2">Mã SP : </label>
                            <input type="text" class="form-control form-control-sm" id="txtIDsp" name="txtIDsp">
                            <label for="txtIDbt" class="mt-2">Mã BT : </label>
                            <input type="text" class="form-control form-control-sm" id="txtIDbt" name="txtIDbt">

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

                            <div class="d-flex justify-content-center gap-2 pt-3">
                                <button class="btn btn-primary" style="width:70px;" type="submit">Lọc</button>
                                <button class="btn btn-danger"  style="width:70px;" type="reset">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <div class="sanpham py-3" style="font-size: 19px;">
                                <!-- Hiện thông tin phiếu nhập -->
                    <div class="hienthi">
                        <div class="d-flex justify-content-center border border-3 border-bottom-0 p-2 bg-light">
                            <p class="mb-0 fs-3">
                                Danh sách chi tiết phiếu nhập
                            </p>
                        </div>
                    <table class="table border-start border-end table-striped table-sm">
                        <thead>
                            <tr class="text-center">
                                <th class="bg-secondary text-white hienthiid">ID CTPN</th>
                                <th class="bg-secondary text-white hienthiid">ID PN</th>
                                <th class="bg-secondary text-white hienthiid">ID BT</th>
                                <th class="bg-secondary text-white tensp giaodienmb">Tên SP</th>
                                <th class="bg-secondary text-white tensp giaodienmb">Ngày lập</th>
                                <th class="bg-secondary text-white hienthigia giaodienmb">Trạng thái</th>
                                <!-- <th class="bg-secondary text-white hienthibtn-ne cotxuly">Xử lý</th> -->
                                 <?php if ($hasReadPermission): ?>
    <th class="bg-secondary text-white hienthibtn-ne cotxuly">Xử lý</th>
<?php endif; ?>

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
            <p class="mb-0 text-center fs-4">Sửa thông tin chi tiết phiếu nhập</p>

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
        <p class="mb-0 text-center fs-4">Sửa thông tin chi tiết phiếu nhập</p>
            <div class="" style="font-size: 16px;">
            <form action="./ajax/updateCTPhieuNhap.php" id="formSuaPN"> 
                    <!-- Chọn nhà cung cấp -->
                    <div class="d-flex">
                    <div class="pt-3 me-auto">
                        <label for="txtMaCTPNsua">Mã CTPN: </label>
                        <input type="text" name="txtMaCTPNsua" id="txtMaCTPNsua" readonly class="form-control bg-light">
                    </div>
                    <div class="pt-3">
                        <label for="txtMaBTsua">Mã BT: </label>
                        <input type="text" name="txtMaBTsua" id="txtMaBTsua"  class="form-control bg-light" readonly>
                    </div>
                    </div>
                    <div class="d-flex">
                    <div class="pt-3 me-auto">
                        <label for="txtMaSPsua">Mã SP: </label>
                        <input type="text" name="txtMaSPsua" id="txtMaSPsua"  class="form-control">
                    </div>

                    <div class="pt-3">
                        <label for="txtMaPNsua">Mã PN: </label>
                        <input type="text" name="txtMaPNsua" id="txtMaPNsua"  class="form-control">
                    </div>
                    </div>

                    <div class="pt-3">
                        <label for="txtSlsuaTon">Số lượng: </label>
                        <input type="text" name="txtSlsuaTon" id="txtSlsuaTon"  class="form-control">
                    </div>

                    <div class="pt-3">
                        <label for="txtNgayLap">Ngày lập: </label>
                        <input type="text" name="txtNgayLap" id="txtNgayLap" class="form-control bg-light" readonly >
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

<div id="boxTrungSP" class="thongBaoTrung rounded-2 bg-light p-3 border" style="display: none; position: fixed; top: 40%; left: 50%; transform: translate(-50%, -50%); z-index: 999;">
    <p class="mb-0 fs-5 text-center">Sản phẩm này đã có trong hàng đợi!</p>
    <p class="mb-0 fs-5 text-center">Bạn có muốn cộng dồn vào không?</p>
    <div class="d-flex justify-content-center gap-3 mt-2">
        <div>
            <button id="btnCoTrung" class="btn btn-danger" style="width:80px;">Có</button>
        </div>
        <div>
            <button id="btnKhongTrung" class="btn btn-primary" style="width:80px;">Không</button>
        </div>
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
<div class="overlay"></div>
<div class="thongbaoLoi  bg-danger me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
            </p>
        </div>
    </section>

    <!-- Modal Chi tiết biến thể -->
<div class="modal fade" id="modalChiTietBienThe" tabindex="-1" aria-labelledby="modalChiTietLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="modalChiTietLabel">Chi tiết biến thể đã nhập</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row align-items-center">
          <div class="col-md-4 text-center">
            <img id="ctbt_image" src="" class="img-fluid rounded border" style="max-height: 280px; object-fit: contain;" alt="Ảnh sản phẩm">
          </div>
          <div class="col-md-8 fs-6">
            <p style="font-size: 17px;"><strong>Sản phẩm:</strong> <span id="ctbt_tensp"></span></p>
            <p style="font-size: 17px;"><strong>Màu sắc:</strong> <span id="ctbt_mau"></span></p>
            <p style="font-size: 17px;"><strong>Size:</strong> <span id="ctbt_size"></span></p>
            <p><strong>Số lượng nhập:</strong> <span id="ctbt_sl"></span></p>
            <p><strong>Giá nhập:</strong> <span id="ctbt_gia"></span> đ</p>
            <p><strong>Tổng tiền:</strong> <span id="ctbt_thanhtien"></span> đ</p>
            <p><strong>Ngày lập:</strong> <span id="ctbt_ngay"></span></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="thongBaoQuyen bg-danger me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
                Bạn không có quyền thực hiện chức năng này
            </p>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <script src="./assets/js/fetch_ctphieunhap.js"></script>
</body>
</html>