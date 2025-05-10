<?php
require_once  'Admin-form/Login_Form/Logout/admin_auth.php'; // Chuc nang logout khi close tab, ấn nút , không đăng nhập

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

</head>

<body>
    <section class="d-flex position-relative">
        <?php
        if (session_status() == PHP_SESSION_NONE) {
            session_start(); // Chỉ gọi session_start() nếu session chưa được bắt đầu
        }
        // Kiểm tra xem người dùng đã đăng nhập chưa và lấy role_id từ session
        $user_id = $_SESSION['user_id'] ?? null;
        $role_id = $_SESSION['role_id'] ?? null;

<<<<<<< HEAD
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
=======
        if ($user_id) {
            // Kết nối đến cơ sở dữ liệu và lấy thông tin người dùng nếu cần
            require_once(__DIR__ . '/../database/DBConnection.php');
            $db = DBConnect::getInstance();
>>>>>>> 7e603b32fd7747ad653eda566ff3d24ee1e6402d

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
                <li class="nav-item mb-2">
                    <a class="nav-link text-white d-flex align-items-center gap-2 <?= $currentPage === 'phieunhap' ? 'active' : '' ?>" href="index.php?page=phieunhap&pageadmin=1">
                        <i class="fa-solid fa-file-import"></i> <span>Phiếu nhập</span>
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white d-flex align-items-center gap-2 <?= $currentPage === 'ctphieunhap' ? 'active' : '' ?>" href="index.php?page=ctphieunhap&pageadmin=1">
                        <i class="fa-solid fa-list"></i> <span>Chi tiết phiếu nhập</span>
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white d-flex align-items-center gap-2 <?= $currentPage === 'sanpham' ? 'active' : '' ?>" href="index.php?page=sanpham&pageadmin=1">
                        <i class="fa-solid fa-box-open"></i> <span>Sản phẩm</span>
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white d-flex align-items-center gap-2 <?= $currentPage === 'bienthe' ? 'active' : '' ?>" href="index.php?page=bienthe&pageadmin=1">
                        <i class="fa-solid fa-cubes"></i> <span>Biến thể sản phẩm</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link text-white d-flex align-items-center gap-2 <?= $currentPage === 'nhacungcap' ? 'active' : '' ?>" href="index.php?page=nhacungcap&pageadmin=1">
                        <i class="fas fa-truck"></i> <span>Nhà cung cấp</span>
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link text-white d-flex align-items-center gap-2 <?= $currentPage === 'thuoctinh' ? 'active' : '' ?>" href="index.php?page=thuoctinh&pageadmin=1">
                        <i class="fas fa-sliders-h"></i> <span>Thuộc tính</span>
                    </a>
                </li>

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

</body>

</html>