<?php
session_start();
require_once 'connect.php';
require_once 'login_handler.php';

$trangthai = $_POST['trangthai'] ?? 'hienthi';

switch ($trangthai) {
    case 'dangky':
        handleDangKy($conn);
        break;
    case 'dangnhap':
        handleDangNhap($conn);
        break;
    default:
        break;
}

$conn->close();
?>
