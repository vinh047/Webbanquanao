<?php
// Xử lý đăng ký tài khoản
function handleDangKy($conn) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['pswd'] ?? '';
    $sdt = trim($_POST['sdt'] ?? '');

    if (!$name || !$email || !$password || !$sdt) {
        echo json_encode(["status" => "MISSING_FIELDS"]);
        return;
    }
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo json_encode(["status" => "UNAME_EXISTS"]);
        return;
    }

    // Kiểm tra email đã tồn tại
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo json_encode(["status" => "EMAIL_EXISTS"]);
        return;
    }
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE phone = ?");
    $stmt->bind_param("s", $sdt);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo json_encode(["status" => "PHONE_EXISTS"]);
        return;
    }



    // Tạo user_id mới
    $result = $conn->query("SELECT MAX(user_id) AS max_id FROM users");
    $row = $result->fetch_assoc();
    $next_id = ($row['max_id'] !== null) ? $row['max_id'] + 1 : 1;

    // Mã hóa mật khẩu
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $role_id = 1; // Mặc định là user
    $status = 1; // Trạng thái hoạt động

    // Thêm người dùng mới vào cơ sở dữ liệu
    $stmt = $conn->prepare("INSERT INTO users (user_id, name, email, password, phone, role_id, status)
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssii", $next_id, $name, $email, $hashed, $sdt, $role_id, $status);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'REGISTER_SUCCESS',
            'role' => $role_id
    ]);
    } else {
        echo json_encode(["status" => "ERROR"]);
    }
}

// Xử lý đăng nhập
function handleDangNhap($conn) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['pswd'] ?? '';

    if (!$email || !$password) {
        echo json_encode(["status" => "MISSING_FIELDS"]);
        return;
    }

    $stmt = $conn->prepare("SELECT user_id, password, role_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Kiểm tra mật khẩu
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            // Lưu session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role_id'] = $user['role_id'];

            // Trả về dữ liệu JSON
            echo json_encode([
                'status' => 'LOGIN_SUCCESS',
                'role' => $user['role_id']
            ]);
        } else {
            echo json_encode(["status" => "INVALID_PASSWORD"]);
        }
    } else {
        echo json_encode(["status" => "NO_ACCOUNT"]);
    }
}
// Xử lý đăng ký tài khoản

?>