<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

function handleQuenMatKhau($conn) {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        echo json_encode(["status" => "MISSING_EMAIL"]);
        exit();
    }

    // Kiểm tra email có tồn tại
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo json_encode(["status" => "NO_ACCOUNT"]);
        exit();
    }

    // Sinh OTP và hạn
    $otp = rand(100000, 999999);
    $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    // Lưu vào DB
    $stmtUpdate = $conn->prepare("UPDATE users SET otp = ?, otp_expired_at = ? WHERE email = ?");
    $stmtUpdate->bind_param("sss", $otp, $expiry, $email);
    $stmtUpdate->execute();
    $stmtUpdate->close();

    $_SESSION['otp_email'] = $email;
    $_SESSION['otp'] = $otp;

    // Gửi OTP bằng PHPMailer
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'hahodat1958@gmail.com';             // Email gửi
        $mail->Password = 'mtqz syoi iewo febb';                // Mật khẩu ứng dụng
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('hahodat1958@gmail.com', 'Sagkuto');
        $mail->addAddress($email);                              // Người nhận
        $mail->addReplyTo('hahodat1958@gmail.com', 'Hỗ trợ Sagkuto');

        $mail->isHTML(true);
        $mail->Subject = 'Sagkuto - OTP confirmed';
        $mail->Body = "
            <html>
            <body>
                <p>Xin chào,</p>
                <p>Bạn đã yêu cầu đặt lại mật khẩu tại <b>Sagkuto</b>.</p>
                <p>Mã OTP của bạn là: <b>$otp</b></p>
                <p>Mã có hiệu lực trong 5 phút.</p>
                <p>Nếu không phải bạn yêu cầu, vui lòng bỏ qua email này.</p>
            </body>
            </html>";
        $mail->AltBody = "Mã OTP của bạn là: $otp. Có hiệu lực trong 5 phút.";

        $mail->send();

        echo json_encode([
            "status" => "FORGOT_SUCCESS",
            "message" => "Đã gửi OTP đến email"
        ]);
        exit();
    } catch (Exception $e) {
        echo json_encode([
            "status" => "SEND_FAILED",
            "message" => "Không thể gửi OTP. Lỗi: " . $mail->ErrorInfo
        ]);
        exit();
    }
}
