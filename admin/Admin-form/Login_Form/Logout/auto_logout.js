
// Nếu người dùng quay lại trang bằng nút Back → buộc reload lại để kiểm tra session
window.addEventListener("pageshow", function (event) {
    const isBack = event.persisted || (window.performance && window.performance.navigation.type === 2);
    if (isBack) {
        window.location.reload();
    }
});

