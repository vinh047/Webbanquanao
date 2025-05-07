<?php
// Đảm bảo bật session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Bật debug khi cần
ini_set('display_errors', 1);
error_reporting(E_ALL);

function handleQuenMatKhau($conn) {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "MISSING_EMAIL"
        ]);
        return;
    }

    // Kiểm tra email có trong database không
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "NO_ACCOUNT"
        ]);
        return;
    }

    // Sinh OTP và lưu vào session
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_email'] = $email;

    // Trả về JSON sạch
    header('Content-Type: application/json');
    echo json_encode([
        "status" => "FORGOT_SUCCESS",
        "message" => "Đã gửi OTP đến email",
        "otp" => $otp // ⚠️ Khi live có thể bỏ dòng này
    ]);
}
?>
