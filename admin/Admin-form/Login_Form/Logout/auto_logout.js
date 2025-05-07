// auto_logout.js

// Khi trang được load lại (F5 hoặc quay lại bằng nút Back) → đánh dấu không logout
window.addEventListener("pageshow", function (event) {
    sessionStorage.setItem("stayActive", "true");
});

// Khi unload (trước khi đóng hoặc reload)
window.addEventListener("beforeunload", function () {
    const shouldLogout = sessionStorage.getItem("stayActive") !== "true";
    if (shouldLogout) {
        navigator.sendBeacon('/admin/index.php?action=logout_on_close');
    }
    // Reset lại cờ
    sessionStorage.setItem("stayActive", "false");
});
