// auto_logout.js

// Khi quay lại từ bfcache, Ctrl+Shift+T hoặc Back/Forward → ép reload để PHP kiểm tra lại session
window.addEventListener("pageshow", function (event) {
    const navType = performance.getEntriesByType("navigation")[0]?.type;

    if (event.persisted || navType === "back_forward") {
        console.log("Reloading from bfcache or back navigation");
        window.location.reload();
    }
});
