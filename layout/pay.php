<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once './database/DBConnection.php';
$db = DBConnect::getInstance();

$user_id = $_SESSION['user_id'] ?? null;
$user = ['name' => '', 'phone' => '', 'email' => ''];
$user_addresses = [];
if ($user_id) {
  $user = $db->selectOne("SELECT name, phone, email FROM users WHERE user_id = ?", [$user_id]);
  $user_addresses = $db->select(
    "SELECT * FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC, updated_at DESC",
    [$user_id]
  );
}
$payment_methods = $db->select("SELECT * FROM payment_method", []);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>sagkuto - Thanh toán</title>
  <link rel="stylesheet" href="./assets/css/pay.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./assets/fonts/font.css">
  <link rel="stylesheet" href="./assets/css/footer.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
</head>

<body>
  <div class="container-md mt-3">
    <div class="border rounded py-2 px-4 d-flex align-items-center">
      <div class="me-auto">
        <p class="mb-0">
          <a href="index.php" class="text-decoration-none link-primary">Trang chủ</a>
          <span class="mx-2"><i class="fa-solid fa-angle-right"></i></span>
          <span class="text-dark">Thanh toán</span>
        </p>
      </div>
    </div>
  </div>

  <div class="container my-5">
    <div class="row">
      <!-- Thông tin giao hàng -->
      <div class="col-lg-4 mb-4">
        <h5>Thông tin giao hàng</h5>
        <form id="shipping-form" novalidate>
          <div class="mb-2">
            <input type="text" class="form-control" id="name" name="name" placeholder="Họ và tên" required
              value="<?= htmlspecialchars($user['name'] ?? '') ?>">
            <div class="invalid-feedback">Họ và tên là bắt buộc</div>
          </div>
          <div class="mb-2">
            <input type="tel" class="form-control" id="sdt" name="phone" placeholder="Số điện thoại" required
              value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
            <div class="invalid-feedback">Số điện thoại là bắt buộc</div>
          </div>
          <div class="mb-3">
            <input type="email" class="form-control" id="email" name="email" placeholder="Email" required
              value="<?= htmlspecialchars($user['email'] ?? '') ?>">
            <div class="invalid-feedback">Email là bắt buộc</div>
          </div>

          <div class="mb-3 d-flex">
            <div class="form-check me-4">
              <input class="form-check-input" type="radio" name="address_option" id="addr_saved" value="saved" checked>
              <label class="form-check-label" for="addr_saved">Chọn địa chỉ đã lưu</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="address_option" id="addr_new" value="new">
              <label class="form-check-label" for="addr_new">Nhập địa chỉ mới</label>
            </div>
          </div>

          <div id="saved-container" class="mb-3">
            <select id="saved-address" class="form-select">
              <option selected disabled>Chọn địa chỉ đã lưu</option>
              <?php foreach ($user_addresses as $addr): ?>
                <?php
                $full = $addr['address_detail'] . ', ' . $addr['ward'] . ', ' . $addr['district'] . ', ' . $addr['province'];
                ?>
                <option value="<?= $addr['address_id'] ?>" <?= $addr['is_default'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($full) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Chọn địa chỉ đã lưu</div>
          </div>

          <div id="new-container" class="mb-3" style="display:none;">
            <div class="row g-2 mb-2">
              <div class="col-md-4">
                <select id="province" class="form-select" required>
                  <option selected disabled>Tỉnh/TP</option>
                </select>
                <div class="invalid-feedback">Tỉnh/TP là bắt buộc</div>
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
            <div class="mb-2">
              <input type="text" class="form-control" id="specific-address" placeholder="Địa chỉ cụ thể (Tùy chọn)">
            </div>
          </div>
        </form>
      </div>

      <!-- Phương thức & Mua online -->
      <div class="col-lg-4 mb-4">
        <!-- <h5>Mua online</h5>
        <div class="border p-3 mb-3 rounded">
          <div class="form-check">
            <input class="form-check-input" type="radio" name="shipping" id="defaultShipping" checked>
            <label class="form-check-label" for="defaultShipping">Giao hàng tiêu chuẩn</label>
          </div>
        </div> -->

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
          <input type="text" class="form-control" placeholder="Nhập mã giảm giá" id="voucher-code">
          <button class="btn btn-dark" id="apply-voucher-btn">Áp dụng</button>
        </div>
      </div>

      <!-- Đơn hàng -->
      <div class="col-lg-4 order-summary">
        <h5>Đơn hàng</h5>
        <div id="order-items"></div>

        <ul class="list-group mt-3">
          <li class="list-group-item d-flex justify-content-between">
            <span>Tạm tính</span>
            <strong id="subtotal">0đ</strong>
          </li>
          <!-- <li class="list-group-item d-flex justify-content-between">
            <span>Phí vận chuyển</span>
            <span>0đ</span>
          </li> -->
          <li class="list-group-item d-flex justify-content-between">
            <span>Giảm giá</span>
            <span>0đ</span> <!-- payment.js lấy qua nth-child(3) -->
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <strong>Thành tiền</strong>
            <strong id="total">0đ</strong> <!-- payment.js sẽ gán vào đây -->
          </li>
        </ul>

        <!-- <span> ẩn dùng để JS cập nhật trước, rồi sẽ copy vào #total -->
        <span id="paid_price" style="display:none">0đ</span>

        <!-- Container để show QR code -->
        <div id="qr-section" class="mt-3"></div>

        <button id="btnPay" class="btn btn-dark w-100 mt-3" onclick="submitOrder()">
          Đặt hàng
        </button>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    window.savedAddresses = <?= json_encode($user_addresses, JSON_UNESCAPED_UNICODE) ?>;
    window.currentUser = <?= json_encode($user, JSON_UNESCAPED_UNICODE) ?>;
    window.bankAccount = <?= json_encode(
                            $db->selectOne("SELECT bank_code AS BANK_ID, account_number AS ACCOUNT_NO FROM bank_account WHERE is_active = 1 AND is_default = 1 LIMIT 1"),
                            JSON_UNESCAPED_UNICODE
                          ) ?>;

    // Define startPaymentProcess to bridge pay.js & payment.js
    window.startPaymentProcess = function(orderData) {
      if (orderData.payment_method === '1') {
        return; // ❌ Bỏ dòng alert ở đây, để payment.js lo
      }

      const MY_BANK = window.bankAccount || {
        BANK_ID: 'DEFAULTBANK',
        ACCOUNT_NO: '0000000000'
      };

      const raw = document.getElementById('paid_price').textContent || '';
      const amount = parseInt(raw.replace(/\D/g, ''), 10) || 0;
      const qrUrl = `https://img.vietqr.io/image/${MY_BANK.BANK_ID}-${MY_BANK.ACCOUNT_NO}-compact2.png?amount=${amount}&accountName=SAUKUTO`;

      document.getElementById('qr-section').innerHTML = `
    <div class="text-center">
      <p class="mb-2">Quét mã QR ngân hàng của bạn</p>
      <img src="${qrUrl}" width="150" alt="QR Code">
    </div>`;
    };
  </script>
  <script src="./assets/js/pay.js"></script>
  <script src="./assets/js/payment.js"></script>
</body>

</html>