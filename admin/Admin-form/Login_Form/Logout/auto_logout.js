window.addEventListener('beforeunload', function (e) {
    navigator.sendBeacon('/admin/Admin-form/Login_Form/Logout/admin_auth.php?action=logout_on_close');
    e.preventDefault();
    e.returnValue = ''; // Chrome requires this line to show confirmation dialog
});
