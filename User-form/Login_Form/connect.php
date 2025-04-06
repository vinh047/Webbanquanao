<?php
$servername = "localhost";   // hoặc 127.0.0.1
$username = "root";          // tài khoản MySQL (thường là root)
$password = "";              // mật khẩu (mặc định là rỗng với XAMPP)
$database = "db_web_quanao";     // tên CSDL của bạn

$conn = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("❌ Kết nối thất bại: " . $conn->connect_error);
}

?>
