<?php

function handleDangKy($conn) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['pswd'] ?? '';
    $sdt = trim($_POST['sdt'] ?? '');
    $diachi = trim($_POST['diachi'] ?? '');

    if (!$username || !$email || !$password || !$sdt || !$diachi) {
        die("Vui lòng nhập đầy đủ thông tin.");
    }

    // Kiểm tra email đã tồn tại
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        die("Email đã tồn tại.");
    }

    // Lấy user_id tiếp theo
    $result = $conn->query("SELECT MAX(user_id) AS max_id FROM users");
    $row = $result->fetch_assoc();
    $next_id = ($row['max_id'] !== null) ? $row['max_id'] + 1 : 1;

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $role_id = 1;
    $status = 1;

    $stmt = $conn->prepare("INSERT INTO users (user_id, username, email, password, phone, address, role_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssi", $next_id, $username, $email, $hashed, $sdt, $diachi, $role_id, $status);

    if ($stmt->execute()) {
        echo "✅ Đăng ký thành công!";
    } else {
        echo "❌ Lỗi khi đăng ký: " . $stmt->error;
    }
}

function handleDangNhap($conn) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['pswd'] ?? '';

    if (!$email || !$password) {
        die("Vui lòng nhập email và mật khẩu.");
    }

    $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Nếu tồn tại user so sanh ca 2 cai: ma hoa va khong ma hoa
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            echo "✅ Đăng nhập thành công!";
        } else {
            echo "❌ Sai mật khẩu.";
        }
    } else {
        echo "❌ Tài khoản không tồn tại.";
    }
    
}


