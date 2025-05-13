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
    $permissions = $db->select("SELECT action, permission_id FROM role_permission_details WHERE role_id = ? AND permission_id = 1", [$role_id]);

    // Lưu các quyền vào mảng permissions trong session
    $permissionsArray = [];
    foreach ($permissions as $permission) {
        $permissionsArray[] = $permission['action']; // Lưu các hành động vào mảng permissions
    }

    // Lưu các quyền vào session
    $_SESSION['permissions'] = $permissionsArray; // Lưu danh sách quyền vào session
}

$permissions = $_SESSION['permissions'] ?? [];
$hasReadPermission = in_array('read', $permissions);
$hasWritePermission = in_array('write', $permissions);
$hasDeletePermission = in_array('delete', $permissions);
// ✅ Kiểm tra nếu KHÔNG có quyền nào
$hasAnyActionPermission = $hasReadPermission || $hasWritePermission || $hasDeletePermission;

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
                    <span class="fs-3"><i class="fa-solid fa-filter filter-icon" title="Lọc biến thể"></i> <span class="fs-5">Lọc danh sách biến thể</span> </span>
                    <div class="filter-loc position-absolute bg-light p-3 rounded-2 d-none" style="width:270px;z-index : 2000;border:1px solid black;">
                        <form action="" method="POST" id="formLoc">
                        <div class="d-flex">
                                <div class="me-auto">
                                    <h5>Lọc sản phẩm</h5>
                                </div>
                                <div class="">
                                    <button class="btn btn-outline-secondary btn-sm border-0" id="tatFormLoc" >X</button>
                                </div>  
                            </div>
                            <label for="txtIDBT">Mã BT : </label>
                            <input type="text" name="txtIDBT" id="txtIDBT" class="form-control form-control-sm">
                            <label for="txtIDSP" class="pt-2">Mã SP : </label>
                            <input type="text" name="txtIDSP" id="txtIDSP" class="form-control form-control-sm">
                            <label for="cbSizeLoc" class="pt-2">Size : </label>
                            <select name="cbSizeLoc" id="cbSizeLoc" class="form-select select2">
                                <option value="">Chọn size : </option>
                                <?php foreach($size as $s): ?>
                                <option value="<?=$s['size_id']?>"><?=$s['name']?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="cbMauLoc" class="pt-2">Màu : </label>
                            <select name="cbMauLoc" id="cbMauLoc" class="form-select select2">
                                <option value="">Chọn màu : </option>
                                <?php foreach($color as $c): ?>
                                <option value="<?=$c['color_id']?>"><?=$c['name']?></option>
                                <?php endforeach ?>
                            </select>
                            <!-- <label for="txtSoLuong" class="py-2">Số lượng : </label>
                            <input type="text" name="txtSoLuong" id="txtSoLuong" class="form-control form-control-sm"> -->

                            <div class="d-flex justify-content-center gap-2 pt-2">
                                <button class="btn btn-primary" style="width:70px;" type="submit">Lọc</button>
                                <button class="btn btn-danger"  style="width:70px;" type="reset">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>


            <div class="sanpham py-3" style="font-size: 19px;display:none;">


                <form action="../ajax/insertBienThe.php" method="POST" id="formNhapSPbienThe" enctype="multipart/form-data">
                    <div class="">
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
                        <label for="cbSize">Size : </label>
                        <select name="cbSize" id="cbSize" class="form-select">
                            <option value="">Chọn size sản phẩm</option>
                            <?php foreach($size as $s): ?>
                            <option value="<?=$s['size_id']?>"><?=$s['name']?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
    
                    <div class="pt-3">
                        <label for="txtSl">Số lượng sản phẩm : </label>
                        <input type="text" name="txtSl" id="txtSl" class="form-control" readonly placeholder="Số lượng của sản phẩm">
                    </div>

                    <div class="pt-3">
                        <label for="cbMau">Màu : </label>
                        <select name="cbMau" id="cbMau" class="form-select">
                            <option value="">Chọn màu sản phẩm</option>
                            <?php foreach($color as $cl): ?>

                                <option value="<?=$cl['color_id']?>"><?=$cl['name']?></option>


                            <?php endforeach ?>
                        </select>
                    </div>
    
                    <div class="pt-3">
                        <button class="btn btn-outline-primary" type="submit">Thêm biến thể</button>
                    </div>
                </form>

            </div>

            <div class="hienthi">
                <table class="table border-start border-end table-striped table-sm">
                    <thead>
                        <tr class="text-center">
                            <th class="bg-secondary text-white hienthiidbt">ID BT</th>
                            <th class="bg-secondary text-white hienthianh">ID SP</th>
                            <th class="bg-secondary text-white hienthiidsp">Hình ảnh</th>
                            <th class="bg-secondary text-white hienthisize">Size</th>
                            <th class="bg-secondary text-white hienthigia">Số lượng</th>
                            <th class="bg-secondary text-white hienthimau">Màu</th>
                            
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
                Xóa biến thể thành công
            </p>
        </div>

        <div class="thongbaoXoaHiddenThanhCong  bg-success me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
                Đã tạm thời ẩn đi
            </p>
            <p class="mb-0 text-white">       
                Do đã có trong hóa đơn
            </p>
        </div>

        <div class="thongbaoXoaThatBai  bg-danger me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
                Xóa biến thể thất bại
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

        <div class="overlay"></div>

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
        
            </div>
