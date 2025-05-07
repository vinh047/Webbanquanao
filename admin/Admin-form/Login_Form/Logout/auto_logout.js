// ✅ Gửi logout nếu đóng tab hoặc reload
window.addEventListener('beforeunload', function (e) {
    navigator.sendBeacon('/admin/Admin-form/Login_Form/Logout/admin_auth.php?action=logout_on_close');
});

// ✅ Nếu người dùng quay lại bằng nút Back → bắt buộc reload hoặc redirect
window.addEventListener("pageshow", function (event) {
    if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
        // Cách 1: reload lại trang (giúp PHP kiểm tra lại session)
        // window.location.reload();

        // Cách 2: chuyển thẳng về trang login (an toàn hơn, nhẹ hơn)
        window.location.href = "/admin/Admin-form/Login_Form/Login_Form.php";
    }
});
