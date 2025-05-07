<?php
session_start();

// Nếu logout từ việc đóng tab
if (isset($_GET['action']) && $_GET['action'] === 'logout_on_close') {
    session_unset();
    session_destroy();
    exit();
}

// Nếu logout chủ động (ấn nút logout)
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header('Location: Admin-form/Login_Form/Login_Form.php');
    exit();
}

// Nếu chưa đăng nhập hoặc không phải admin thì đá về login
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] ?? 1) == 1) {
    header('Location: Admin-form/Login_Form/Login_Form.php');
    exit();
}
