<?php
function handleXacNhanOtp($conn) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    header('Content-Type: application/json');

    $email = $_SESSION['otp_email'] ?? null;
    $otp = trim((string)($_POST['otp'] ?? ''));

    if (!$email || !$otp) {
        echo json_encode(['status' => 'INVALID_OTP']);
        exit();
    }

    $stmt = $conn->prepare("SELECT otp, otp_expired_at FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user || $user['otp'] !== $otp || strtotime($user['otp_expired_at']) < time()) {
        echo json_encode(['status' => 'INVALID_OTP']);
        exit();
    }

    echo json_encode(['status' => 'OTP_SUCCESS']);
    exit();
}
