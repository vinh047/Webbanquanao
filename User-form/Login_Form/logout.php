<?php
// logout.php
session_start();

// 1) Xóa toàn bộ biến session
$_SESSION = [];

// 2) Hủy session trên server
session_destroy();

// 3) Nếu đang dùng cookie để giữ session, xóa luôn cookie phiên
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// 4) Redirect về index với flag loggedout=1
header('Location: /index.php?loggedout=1');
exit;
