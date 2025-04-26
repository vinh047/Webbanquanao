
<?php
require_once 'admin_auth.php'; // Chuc nang logout khi close tab, ấn nút , không đăng nhập
require_once '../User-form/Login_Form/get_user_id.php'; //lay user_id de hien thi thong tin 
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
    <link rel="stylesheet" href="../../assets/fonts/font.css">
    <link rel="stylesheet" href="./assets/css/sanpham.css">
</head>
<body>
<section class="d-flex position-relative">
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Chỉ gọi session_start() nếu session chưa được bắt đầu
}

$currentPage = $_GET['page'] ?? ''; // lấy trang hiện tại
?>

<nav class="nav-left text-white p-3">
    <div class="text-center mb-4">  
        <img src="../../assets/img/logo_favicon/logo.png" alt="logo" class="img-fluid" style="height:80px; width:100%;">
    </div>

    <ul class="nav flex-column">
        <li class="nav-item mb-2">
            <a class="nav-link text-white d-flex align-items-center gap-2 <?= $currentPage === 'phieunhap' ? 'active' : '' ?>" href="index.php?page=phieunhap">
                <i class="fa-solid fa-file-import"></i> <span>Phiếu nhập</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link text-white d-flex align-items-center gap-2 <?= $currentPage === 'ctphieunhap' ? 'active' : '' ?>" href="index.php?page=ctphieunhap">
                <i class="fa-solid fa-list"></i> <span>Chi tiết phiếu nhập</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link text-white d-flex align-items-center gap-2 <?= $currentPage === 'sanpham' ? 'active' : '' ?>" href="index.php?page=sanpham">
                <i class="fa-solid fa-box-open"></i> <span>Sản phẩm</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link text-white d-flex align-items-center gap-2 <?= $currentPage === 'bienthe' ? 'active' : '' ?>" href="index.php?page=bienthe">
                <i class="fa-solid fa-cubes"></i> <span>Biến thể sản phẩm</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link text-white d-flex align-items-center gap-2" href="index.php?action=logout">
                <i class="fa-solid fa-sign-out"></i> <span>Đăng xuất</span>
            </a>
        </li>

    </ul>
</nav>




    <div class="quanlysp container-md">
        <div class="infouser row p-2" style="background-color: #f8f9fa;">
            <div class="col-md text-end">
                <p class="mb-0 fs-3"><i class="fa-solid fa-user"></i></p>
            </div>
        </div>
<?php
if(isset($_GET['page']))
{
    $page = $_GET['page'];
    switch($page)
    {
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
    }
}
?>
</div>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
<!-- Đạt thêm tắt khi close tab -->
<script src="auto_logout.js"></script> 
</body>
</html>
