<?php

function handleDangNhap($conn) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['pswd'] ?? '';

    if (!$email || !$password) {
        echo json_encode(['status' => 'MISSING_FIELDS']);
        return;
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo json_encode(['status' => 'NO_ACCOUNT']);
        return;
    }

    if (!password_verify($password, $user['password'])) {
        echo json_encode(['status' => 'INVALID_PASSWORD']);
        return;
    }

    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['role_id'] = $user['role_id'];

    echo json_encode([
        'status' => 'LOGIN_SUCCESS',
        'role' => (int)$user['role_id']
    ]);
}
