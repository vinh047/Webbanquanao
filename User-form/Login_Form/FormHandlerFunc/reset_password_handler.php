<?php
function handleResetPassword($conn) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    header('Content-Type: application/json');

    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($newPassword) || $newPassword !== $confirmPassword) {
        echo json_encode(['status' => 'RESET_FAILED']);
        exit();
    }

    $email = $_SESSION['otp_email'] ?? null;
    if (!$email) {
        echo json_encode(['status' => 'RESET_FAILED']);
        exit();
    }

    // Lấy mật khẩu cũ từ DB
    $stmtOld = $conn->prepare("SELECT password FROM users WHERE email = ?");
    $stmtOld->bind_param("s", $email);
    $stmtOld->execute();
    $result = $stmtOld->get_result();
    $user = $result->fetch_assoc();
    $stmtOld->close();

    if (!$user) {
        echo json_encode(['status' => 'RESET_FAILED']);
        exit();
    }

    // So sánh mật khẩu mới và mật khẩu cũ
    if (password_verify($newPassword, $user['password'])) {
        echo json_encode(['status' => 'SAME_AS_OLD_PASSWORD']);
        exit();
    }

    // Mã hoá mật khẩu mới
    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

    // Cập nhật vào DB
    $stmt = $conn->prepare("UPDATE users SET password = ?, otp = NULL, otp_expired_at = NULL WHERE email = ?");
    $stmt->bind_param("ss", $hashed, $email);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['status' => 'RESET_SUCCESS']);
    exit();
}
