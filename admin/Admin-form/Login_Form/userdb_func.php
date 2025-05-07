<?php
session_start();
require_once 'connect.php';
require_once 'LoginFormHandler/login_handler.php';

$trangthai = $_POST['trangthai'] ?? 'hienthi';

if ($trangthai === 'dangnhap') {
    handleDangNhap($conn);
}

$conn->close();
?>
