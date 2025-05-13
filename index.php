<?php
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
    <title>sagkuto</title>
    <link rel="icon" type="image/png" href="./assets/img/logo_favicon/favicon.png">
    <link rel="stylesheet" href="./assets/css/header.css">
    <link rel="stylesheet" href="./assets/css/slide.css">
    <link rel="stylesheet" href="./assets/css/footer.css">
    <link rel="stylesheet" href="./assets/fonts/font.css">
</head>
<body>';

include("./layout/header.php");

if (isset($_GET['page'])) {
    $page = $_GET['page'];
    if (is_numeric($page)) {
        include('./layout/product.php');
    } else {
        switch ($page) {
            case 'ao':
            case 'quan':
            case 'aopolo':
            case 'aosomi':
            case 'aokhoac':
                include('./layout/phanloai.php');
                break;
            case 'sanpham':
                if (isset($_GET['phanloai'])) {
                    include('./layout/phanloai.php');
                } else {
                    include('./layout/product.php');
                }
                break;
            case 'giohang':
                include('./layout/cart.php');
                break;
            case 'pay':
                include('./layout/pay.php');
                break;
            case 'taikhoan':
            case 'danhsachdiachi':
            case 'donhang':
            case 'lichsumuahang':
                include('./layout/info_user.php');
                break;
            case 'search':
                include "layout/search.php";
                break;
            case 'error':
                include('./layout/error404.php');
                break;
        }
    }
} else {
    include('./layout/home.php');
}

include("./layout/footer.php");
?>

<script src="/assets/bootstrap/js/bootstrap.bundle.min.js" defer></script>


<!-- <script src="/assets/js/cart.js"      defer></script>
<script src="/assets/js/header.js"    defer></script>
<script src="/assets/js/info_user.js" defer></script> -->
<?php if (!empty($_SESSION['user_id'])): ?>
<script>
window.addEventListener('load', () => {
  if (typeof syncCartAfterLogin === 'function' && !sessionStorage.getItem('cart_merge_prompted')) {
    syncCartAfterLogin();
  }
});
</script>
<?php endif; ?>

</body>
</html>