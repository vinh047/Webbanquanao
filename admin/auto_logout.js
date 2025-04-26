window.addEventListener('beforeunload', function (e) {
    navigator.sendBeacon('auth.php?action=logout_on_close');
});
