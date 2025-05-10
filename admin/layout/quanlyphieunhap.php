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
    <style>
.preview-img img {
  height: 100px;
  object-fit: contain;
  display: inline-block;
  vertical-align: middle;
}
.preview-img:hover,.img-phongto:hover {
  transform: scale(4.6);
  transition: transform 0.2s ease;
  z-index: 10;
}

</style>

    <?php
// Bắt đầu session để truy cập thông tin người dùng đã đăng nhập
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Chỉ gọi session_start() nếu session chưa được bắt đầu
}
// ✅ Kết nối DB
require_once __DIR__ . '/../../database/DBConnection.php';
$db = DBConnect::getInstance()->getConnection();


// ✅ Lấy danh sách cần dùng để truyền vào JS
$productList = $db->query("SELECT product_id, name FROM products")->fetchAll(PDO::FETCH_ASSOC);
$sizeList = $db->query("SELECT size_id, name FROM sizes ORDER BY size_id ASC")->fetchAll(PDO::FETCH_ASSOC);
$colorList = $db->query("SELECT color_id, name FROM colors")->fetchAll(PDO::FETCH_ASSOC);

// Kiểm tra xem người dùng đã đăng nhập chưa và lấy role_id từ session
$user_id = $_SESSION['user_id'] ?? null;
$role_id = $_SESSION['role_id'] ?? null;