<!-- Modal Sửa Biến Thể Sản Phẩm -->
<div class="modal fade" id="modalSuaBienThe" tabindex="-1" aria-labelledby="modalSuaBienTheLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalSuaBienTheLabel">Sửa thông tin biến thể sản phẩm</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>

      <div class="modal-body">
        <form action="../ajax/updateBienthe.php" method="POST" id="formSuaSPbienThe" enctype="multipart/form-data">
          <input type="hidden" name="txtMaCTPN" id="txtMaCTPN">

          <div class="mb-3">
            <label for="txtMaBt" class="form-label">Mã biến thể:</label>
            <input type="text" name="txtMaBt" id="txtMaBt" placeholder="Mã của biến thể" class="form-control bg-light" readonly>
          </div>
          <div class="mb-3">
            <label for="txtTenspSua" class="form-label">Tên sản phẩm:</label>
            <input type="hidden" name="txtMaSua" id="txtMaSua" placeholder="Mã của sản phẩm" class="form-control bg-light" readonly>
            <input type="text" name="txtTenspSua" id="txtTenspSua" placeholder="Tên của sản phẩm" class="form-control bg-light" readonly>
          </div>

          <div class="mb-3">
            <label for="fileAnhSua" class="form-label">Hình ảnh:</label>
            <input type="file" name="fileAnhSua" id="fileAnhSua" class="form-control">
            <div class="d-flex pt-2">
              <div id="hienthianhSua" style="max-width:170px; max-height:200px;">
                <img src="" alt="" class="img-fluid" style="width:170px; height:200px; object-fit:contain; display:none;">
              </div>
              <div id="tenFileAnhSua" class="text-muted small fst-italic mt-1 ms-2"></div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="cbSizeSua" class="form-label">Size:</label>
              <select name="cbSizeSua" id="cbSizeSua" class="form-select" disabled>
                <option value="">Chọn size sản phẩm</option>
                <?php foreach($size as $s): ?>
                <option value="<?= $s['size_id'] ?>"><?= $s['name'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label for="cbMauSua" class="form-label">Màu:</label>
              <select name="cbMauSua" id="cbMauSua" class="form-select" disabled>
                <option value="">Chọn màu sản phẩm</option>
                <?php foreach($color as $cl): ?>
                <option value="<?= $cl['color_id'] ?>"><?= $cl['name'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label for="txtSlSua" class="form-label">Số lượng sản phẩm:</label>
            <input type="text" name="txtSlSua" id="txtSlSua" class="form-control bg-light" readonly placeholder="Số lượng của sản phẩm">
          </div>

          <div class="d-flex justify-content-center gap-3 pt-3">
            <button type="submit" class="btn btn-success" style="width: 100px;">Xác nhận</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" style="width: 100px;">Hủy</button>
          </div>

        </form>
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


    <!-- Modal Chi tiết biến thể -->
    <div class="modal fade" id="modalChiTietBienThe" tabindex="-1" aria-labelledby="modalChiTietLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="modalChiTietLabel">Chi tiết biến thể</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row align-items-center">
          <div class="col-md-4 text-center">
            <img id="ctbt_image" src="" class="img-fluid rounded border" style="max-height: 280px; object-fit: contain;" alt="Ảnh sản phẩm">
          </div>
          <div class="col-md-8 fs-6">
            <p style="font-size: 17px;"><strong>ID biến thể:</strong> <span id="idbt_sp"></span></p>
            <p style="font-size: 17px;"><strong>Sản phẩm:</strong> <span id="ctbt_tensp"></span></p>
            <p style="font-size: 17px;"><strong>Màu sắc:</strong> <span id="ctbt_mau"></span></p>
            <p style="font-size: 17px;"><strong>Size:</strong> <span id="ctbt_size"></span></p>
            <p><strong>Tồn kho:</strong> <span id="ctbt_sl"></span></p>
          </div>
        </div>
      </div>

        <div class="px-3">
        <table class="table table-bordered" id="chitiet-phieunhap">
          <thead>
            <tr>
              <th class="text-center">#</th>
              <th class="text-center">ID ctpn</th>
              <th class="text-center">ID pn</th>
              <th class="text-center">ID sp</th>
              <th class="text-center">ID bt</th>
              <th class="text-center">Số lượng nhập</th>
              <th class="text-center">Ngày nhập</th>
            </tr>
          </thead>
          <tbody>
            <!-- JS sẽ render -->
          </tbody>
        </table>
        </div>

        <!-- 👇 Phân trang -->
        <div id="modal-pagination" class="d-flex justify-content-center align-items-center gap-2 mb-3"></div>
        <!-- JS sẽ render nút -->
        </div>
      </div>
    </div>
  </div>
</div>

      <script src="./assets/js/fetch_bienthe.js"></script>
</body>
</html>