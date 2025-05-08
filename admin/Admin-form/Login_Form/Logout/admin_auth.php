<?php
session_start();

// Ngăn trình duyệt cache lại trang sau khi logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Nếu logout do đóng tab
if (isset($_GET['action']) && $_GET['action'] === 'logout_on_close') {
    session_unset();
    session_destroy();
    exit();
}

// Nếu logout chủ động (nhấn logout)
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header('Location: Admin-form/Login_Form/Login_Form.php');
    exit();
}

// Kiểm tra quyền truy cập
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] ?? 1) == 1) {
    header('Location: Admin-form/Login_Form/Login_Form.php');
    exit();
}
?>
