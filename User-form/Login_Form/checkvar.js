document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('mainformmainform');

    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Chặn form submit mặc định

        // Lấy giá trị từ form
        const username = document.querySelector('input[name="username"]').value.trim();
        const email = document.querySelector('input[name="email"]').value.trim();
        const pswd = document.querySelector('input[name="pswd"]').value;
        const sdt = document.querySelector('input[name="sdt"]').value.trim();
        const ngaysinh = document.querySelector('input[name="ngaysinh"]').value;
        const gioitinhChecked = document.querySelector('input[name="gioitinh"]:checked');

        // Kiểm tra Họ tên
        if (username === '') {
            alert('Username không được để trống.');
            return false;
        }

        // Kiểm tra email hợp lệ
        const emailRegex = /^[\w.-]+@[\w.-]+\.\w{2,}$/;
        if (!emailRegex.test(email)) {
            alert('Email không hợp lệ.');
            return false;
        }

        // Kiểm tra mật khẩu (ít nhất 6 ký tự, bao gồm chữ và số)
        const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/;
        if (!passwordRegex.test(pswd)) {
            alert('Mật khẩu yếu (ít nhất 6 ký tự, bao gồm cả chữ và số).');
            return false;
        }

        // Kiểm tra số điện thoại (+84 hoặc 0 đầu, theo sau là 9 hoặc 10 số)
        const phoneRegex = /^(?:\+84|0)\d{9,10}$/;
        if (!phoneRegex.test(sdt)) {
            alert('Số điện thoại không hợp lệ (bắt đầu bằng +84 hoặc 0 và đủ 9-10 chữ số theo sau).');
            return false;
        }

        // Kiểm tra ngày sinh
        if (ngaysinh === '') {
            alert('Ngày sinh không được để trống.');
            return false;
        }

        // Kiểm tra giới tính (phải chọn 1 trong 2)
        if (!gioitinhChecked) {
            alert('Bạn phải chọn giới tính.');
            return false;
        }

        // Nếu tất cả hợp lệ
        alert('Đăng ký thành công!');
        
        form.submit(); // gửi form đi nếu dữ liệu đã hợp lệ
    });
});
