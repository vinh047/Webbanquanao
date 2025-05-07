<?php
function handleXacNhanOtp($conn) {
    session_start();
    $otp = $_POST['otp'] ?? '';
    $email = $_SESSION['otp_email'] ?? '';
    $new_pass = $_POST['new_password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    if (!isset($_SESSION['otp']) || $otp != $_SESSION['otp']) {
        echo json_encode(["status" => "INVALID_OTP", "message" => "OTP không đúng"]);
        return;
    }

    if ($new_pass !== $confirm_pass) {
        echo json_encode(["status" => "MISMATCH_PASSWORD", "message" => "Mật khẩu xác nhận không khớp"]);
        return;
    }

    $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashed, $email);

    if ($stmt->execute()) {
        unset($_SESSION['otp'], $_SESSION['otp_email']);
        echo json_encode(["status" => "RESET_SUCCESS", "message" => "Đặt lại mật khẩu thành công"]);
    } else {
        echo json_encode(["status" => "RESET_FAILED", "message" => "Lỗi khi đặt lại mật khẩu"]);
    }
}
