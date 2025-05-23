<?php
require_once  'Admin-form/Login_Form/Logout/admin_auth.php'; // Chuc nang logout khi close tab, ấn nút , không đăng nhập

require_once __DIR__ . '/ajax/permission_helper.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý cửa hàng</title>
    <link rel="icon" type="./Images/png" href="/assets/img/logo_favicon/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- <link rel="stylesheet" href="../../assets/fonts/font.css"> -->
    <link rel="stylesheet" href="./assets/css/sanpham.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../assets/fonts/font.css">
</head>

<body>
    <section class="d-flex position-relative">
        <?php
        if (session_status() == PHP_SESSION_NONE) {
            session_start(); // Chỉ gọi session_start() nếu session chưa được bắt đầu
        }
        // Kiểm tra xem người dùng đã đăng nhập chưa và lấy role_id từ session
        // $user_id = $_SESSION['user_id'] ?? null;
        $user_id = $_SESSION['admin_id'] ?? null;

        $role_id = $_SESSION['role_id'] ?? null;
        

        if ($user_id) {
            // Kết nối đến cơ sở dữ liệu và lấy thông tin người dùng nếu cần
            require_once(__DIR__ . '../../database/DBConnection.php');
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
        $currentPage = $_GET['page'] ?? ''; // lấy trang hiện tại
        ?>

        <nav class="nav-left text-white p-3">
            <div class="text-center mb-4">
                <img src="../../assets/img/logo_favicon/logo.png" alt="logo" class="img-fluid" style="height:80px; width:100%;">
            </div>

            <ul class="nav flex-column">
                <?php if (hasPermission('Quản lý đơn nhập')): ?>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-white d-flex align-items-center gap-2 <?= $currentPage === 'phieunhap' ? 'active' : '' ?>" href="index.php?page=phieunhap&pageadmin=1">
                            <i class="fa-solid fa-file-import"></i> <span>Phiếu nhập</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (hasPermission('Quản lý đơn nhập')): ?>
                    <li class="nav-item mb-2 d-none">
                        <a class="nav-link text-white d-flex align-items-center gap-2 <?= $currentPage === 'ctphieunhap' ? 'active' : '' ?>" href="index.php?page=ctphieunhap&pageadmin=1">
                            <i class="fa-solid fa-list"></i> <span>Chi tiết phiếu nhập</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (hasPermission('Quản lý sản phẩm')): ?>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-white d-flex align-items-center gap-2 <?= $currentPage === 'sanpham' ? 'active' : '' ?>" href="index.php?page=sanpham&pageadmin=1">
                            <i class="fa-solid fa-box-open"></i> <span>Sản phẩm</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (hasPermission('Quản lý sản phẩm')): ?>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-white d-flex align-items-center gap-2 <?= $currentPage === 'bienthe' ? 'active' : '' ?>" href="index.php?page=bienthe&pageadmin=1">
                            <i class="fa-solid fa-cubes"></i> <span>Biến thể sản phẩm</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (hasPermission('Quản lý nhà cung cấp')): ?>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-white d-flex align-items-center gap-2 <?= $currentPage === 'nhacungcap' ? 'active' : '' ?>" href="index.php?page=nhacungcap&pageadmin=1">
                            <i class="fas fa-truck"></i> <span>Nhà cung cấp</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (hasPermission('Quản lý thuộc tính')): ?>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-white d-flex align-items-center gap-2 <?= $currentPage === 'thuoctinh' ? 'active' : '' ?>" href="index.php?page=thuoctinh&pageadmin=1">
                            <i class="fas fa-sliders-h"></i> <span>Thuộc tính</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (hasPermission('Quản lý quyền')): ?>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-white d-flex align-items-center gap-2 <?= $currentPage === 'phanquyen' ? 'active' : '' ?>" href="index.php?page=phanquyen&pageadmin=1">
                            <i class="fas fa-user-shield"></i> <span>Phân quyền</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (hasPermission('Quản lý đơn hàng')): ?>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-white d-flex align-items-center gap-2 <?= $currentPage === 'donhang' ? 'active' : '' ?>" href="index.php?page=donhang&pageadmin=1">
                            <i class="fa fa-cart-plus"></i> <span>Đơn hàng</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (hasPermission('Quản lý khách hàng')): ?>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-white d-flex align-items-center gap-2 <?= $currentPage === 'khachhang' ? 'active' : '' ?>" href="index.php?page=khachhang&pageadmin=1">
                            <i class="fa-solid fa-users"></i> <span>Khách hàng</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (hasPermission('Quản lý nhân viên')): ?>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-white d-flex align-items-center gap-2 <?= $currentPage === 'nhanvien' ? 'active' : '' ?>" href="index.php?page=nhanvien&pageadmin=1">
                            <i class="fa-solid fa-user-tie"></i> <span>Nhân viên</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (hasPermission('Quản lý tài khoản ngân hàng')): ?>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-white d-flex align-items-center gap-2 <?= $currentPage === 'taikhoannganhang' ? 'active' : '' ?>" href="index.php?page=taikhoannganhang&pageadmin=1">
                            <i class="fa-solid fa-credit-card"></i> <span>Tài khoản ngân hàng</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (hasPermission('Quản lý thuộc tính')): ?>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-white d-flex align-items-center gap-2 <?= $currentPage === 'vouchers' ? 'active' : '' ?>" href="index.php?page=vouchers&pageadmin=1">
                            <i class="fa-solid fa-ticket"></i> <span>Vouchers</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (hasPermission('Xem báo cáo')): ?>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-white d-flex align-items-center gap-2 <?= $currentPage === 'thongke' ? 'active' : '' ?>" href="index.php?page=thongke&pageadmin=1">
                            <i class="fas fa-chart-line"></i> <span>Thống kê</span>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item mb-2">
                    <a class="nav-link text-white d-flex align-items-center gap-2" href="index.php?action=logout">
                        <i class="fa-solid fa-sign-out"></i> <span>Đăng xuất</span>
                    </a>
                </li>


            </ul>
        </nav>




        <div class="quanlysp flex-fill me-3">
            <div class="infouser row p-2" style="background-color: #f8f9fa;">
                <div class="col-md text-end">
                    <p class="mb-0 fs-5">Xin chào, <i><?= htmlspecialchars($name) ?></i></p>
                </div>
            </div>
            <?php
            if (isset($_GET['page'])) {
                $page = $_GET['page'];
                $subpage = $_GET['subpage'] ?? null;

                // Nếu người dùng chỉ vào trang thuộc tính chính (không có subpage), thì xóa session subpage
                if ($page === 'thuoctinh' && !$subpage && isset($_SESSION['last_subpage'])) {
                    unset($_SESSION['last_subpage']);
                }

                // Ưu tiên lấy subpage từ URL, nếu không có thì lấy từ session
                $subpage = $subpage ?? ($_SESSION['last_subpage'] ?? null);

                if ($page === 'thuoctinh' && $subpage) {
                    $_SESSION['last_subpage'] = $subpage;
                }

                switch ($page) {
                    case 'phieunhap':
                        include '../admin/layout/quanlyphieunhap.php';
                        break;
                    case 'ctphieunhap':
                        include '../admin/layout/quanlyctphieunhap.php';
                        break;
                    case 'sanpham':
                        include '../admin/layout/quanlysanpham.php';
                        break;
                    case 'bienthe':
                        include '../admin/layout/quanlybienthe.php';
                        break;
                    case 'nhacungcap':
                        include '../admin/layout/quanlynhacungcap.php';
                        break;
                    case 'thuoctinh':
                        switch ($subpage) {
                            case 'mausac':
                                include '../admin/layout/mau_sac.php';
                                break;
                            case 'size':
                                include '../admin/layout/size.php';
                                break;
                            case 'theloai':
                                include '../admin/layout/the_loai.php';
                                break;
                            case 'phuongthucthanhtoan':
                                include '../admin/layout/phuong_thuc_thanh_toan.php';
                                break;
                            default:
                                include '../admin/layout/thuoctinh.php';
                        }
                        break;
                    case 'phanquyen':
                        include '../admin/layout/phan_quyen.php';
                        break;
                    case 'donhang':
                        include '../admin/layout/don_hang.php';
                        break;
                    case 'khachhang':
                        include '../admin/layout/khach_hang.php';
                        break;
                    case 'nhanvien':
                        include '../admin/layout/nhan_vien.php';
                        break;
                    case 'taikhoannganhang':
                        include '../admin/layout/tai_khoan_ngan_hang.php';
                        break;
                    case 'thongke':
                        include '../admin/layout/thongke.php';
                        break;
                    case 'vouchers':
                        include '../admin/layout/vouchers.php';
                        break;
                }
            }
            ?>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <!-- Đạt thêm tắt khi close tab -->
    <!-- Nhúng jQuery trước tất cả -->
    <!-- ✅ JQUERY PHẢI ĐƯỢC NHÚNG TRƯỚC -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 CSS + JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="Admin-form/Login_Form/Logout/auto_logout.js"></script>
    <script src="Admin-form/Login_Form/Validate/validateForm.js"></script>


</body>

</html>