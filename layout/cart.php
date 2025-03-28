<?php
echo '<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <title>sagkuto</title>
        <link rel="icon" type="./Images/png" href="../assets/img/logo_favicon/favicon.png">
        <link rel="stylesheet" href="../assets/fonts/font.css">
        <link rel="stylesheet" href="../assets/css/product.css">
        <link rel="stylesheet" href="../assets/css/footer.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>
';
?>
<?php
echo '
<body>
    <!-- Breadcrumb -->
    <div class="container-md mt-3">
        <div class="border rounded py-2 px-4 d-flex align-items-center">
            <div class="me-auto">
                <p class="mb-0">
                    <a href="../index.php" class="text-decoration-none link-primary aHover">
                        Trang chủ
                    </a>
                    <span class="mx-2"><i class="fa-solid fa-angle-right"></i></span>
                    <span class="text-dark">Giỏ hàng</span>
                </p>
            </div>
            <div>
                <a href="../login.html" title="Đăng nhập" class="text-dark">
                    <span class="material-symbols-outlined fs-3">person</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Nội dung giỏ hàng -->
    <div class="container-md mt-4">
        <div class="p-4">
            <h5 class="mb-3">Giỏ hàng của bạn</h5>
            <div id="cart-items">
            <p class="text-muted">Hiện tại giỏ hàng đang trống.</p>
            <a href="../index.php" class="btn btn-dark px-4 py-2 text-uppercase fw-bold">
                Continue Shopping
            </a>
            </div>
        </div>
    </div>
</body>
';
?>


<!-- js -->
<?php
   echo '<script src="/Webbanquanao/assets/js/cart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
   </body>
   </html>';
?>