<?php

function handleDangNhap($conn) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['pswd'] ?? '';

    if (!$email || !$password) {
        echo json_encode(["status" => "MISSING_FIELDS"]);
        return;
    }

    $stmt = $conn->prepare("SELECT user_id, password, role_id, status FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Kiểm tra mật khẩu
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            // Lưu session
            $_SESSION['admin_id'] = $user['user_id'];
            $_SESSION['role_id'] = $user['role_id'];

            // Trả về dữ liệu JSON
            echo json_encode([
                'status' => 'LOGIN_SUCCESS',
                'role' => $user['role_id'],
                'online' => (int)$user['status'] // = 0 thì bị khóa, = 1 thì đang hoạt động
            ]);
        } else {
            echo json_encode(["status" => "INVALID_PASSWORD"]);
        }
    } else {
        echo json_encode(["status" => "NO_ACCOUNT"]);
    }
}

