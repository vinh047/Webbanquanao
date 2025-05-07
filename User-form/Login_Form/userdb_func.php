<?php
session_start();

// Kết nối database
require_once 'connect.php';

// Import các handler xử lý từng trạng thái
require_once 'FormHandlerFunc/login_handler.php';
require_once 'FormHandlerFunc/forgot_password_handler.php';
require_once 'FormHandlerFunc/otp_verify_handler.php';

// Lấy trạng thái đang xử lý từ frontend
$trangthai = $_POST['trangthai'] ?? 'hienthi';

// Router: Gọi hàm phù hợp với trạng thái
switch ($trangthai) {
    case 'dangky':
        handleDangKy($conn);
        break;

    case 'dangnhap':
        handleDangNhap($conn);
        break;

    case 'quenmatkhau':
        handleQuenMatKhau($conn);
        break;

    case 'nhapotp':
        handleXacNhanOtp($conn);
        break;

    default:
        break;
}

// Đóng kết nối sau khi xử lý xong
$conn->close();
?>
