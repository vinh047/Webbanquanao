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
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../database/DBConnection.php';
$dbInstance = DBConnect::getInstance();  // ✅ chỉ gọi 1 lần
$conn = $dbInstance->getConnection();

// ✅ Lấy danh sách cần dùng để truyền vào JS
$productList = $conn->query("SELECT product_id, name FROM products")->fetchAll(PDO::FETCH_ASSOC);
$sizeList = $conn->query("SELECT size_id, name FROM sizes ORDER BY size_id ASC")->fetchAll(PDO::FETCH_ASSOC);
$colorList = $conn->query("SELECT color_id, name FROM colors ORDER BY color_id ASC")->fetchAll(PDO::FETCH_ASSOC);

// ✅ Phân quyền
$user_id = $_SESSION['user_id'] ?? null;
$role_id = $_SESSION['role_id'] ?? null;

if ($role_id) {
    $permissions = $dbInstance->select("SELECT action FROM role_permission_details WHERE role_id = ? AND permission_id = 1", [$role_id]);
    $_SESSION['permissions'] = array_column($permissions, 'action');
}

$permissions = $_SESSION['permissions'] ?? [];
$hasReadPermission = in_array('read', $permissions);
$hasWritePermission = in_array('write', $permissions);
$hasDeletePermission = in_array('delete', $permissions);
$hasAnyActionPermission = $hasReadPermission || $hasWritePermission || $hasDeletePermission;

// ✅ Truyền quyền vào HTML
$permissionsJson = json_encode($permissions);