if ($user_id) {
    // Kết nối đến cơ sở dữ liệu và lấy thông tin người dùng nếu cần
    require_once(__DIR__ . '/../../database/DBConnection.php');
    $db = DBConnect::getInstance();
    
    // Truy vấn để lấy tên người dùng dựa trên user_id
    $stmt = $db->select("SELECT name FROM users WHERE user_id = ?", [$user_id]);
    
    if ($stmt) {
        $name = $stmt[0]['name']; // Gán tên người dùng vào biến
    } else {
        $name = "Không tìm thấy người dùng";
    }
} else {
    // Nếu không có user_id trong session, người dùng chưa đăng nhập
    $name = "Chưa đăng nhập";
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
<button class="btn btn-primary" id="create_pn">Tạo phiếu nhập</button>
<button type="button" class="btn btn-secondary" id="btnThemSanPhamMoi">
    <i class="fa fa-plus"></i> Thêm SP mới
  </button>
  <button id="btnMoModalBienThe" class="btn btn-warning">Thêm biến thể</button>
        <!-- Modal Tạo Phiếu Nhập -->
<div class="modal fade" id="modalCreatePN" tabindex="-1" aria-labelledby="modalCreatePNLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalCreatePNLabel">Tạo Phiếu Nhập Mới</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" style="overflow-y: auto;max-height: 70vh;">
        <!-- Form phiếu nhập -->
        <form action="./ajax/insertPhieuNhap.php" id="formNhapPhieuNhap">
          <div class="row g-4">
            <!-- Nhà cung cấp -->
            <div class="col-md-4">
              <label class="form-label">Nhà cung cấp</label>
              <select name="supplier_id" id="supplier_id" class="form-select select2">
                <option value="">-- Chọn nhà cung cấp --</option>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?= $supplier['supplier_id'] ?>"><?= $supplier['name'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Mã nhân viên -->
            <div class="col-md-2 me-auto">
              <label class="form-label">Tên nhân viên</label>
              <input type="text" name="name_display" id="name_display" value="<?= htmlspecialchars($name) ?>" readonly class="form-control bg-light">
              <input type="hidden" name="user_id" id="user_id" value="<?= htmlspecialchars($user_id) ?>">
            </div>

          </div>

          <hr class="pt-1">

          <!-- Vùng thêm động các sản phẩm -->
          <div id="dynamic-product-forms" class="mt-3"></div>
          <div id="pagination-product-forms" class="d-flex justify-content-center align-items-center gap-2 mt-3"></div>

          <div class="mt-4 d-flex justify-content-end gap-2">
          <button type="button" id="resetFormProduct" class="btn btn-danger me-auto">Reset</button>

            <button type="button" class="btn btn-outline-success" id="btnThemSanPham">Thêm chi tiết SP</button>
            <button type="submit" class="btn btn-primary">Lưu Phiếu Nhập</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>
<!-- Modal danh sách biến thể -->
<div class="modal fade" id="modalChonBienThe" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">Chọn Biến Thể</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered table-hover" id="variant-table">
        <thead>
  <tr class="text-center">
    <th>Mã</th>
    <th style="width:15%;">Ảnh</th>
    <th>Size</th>
    <th>Màu</th>
    <th style="width:15%;">Tồn kho</th>
    <th>Xử lý</th>
  </tr>
</thead>
          <tbody></tbody>
        </table>
        <div id="variant-pagination" class="d-flex justify-content-center align-items-center gap-2 mt-3"></div>

      </div>
    </div>
  </div>
</div>


                                <hr class="mt-5">

                                <!-- Hiện thông tin phiếu nhập -->

                                <!-- Phần xử lý bộ lọc -->
                                <section class="pb-4 pt-2">
                <div class="boloc ms-5 position-relative">
                    <span class="fs-3"><i class="fa-solid fa-filter filter-icon" id="filter-icon" title="Lọc phiếu nhập"></i> <span class="fs-5">Lọc danh sách CTPN</span> </span>
                    <div class="filter-loc position-absolute bg-light p-3 rounded-2 d-none" style="z-index : 2000;border:1px solid black;max-width:300px;">
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
                            <select name="txtIDncc" id="txtIDncc" class="form-select select2">
                                <option value="">Chọn nhà cung cấp</option>
                                <?php foreach($suppliers as $s): ?>
                                <option value="<?=$s['supplier_id']?>"><?=$s['name']?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="txtIDnv" class="mt-2">Nhân viên : </label>
                            <select name="txtIDnv" id="txtIDnv" class="form-select select2">
                                <option value="">Chọn nhân viên</option>
                                <?php foreach($nhanvien as $n): ?>
                                <option value="<?=$n['user_id']?>"><?=$n['name']?></option>
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
                    <table class="table table-striped table-sm border-start border-end">
                        <thead>
                            <tr class="text-center">
                                <th class="bg-secondary text-white hienthiid">ID PN</th>
                                <th class="bg-secondary text-white hienthigia giaodienmb">Tên NV</th>
                                <th class="bg-secondary text-white tensp giaodienmb">Tên NCC</th>
                                <th class="bg-secondary text-white tensp giaodienmb">Tổng tiền</th>
                                <th class="bg-secondary text-white tensp giaodienmb">Ngày lập</th>
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




                                <!-- Xử lý phần sửa phiếu nhập -->
<div class="modal fade" id="modalSuaPhieuNhap" tabindex="-1" aria-labelledby="modalSuaPhieuNhapLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalSuaPhieuNhapLabel">Sửa Phiếu Nhập</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>

      <div class="modal-body" style="overflow-y: auto;max-height: 70vh;">
        <div class="row g-4">
          <!-- Thông tin bên trái -->
          <div class="col-md-4">
            <form id="formSuaPN">
              <div class="mb-3">
                <label class="form-label">Mã PN</label>
                <input type="text" name="txtMaPNsua" id="txtMaPNsua" class="form-control bg-light" readonly>
              </div>
              <div class="mb-3">
                <label class="form-label">Nhà cung cấp</label>
                <select id="supplier_idSuaPN" name="supplier_idSuaPN" class="form-select select2" required>
                  <option value="">-- Chọn nhà cung cấp --</option>
                  <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?= $supplier['supplier_id'] ?>"><?= $supplier['name'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Nhân viên</label>
                <input type="text" id="user_Name" name="user_Name" class="form-control bg-light" readonly>
                <input type="hidden" id="user_idSuaPN" name="user_idSuaPN" class="form-control bg-light" readonly>
              </div>
              <div class="mb-3">
                <label class="form-label">Tổng giá trị</label>
                <input type="text" id="txtTongGT" name="txtTongGT" class="form-control bg-light" readonly>
              </div>
              <div class="mb-3">
                <label class="form-label">Ngày lập</label>
                <input type="text" id="txtNgayLap" name="txtNgayLap" class="form-control bg-light" readonly>
              </div>
              <div class="d-flex justify-content-center gap-2">
                <button type="button" id="btn_sua_pn" class="btn btn-primary">Xác nhận sửa</button>
              </div>
            </form>
          </div>

          <!-- Chi tiết bên phải -->
          <div class="col-md-8">
            <h6 class="fw-bold mb-2">Chi tiết phiếu nhập</h6>
            <table class="table table-bordered" id="tableChiTietPhieuNhap">
              <thead>
                <tr class="text-center">
                  <th>Tên sản phẩm</th>
                  <th style="width:15%;">Mã biến thể</th>
                  <th style="width:15%;">Số lượng nhập</th>
                </tr>
              </thead>
              <tbody>
                <!-- JS sẽ render -->
              </tbody>
            </table>
            <div id="pagination-sua-phieunhap" class="d-flex justify-content-center align-items-center mt-3"></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>




                                <!-- Xử lý phần thêm 1 sản phẩm mới nếu như chưa có -->
<!-- Modal Thêm sản phẩm mới -->
<div class="modal fade" id="modalNhapSanPham" tabindex="-1" aria-labelledby="modalNhapSanPhamLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalNhapSanPhamLabel">Thêm sản phẩm</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>

      <div class="modal-body">
        <form id="formNhapSanPham">
          <!-- Thêm sản phẩm -->
          <div class="mb-3">
            <label for="txtTen" class="form-label">Tên sản phẩm:</label>
            <input type="text" name="txtTen" id="txtTen" placeholder="Tên của sản phẩm" class="form-control">
          </div>

          <div class="mb-3">
            <label for="txtMota" class="form-label">Mô tả sản phẩm:</label>
            <textarea name="txtMota" id="txtMota" class="form-control" placeholder="Mô tả"></textarea>
          </div>

          <div class="mb-3">
            <label for="cbLoai" class="form-label">Loại sản phẩm:</label>
            <select name="cbLoai" id="cbLoai" class="form-select">
              <option value="">Chọn loại sản phẩm</option>
              <?php foreach($categories as $loai): ?>
                  <option value="<?=$loai['category_id']?>"><?=$loai['name']?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="txtPT" class="form-label">Tỉ lệ phần trăm tăng giá bán:</label>
            <input type="text" name="txtPT" id="txtPT" class="form-control" placeholder="Phần trăm giá sản phẩm" value="30%">
          </div>

          <div class="d-flex justify-content-center gap-2 pt-3">
            <button type="button" class="btn btn-success" id="btnLuuSanPham" style="width: 120px;">Lưu sản phẩm</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal thêm biến thể -->
<div class="modal fade" id="modalThemBienThe" tabindex="-1" aria-labelledby="modalThemBienTheLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="modalThemBienTheLabel">Thêm Biến Thể cho sản phẩm</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>

      <div class="modal-body" style="overflow-y: auto; max-height: 70vh;">
        <form id="formBienThe" enctype="multipart/form-data">
          
              <div class="row">
                <div class="col-md-4">
                <label class="form-label">Chọn sản phẩm</label>
              <select name="id_sanpham" id="id_sanpham" class="form-select w-50 select2">
                <option value="">-- Chọn sản phẩm --</option>
                <?php foreach ($tensp as $ten): ?>
                    <option value="<?= $ten['product_id'] ?>"><?=$ten['product_id']?> - <?= $ten['name'] ?></option>
                <?php endforeach; ?>
              </select>
                </div>
              </div>

          <div id="variant-container"></div>

          <div class="d-flex gap-2 mt-3">
            <button class="btn btn-danger me-auto" type="button" id="resetBienThe">Reset</button>
            <button type="button" class="btn btn-secondary" id="btnAddVariantRow">
              <i class="fa fa-plus me-1"></i> Thêm dòng biến thể
            </button>
            <button type="submit" class="btn btn-success">Lưu biến thể</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


      
        <!-- Toàn bộ đa số dưới đây là thông báo thôi -->
  <div class="thongBaoXoa rounded-2">
    <p class="mb-0 fs-5 text-center">
        Bạn có chắc chắn muốn xóa phiếu nhập hay không?       
    </p>
    <div class="d-flex justify-content-center gap-3 mt-2">
        <div>
            <button class="btn btn-danger btn-confirm-yes" style="width:80px;">Có</button>
        </div>
        <div>
            <button class="btn btn-primary btn-confirm-no" style="width:80px;">Không</button>
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
        Xóa chi tiết phiếu nhập thành công
    </p>
</div>
        <div class="thongbaoXoaKhongThanhCong  bg-danger me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
            Xóa chi tiết phiếu nhập thất bại
            </p>
        </div>


        <div class="thongbaoXoaPNthanhcong bg-success me-3 mt-3 p-3 rounded-2">
    <p class="mb-0 text-white">       
        Xóa phiếu nhập thành công
    </p>
</div>
        <div class="thongbaoXoaPNKhongThanhCong  bg-danger me-3 mt-3 p-3 rounded-2">
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
              <th>ID BT</th>
              <th>Sản phẩm</th>
              <th>Size</th>
              <th>Màu</th>
              <th>Giá nhập</th>
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

<!-- Modal xóa phiếu nhập -->
<div class="modal fade" id="modalXoaChiTietPN" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Xoá chi tiết phiếu nhập</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <thead>
            <tr class="text-center">
              <th>Mã CTPN</th>
              <th>Mã SP</th>
              <th>Mã Biến Thể</th>
              <th>Số lượng nhập</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody id="body-xoa-ctpn">
            <!-- Render JS -->
          </tbody>
        </table>
        <div id="phantrang-xoa-ctpn" class="d-flex justify-content-center mt-3"></div>
      </div>
      <div class="modal-footer" id="anhienxoa">
        <button id="btnXacNhanXoaPN" class="btn btn-danger">Xác nhận xóa Phiếu nhập</button>
      </div>
    </div>
  </div>
</div>



<!-- Lại là thông báo -->
<!-- Modal thông báo -->
<div class="modal fade" id="modalThongBao" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Thông báo</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <!-- Nội dung thông báo -->
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>


<div id="boxTrungBT" class="thongBaoTrung rounded-2 bg-light p-3 border">
  <p class="mb-0 fs-5 text-center">Đã tồn tại biến thể này rồi!</p>
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
<div class="thongbaoThemBTThanhCong  bg-success me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">   
              Lưu biến thể thành công    
            </p>
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
<script>
  const productListFromPHP = <?= json_encode($productList) ?>;
  const sizeListFromPHP = <?= json_encode($sizeList) ?>;
  const colorListFromPHP = <?= json_encode($colorList) ?>;
</script>
    <script src="./assets/js/fetch_phieuNhap.js"></script>



</body>
</html>
