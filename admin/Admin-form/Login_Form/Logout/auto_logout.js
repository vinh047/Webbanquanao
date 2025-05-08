// auto_logout.js

// Khi trang được hiển thị lại (Back, Forward, Ctrl+Shift+T, F5)
window.addEventListener("pageshow", function (event) {
    sessionStorage.setItem("stayActive", "true");

    const navType = performance.getEntriesByType("navigation")[0]?.type;

    // Nếu trang được phục hồi từ bfcache hoặc là thao tác quay lại/khôi phục tab
    if (event.persisted || navType === "back_forward") {
        window.location.reload(); // Ép reload lại để kiểm tra lại session thực tế từ server
    }
});

// Khi người dùng đóng tab hoặc reload
window.addEventListener("beforeunload", function () {
    const shouldLogout = sessionStorage.getItem("stayActive") !== "true";

    if (shouldLogout) {
        navigator.sendBeacon('/admin/index.php?action=logout_on_close');
    }

    // Đặt lại cờ stayActive để kiểm soát lần truy cập kế tiếp
    sessionStorage.setItem("stayActive", "false");
});