// ✅ Dữ liệu danh mục và sản phẩm
$categories = $dbInstance->select("SELECT * FROM categories", []);
$product = $dbInstance->select("
    SELECT p.*, c.name as category_name
    FROM products p
    JOIN categories c ON p.category_id = c.category_id
    ORDER BY p.product_id ASC
", []);
?>


</head>
<body>
        <!-- Thẻ ẩn để chứa giá trị role_id -->
<!-- Thẻ ẩn để chứa dữ liệu quyền -->
<div id="permissions" data-permissions='<?= $permissionsJson ?>' style="display:none;"></div>



        <section class="py-3">
                <div class="boloc ms-5 position-relative">
                    <div class="">
                    <button type="button" class="btn btn-secondary" id="btnThemSanPhamMoi">
    <i class="fa fa-plus"></i> Thêm SP mới
  </button>
    <button id="btnMoModalBienThe" class="btn btn-warning text-white"><i class="fa fa-plus"></i> Thêm biến thể</button>
                    </div>
                    <span class="fs-3"><i class="fa-solid fa-filter filter-icon" id="filter-icon" title="Lọc sản phẩm"></i> <span class="fs-5">Lọc danh sách sản phẩm</span> </span>
                    <div class="filter-loc position-absolute bg-light p-3 rounded-2 d-none" style="width:270px;z-index : 2000;border:1px solid black;">
                        <form action="" method="POST" id="formLoc">
                            <div class="d-flex">
                                <div class="me-auto">
                                    <h5>Lọc sản phẩm</h5>
                                </div>
                                <div class="">
                                    <button class="btn btn-outline-secondary btn-sm border-0" type="button" id="tatFormLoc" >X</button>
                                </div>  
                            </div>
                            <label for="txtIDSP">Mã sản phẩm : </label>
                            <input type="text" name="txtIDSP" id="txtIDSP" class="form-control form-control-sm">
                            <label for="txtTensp" class="mt-2">Tên sản phẩm</label>
                            <input type="text" name="txtTensp" id="txtTensp" class="form-control form-control-sm">
                            <label for="cbTheLoai" class="mt-2">Thể loại : </label>
                            <select name="cbTheLoai" id="cbTheLoai" class="form-select select2">
                                <option value="">Chọn thể loại</option>
                                <?php foreach($categories as $theloai): ?>
                                <option value="<?=$theloai['category_id']?>"><?=$theloai['name']?></option>
                                <?php endforeach ?>
                            </select>
                            <div class="d-flex gap-2 mt-2">
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
                                <button class="btn btn-primary" style="width:70px;" type="button" id="btnLocSP">Lọc</button>
                                <button class="btn btn-danger"  style="width:70px;" type="reset">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

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

          <!-- <div class="mb-3">
            <label for="txtGia" class="form-label">Giá sản phẩm:</label>
            <input type="text" name="txtGia" id="txtGia" class="form-control" placeholder="Giá của sản phẩm">
          </div> -->

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


            <div class="hienthi">
                <table class="table border-start border-end table-striped table-sm">
                    <thead>
                        <tr class="text-center">
                            <th class="bg-secondary text-white hienthiid">ID</th>
                            <th class="bg-secondary text-white tensp giaodienmb">Tên sản phẩm</th>
                            <th class="bg-secondary text-white hienthiloai giaodienmb">Loại</th>
                            <th class="bg-secondary text-white mota giaodienmb">Mô tả Sản phẩm</th>
                            <!-- <th class="bg-secondary text-white hienthigia">Giá nhập</th> -->
                            <th class="bg-secondary text-white hienthigia giaodienmb">Giá bán</th>
                            <?php if ($hasAnyActionPermission): ?>
                            <th class="bg-secondary text-white hienthibtn">Xử lý</th>
                            <?php endif; ?>
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
                Xóa sản phẩm thất bại
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
        
<!-- Modal Sửa sản phẩm -->
<div class="modal fade" id="modalSuaSanPham" tabindex="-1" aria-labelledby="modalSuaSanPhamLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalSuaSanPhamLabel">Sửa thông tin sản phẩm</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>

      <div class="modal-body">
        <form action="../ajax/updateSanPham.php" method="POST" id="formSua">
          
          <div class="mb-3">
            <label for="txtId" class="form-label">ID sản phẩm:</label>
            <input type="text" class="form-control bg-light" name="id" id="txtId" readonly>
          </div>

          <div class="mb-3">
            <label for="txtTenSua" class="form-label">Tên sản phẩm:</label>
            <input type="text" name="ten" id="txtTenSua" class="form-control">
          </div>

          <div class="mb-3">
            <label for="txtMotaSua" class="form-label">Mô tả sản phẩm:</label>
            <textarea name="mota" id="txtMotaSua" class="form-control"></textarea>
          </div>

          <div class="mb-3">
            <label for="cbLoaiSua" class="form-label">Loại sản phẩm:</label>
            <select name="loai" id="cbLoaiSua" class="form-select">
              <option value="">Chọn loại sản phẩm</option>
              <?php foreach($categories as $loai): ?>
                  <option value="<?= $loai['category_id'] ?>"><?= $loai['name'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="txtGiaSua" class="form-label">Giá nhập:</label>
            <input type="text" name="gia" id="txtGiaSua" class="form-control bg-light" readonly>
          </div>

          <div class="mb-3">
            <label for="txtPttg" class="form-label">Phần trăm tăng giá:</label>
            <input type="text" name="pttg" id="txtPttg" class="form-control">
          </div>

          <div class="mb-3">
            <label for="txtGiaBanSua" class="form-label">Giá sản phẩm:</label>
            <input type="text" name="giaban" id="txtGiaBanSua" class="form-control">
          </div>

          <div class="d-flex justify-content-center gap-3 pt-3">
            <button type="submit" class="btn btn-success" style="width: 100px;">Xác nhận</button>
            <button type="button" class="btn btn-danger" style="width: 100px;" data-bs-dismiss="modal">Hủy</button>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>






            </div>

    </section>


    <div class="thongBaoQuyen bg-danger me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
                Bạn không có quyền thực hiện chức năng này
            </p>
        </div>

        <div class="thongbaoThemSp bg-success me-3 mt-3 p-3 rounded-2">
    <p class="mb-0 text-white">       
        Thêm sản phẩm mới thành công
    </p>
</div>

        <!-- Chi tiết sản phẩm -->
<div class="modal fade" id="modalChiTietSP" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">Chi tiết sản phẩm</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
          <p><strong>Mã sản phẩm:</strong> <span id="idSP"></span></p>
          <p><strong>Tên sản phẩm:</strong> <span id="tenNSP"></span></p>
          <p><strong>Loại sản phẩm:</strong> <span id="loaiSP"></span></p>
          <p><strong>Mô tả sản phẩm:</strong> <span id="motaSP"></span></p>
          <p><strong>Giá nhập:</strong> <span id="gianhapSP"></span> VNĐ</p>
          <p><strong>Giá bán:</strong> <span id="giabanSP"></span> VNĐ</p>
          <p><strong>Phần trăm tăng giá:</strong> <span id="pttgSP"></span>%</p>
        </div>

        <table class="table table-bordered" id="chitiet-phieunhap">
          <thead>
            <tr class="text-center">
                <th>#</th>
              <th>Mã biến thể</th>
              <th>Sản phẩm</th>
              <th>Size</th>
              <th>Màu</th>
              <th>Ảnh</th>
              <th>Tồn kho</th>
            </tr>
          </thead>
          <tbody>
            <!-- JS sẽ render -->
          </tbody>
        </table>

        <div id="modal-pagination" class="d-flex justify-content-center align-items-center gap-2 mt-3"></div>
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
                <?php foreach ($productList as $ten): ?>
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
<div class="thongbaoThemBTThanhCong  bg-success me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">   
              Lưu biến thể thành công    
            </p>
        </div>
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
<script>
  const productListFromPHP = <?= json_encode($productList) ?>;
  const sizeListFromPHP = <?= json_encode($sizeList) ?>;
  const colorListFromPHP = <?= json_encode($colorList) ?>;
    console.log("✅ Product list:", productListFromPHP);  // 👈 kiểm tra xem có ra không

</script>

    <script src="./assets/js/fetch_sanpham.js"></script>
</body>
</html>