<?php
session_start();

// Ngăn cache để tránh hiển thị giao diện cũ sau khi logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Xử lý logout thủ công
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header('Location: Admin-form/Login_Form/Login_Form.php');
    exit();
}

// Kiểm tra quyền đăng nhập
if (!isset($_SESSION['admin_id']) || ($_SESSION['role_id'] ?? 1) == 1) {
    header('Location: Admin-form/Login_Form/Login_Form.php');
    exit();
}
?>
