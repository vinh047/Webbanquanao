<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>sagkuto</title>
  <link rel="stylesheet" href="./assets/css/pay.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
  <?php
    require_once './database/DBConnection.php';
    $db = DBConnect::getInstance();
    $payment_methods = $db->select("SELECT * FROM payment_method", []);
  ?>
</head>
<body>
  <div class="container-md mt-3">
    <div class="border rounded py-2 px-4 d-flex align-items-center">
      <div class="me-auto">
        <p class="mb-0">
          <a href="index.php" class="text-decoration-none link-primary aHover">Trang chủ</a>
          <span class="mx-2"><i class="fa-solid fa-angle-right"></i></span>
          <span class="text-dark">Thanh toán</span>
        </p>
      </div>
    </div>
  </div>

  <div class="container my-5">
    <div class="row">
      <!-- Thong tin giao hang -->
      <div class="col-lg-4 mb-4">
        <h5>Thông tin giao hàng</h5>
        <form id="shipping-form" novalidate>
          <div class="row g-2">
            <div class="col-6">
              <input type="text" class="form-control" id="ho" placeholder="Họ" required>
              <div class="invalid-feedback">Họ là bắt buộc</div>
            </div>
            <div class="col-6">
              <input type="text" class="form-control" id="ten" placeholder="Tên" required>
              <div class="invalid-feedback">Tên là bắt buộc</div>
            </div>
          </div>
          <input type="tel" class="form-control mt-2" id="sdt" placeholder="Số điện thoại" required>
          <div class="invalid-feedback">Số điện thoại là bắt buộc</div>
          <input type="email" class="form-control mt-2" id="email" placeholder="Email" required>
          <div class="invalid-feedback">Email là bắt buộc</div>

          <div class="row g-2 mt-2">
            <div class="col-md-4">
              <select id="province" class="form-select" required>
                <option selected disabled>Tỉnh/TP</option>
              </select>
              <div class="invalid-feedback">Tỉnh/Thành phố là bắt buộc</div>
            </div>
            <div class="col-md-4">
              <select id="district" class="form-select" required>
                <option selected disabled>Quận/Huyện</option>
              </select>
              <div class="invalid-feedback">Quận/Huyện là bắt buộc</div>
            </div>
            <div class="col-md-4">
              <select id="ward" class="form-select" required>
                <option selected disabled>Phường/Xã</option>
              </select>
              <div class="invalid-feedback">Phường/Xã là bắt buộc</div>
            </div>
          </div>
          <input type="text" class="form-control mt-2" id="specific-address" placeholder="Địa chỉ cụ thể (Tùy chọn)">
        </form>
      </div>

      <!-- Mua online & phuong thuc -->
      <div class="col-lg-4 mb-4">
        <h5>Mua online</h5>
        <div class="border p-3 mb-3 rounded">
          <div class="form-check">
            <input class="form-check-input" type="radio" name="shipping" id="defaultShipping" checked>
            <label class="form-check-label" for="defaultShipping">Giao hàng tiêu chuẩn (3 - 6 ngày)</label>
          </div>
        </div>

        <h5>Phương thức thanh toán</h5>
        <div class="border p-3 rounded">
          <?php foreach ($payment_methods as $method): ?>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="payment_method" id="pm<?= $method['payment_method_id'] ?>" value="<?= $method['payment_method_id'] ?>" <?= $method['payment_method_id'] == 1 ? 'checked' : '' ?> required>
              <label class="form-check-label" for="pm<?= $method['payment_method_id'] ?>">
                <?= htmlspecialchars($method['name']) ?>
              </label>
            </div>
          <?php endforeach; ?>
        </div>

        <h6 class="mt-4 d-flex justify-content-between">Voucher <a href="#" class="text-decoration-none">Xem tất cả</a></h6>
        <div class="input-group">
          <input type="text" class="form-control" placeholder="Nhập mã giảm giá">
          <button class="btn btn-dark">Áp dụng</button>
        </div>
      </div>

      <!-- Don hang -->
      <div class="col-lg-4 order-summary">
        <h5>Đơn hàng</h5>
        <div id="order-items"></div>
        <ul class="list-group mt-3">
          <li class="list-group-item d-flex justify-content-between"><span>Tạm tính</span><strong id="subtotal">0đ</strong></li>
          <li class="list-group-item d-flex justify-content-between"><span>Phí vận chuyển</span><span>0đ</span></li>
          <li class="list-group-item d-flex justify-content-between"><span>Giảm giá</span><span>0đ</span></li>
          <li class="list-group-item d-flex justify-content-between"><strong>Thành tiền</strong><strong id="total">0đ</strong></li>
        </ul>
        <button class="btn btn-dark w-100 mt-3" onclick="submitOrder()">Đặt hàng</button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/pay.js"></script>
</body>
</html>
