<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Đường dẫn đến thư viện PHPMailer
require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

// Khởi tạo
$mail = new PHPMailer(true);

try {
    // Cấu hình SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'hahodat1958@gmail.com';        // Email gửi
    $mail->Password = 'mtqz syoi iewo febb';           // Mật khẩu ứng dụng Gmail
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Người gửi và người nhận
    $mail->setFrom('hahodat1958@gmail.com', 'Test Mail');
    $mail->addAddress('hahodat0803@gmail.com', 'Người nhận');

    // Nội dung
    $mail->isHTML(true);
    $mail->Subject = 'Test gửi mail';
    $mail->Body = 'Hello! Đây là email test gửi bằng PHPMailer.';

    // Gửi
    $mail->send();
    echo '✅ Gửi thành công đến hahodat0803@gmail.com';
} catch (Exception $e) {
    echo '❌ Gửi thất bại. Lỗi: ', $mail->ErrorInfo;
}
