<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <title>sagkuto</title>
  <link rel="icon" type="image/png" href="/assets/img/logo_favicon/favicon.png">
  <link rel="stylesheet" href="./assets/fonts/font.css">
  <link rel="stylesheet" href="./assets/css/product.css">
  <link rel="stylesheet" href="./assets/css/footer.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>

<body>
  <!-- Breadcrumb -->
  <div class="container-md mt-3">
    <div class="border rounded py-2 px-4 d-flex align-items-center">
      <div class="me-auto">
        <p class="mb-0">
          <a href="index.php" class="text-decoration-none link-primary aHover">
            Trang chủ
          </a>
          <span class="mx-2"><i class="fa-solid fa-angle-right"></i></span>
          <span class="text-dark">Giỏ hàng</span>
        </p>
      </div>
    </div>
  </div>

  <!-- Nội dung giỏ hàng -->

  <div class="container my-5">
    <div class="row">

      <!-- Giỏ hàng bên trái -->
      <div class="col-lg-8" id="cart-left">
        <h4 class="mb-4">GIỎ HÀNG CỦA BẠN</h4>

        <div class="cart-scroll-area border rounded p-3">
          <div class="d-flex align-items-center mb-2">
            <input type="checkbox" id="select-all" class="form-check-input me-2" style="border: 1px solic black;">
            <label for="select-all" class="mb-0">Chọn tất cả</label>
          </div>
          <div id="cart-items"></div>
        </div>
      </div>


      <!-- Tóm tắt đơn hàng bên phải -->

      <div class="col-lg-4" id="order-summary">
        <div class="border p-4 rounded shadow-sm">
          <h6 class="mb-3">Tóm tắt đơn hàng</h6>
          <div class="d-flex justify-content-between mb-2">
            <span>Tạm tính:</span>
            <strong class="text-danger" id="total-price">0₫</strong>
          </div>
          <a href="index.php?page=pay" id="btn-checkout" class="btn btn-dark w-100 fw-bold">THANH TOÁN</a>

          <a href="index.php?page=sanpham" class="btn btn-outline-secondary w-100 mt-2 fw-bold">
            TIẾP TỤC MUA SẮM
          </a>
        </div>
      </div>
    </div>
  </div>

</body>



<!-- js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>