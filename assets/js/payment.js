// assets/js/payment.js

/**
 * Thu thập dữ liệu đặt hàng trước khi gửi lên server.
 * Với địa chỉ mới, mình lấy text (tên) của option để lưu vào DB.
 */
function generateAddInfo(length = 8) {
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  let result = 'SAG'; // prefix dễ nhớ
  for (let i = 0; i < length; i++) {
    result += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return result;
}

function gatherOrderData(paymentMethodId) {
  const name = document.getElementById('name')?.value.trim();
  const phone = document.getElementById('sdt')?.value.trim();
  const email = document.getElementById('email')?.value.trim();

  const addressType = document.querySelector('input[name="address_option"]:checked')?.value;
  let address = {};

  if (addressType === 'saved') {
    // Chọn địa chỉ đã lưu: chỉ cần ID
    address.saved_id = document.getElementById('saved-address')?.value;
  } else {
    // Địa chỉ mới: lấy text của option, không lấy mã
    const provSel = document.getElementById('province');
    const distSel = document.getElementById('district');
    const wardSel = document.getElementById('ward');

    address.province = provSel.options[provSel.selectedIndex]?.text || '';
    address.district = distSel.options[distSel.selectedIndex]?.text || '';
    address.ward = wardSel.options[wardSel.selectedIndex]?.text || '';
    address.detail = document.getElementById('specific-address')?.value.trim() || '';
  }

  const cartItems = JSON.parse(sessionStorage.getItem('selectedCartItems') || '[]');
  const totalPrice = parseFloat(
    document.getElementById('paid_price')?.textContent.replace(/\D/g, '') || 0
  );

  return {
    name,
    phone,
    email,
    address,
    cart: cartItems,
    payment_method: parseInt(paymentMethodId, 10),
    discount: window.discount || 0,
    total_price: totalPrice
  };
}

document.addEventListener('DOMContentLoaded', () => {
  // Dữ liệu ngân hàng
  const MY_BANK = window.bankAccount || { BANK_ID: 'MBBank', ACCOUNT_NO: '0000000000' };

  // Reset alertShown
  localStorage.setItem('alertShown', 'false');

  // DOM elements
  const btnPay = document.getElementById('btnPay');
  const paidPriceEl = document.getElementById('paid_price');
  const qrSection = document.getElementById('qr-section');
  const orderItemsEl = document.getElementById('order-items');
  const subtotalEl = document.getElementById('subtotal');
  const totalEl = document.getElementById('total');
  const voucherInput = document.querySelector('.input-group input');
  const voucherBtn = document.querySelector('.input-group button');

  let subtotal = 0;
  window.discount = 0;

  // ✅ Lấy cart từ DB
  async function loadCartFromDB() {
    try {
      const res = await fetch('./ajax/get_cart.php');
      const result = await res.json();

      if (!result.success || !Array.isArray(result.data)) {
        orderItemsEl.innerHTML = '<p class="text-danger">Không thể tải giỏ hàng</p>';
        subtotalEl.textContent = '0đ';
        totalEl.textContent = '0đ';
        paidPriceEl.textContent = '0đ';
        return;
      }

      const cart = result.data;
      subtotal = 0;
      orderItemsEl.innerHTML = '';

      cart.forEach(item => {
        const imgSrc = item.image?.includes('/')
          ? item.image
          : `/assets/img/sanpham/${item.image || 'sp1.jpg'}`;
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;

        const div = document.createElement('div');
        div.className = 'd-flex border-bottom py-2 align-items-center';
        div.innerHTML = `
        <img src="${imgSrc}" class="me-2 rounded" style="width:60px;height:60px;object-fit:cover;">
        <div class="flex-grow-1">
          <p class="mb-0 fw-bold">${item.name}</p>
          <small>${item.color || 'Màu'} - ${item.size || 'Size'}</small>
          <div><small><strong>Số lượng: ${item.quantity}</strong></small></div>
          <p class="text-danger fw-bold mb-0">${itemTotal.toLocaleString()}đ</p>
        </div>`;
        orderItemsEl.appendChild(div);
      });

      // Cập nhật UI
      subtotalEl.textContent = subtotal.toLocaleString() + 'đ';
      const finalTotal = subtotal - window.discount;
      paidPriceEl.textContent = finalTotal.toLocaleString() + 'đ';
      totalEl.textContent = finalTotal.toLocaleString() + 'đ';

    } catch (err) {
      console.error('❌ Lỗi tải giỏ hàng:', err);
      orderItemsEl.innerHTML = '<p class="text-danger">Lỗi kết nối máy chủ</p>';
    }
  }

  // Gọi lúc đầu
  loadCartFromDB();

  // ✅ Áp dụng voucher
  voucherBtn.addEventListener('click', async e => {
    e.preventDefault();
    const code = voucherInput.value.trim().toUpperCase();
    if (!code) return;

    try {
      const res = await fetch('./ajax/check_voucher.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ code })
      });
      const data = await res.json();

      if (data.success) {
        window.discount = subtotal * (data.discount / 100);
        alert(`Áp dụng mã ${data.code}: Giảm ${data.discount}%`);
      } else {
        window.discount = 0;
        alert(data.message);
      }
    } catch (err) {
      console.error('Lỗi kiểm tra voucher:', err);
      alert('Không thể kiểm tra mã giảm giá');
      window.discount = 0;
    }

    // Cập nhật UI
    document.getElementById('discountAmount').textContent =
      window.discount > 0 ? '-' + window.discount.toLocaleString() + 'đ' : '0đ';

    const newTotal = (subtotal - window.discount).toLocaleString() + 'đ';
    paidPriceEl.textContent = newTotal;
    totalEl.textContent = newTotal;
  });


  // Xử lý nút Đặt hàng
  if (btnPay && paidPriceEl && qrSection) {
    btnPay.addEventListener('click', async e => {
      e.preventDefault();
      qrSection.innerHTML = '';

      const addressOption = document.querySelector('input[name="address_option"]:checked')?.value;

      if (addressOption === 'saved') {
        const savedAddress = document.getElementById('saved-address').value;
        if (!savedAddress) {
          alert('Vui lòng chọn địa chỉ đã lưu để giao hàng.');
          document.getElementById('saved-address').focus();
          return;
        }
      }

      if (addressOption === 'new') {
        const province = document.getElementById('province').value;
        const district = document.getElementById('district').value;
        const ward = document.getElementById('ward').value;
        const specific = document.getElementById('specific-address').value.trim();


        if (!province || !district || !ward) {
          alert('Vui lòng chọn đầy đủ Tỉnh/Thành, Quận/Huyện và Phường/Xã.');
          return;
        }
        if (!specific) {
          alert('Vui lòng nhập địa chỉ cụ thể.');
          document.getElementById('specific-address').focus();
          return;
        }
      }

      const method = document.querySelector("input[name='payment_method']:checked").value;
      const orderData = gatherOrderData(method);

      // COD
      if (method === '1') {
        try {
          const res = await fetch('./ajax/save_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(orderData)
          });
          if (!res.ok) throw new Error(await res.text());
          const result = await res.json();

          if (result.success) {
            // Cập nhật lại cart localStorage/sessionStorage
            const fullCart = JSON.parse(localStorage.getItem('cart')) || [];
            const selected = cart;
            const remaining = fullCart.filter(i =>
              !selected.some(s => s.product_id === i.product_id && s.variant_id === i.variant_id)
            );
            localStorage.setItem('cart', JSON.stringify(remaining));
            sessionStorage.removeItem('selectedCartItems');

            if (localStorage.getItem('alertShown') !== 'true') {
              alert('Đặt hàng thành công');
              localStorage.setItem('alertShown', 'true');
              window.location.href = 'index.php';
            }
          } else {
            alert('Lỗi đặt hàng: ' + (result.message || 'Không rõ nguyên nhân'));
          }
        } catch (err) {
          console.error(err);
          alert('Không thể gửi đơn hàng. Vui lòng thử lại sau.');
        }
        return;
      }

      // QR chuyển khoản
      const raw = paidPriceEl.textContent || '';
      const amount = parseInt(raw.replace(/\D/g, ''), 10) || 0;
      const addInfo = generateAddInfo();
      const qrUrl = `https://img.vietqr.io/image/${MY_BANK.BANK_ID}-${MY_BANK.ACCOUNT_NO}-compact2.png?amount=${amount}&accountName=SAGKUTO&addInfo=${addInfo}`;

      qrSection.innerHTML = `
        <div class="text-center">
          <p class="mb-3 h5">Vui lòng quét mã QR thanh toán</p>
          <img src="${qrUrl}" style="width:300px;height:100%;" alt="QR Code">
        </div>`;

      window.latestOrderData = orderData;
      setTimeout(() => {
        checkInterval = setInterval(() => checkPaid(amount, window.latestOrderData), 1000);
      }, 30000);


      const countdownEl = document.createElement('div');
      countdownEl.className = 'mt-2 text-center text-danger fw-bold';
      qrSection.appendChild(countdownEl);

      let countdown = 600; // 10 phút = 600 giây
      let countdownTimer = setInterval(() => {
        const minutes = Math.floor(countdown / 60).toString().padStart(2, '0');
        const seconds = (countdown % 60).toString().padStart(2, '0');
        countdownEl.textContent = `⏳ Thời gian còn lại: ${minutes}:${seconds}`;

        if (countdown <= 0) {
          clearInterval(countdownTimer);
          clearInterval(checkInterval); // ngừng kiểm tra thanh toán
          qrSection.innerHTML = `<p class="text-center text-danger fw-bold">❌ Mã QR đã hết hạn. Vui lòng đặt lại đơn hàng.</p>`;
          btnPay.style.display = 'block'; // Cho phép đặt lại
        }

        countdown--;
      }, 1000);

      // Ẩn nút đặt hàng khi QR hiện
      btnPay.style.display = 'none';

    });
  }
});

// Kiểm tra thanh toán QR
let is_success = false;
let is_checking = false;
let checkInterval;
async function checkPaid(price, orderData) {
  if (is_success || is_checking) return;
  is_checking = true;
  try {
    const response = await fetch("https://script.google.com/macros/s/AKfycbw9Zscnz7v0EJcqa_HPfVgfBF2koyHOUPQEyUB2MPa2tU9938bqFyb3aOI6ZS9x36De2A/exec");
    const data = await response.json();

    console.log("✅ Dữ liệu từ Google Sheets:", data);

    if (!data.data || data.data.length === 0) {
      console.warn("❌ Không có dữ liệu trong data.data");
      return;
    }

    const lastPaid = data.data[data.data.length - 1];
    console.log("👉 Dòng cuối:", lastPaid);
    const lastPrice = parseInt(lastPaid["Giá trị"].toString().replace(/\D/g, '')) || 0;
    console.log("💰 Giá trị cuối:", lastPrice, "| So sánh với:", price);

    if (lastPrice >= price && !is_success) {
      is_success = true;
      clearInterval(checkInterval);

      // Gửi đơn hàng (tương tự COD)
      try {
        const orderToSave = window.latestOrderData || {};
        const res = await fetch('./ajax/save_order.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(orderToSave)
        });
        const result = await res.json();

        if (result.success) {
          // Xử lý giỏ hàng
          const fullCart = JSON.parse(localStorage.getItem('cart')) || [];
          const selected = orderToSave.cart || [];
          const remaining = fullCart.filter(i =>
            !selected.some(s => s.product_id === i.product_id && s.variant_id === i.variant_id)
          );
          localStorage.setItem('cart', JSON.stringify(remaining));
          sessionStorage.removeItem('selectedCartItems');

          alert("✅ Đã thanh toán thành công");
          window.location.href = 'index.php';
        } else {
          alert("❌ Thanh toán xong nhưng không lưu được đơn hàng: " + result.message);
        }

      } catch (err) {
        console.error("❌ Lỗi gửi đơn sau thanh toán QR:", err);
        alert("Lỗi khi lưu đơn hàng sau khi thanh toán. Vui lòng liên hệ hỗ trợ.");
      }
    }


  } catch (err) {
    console.error("Lỗi kiểm tra thanh toán:", err);
  } finally {
    is_checking = false;
  }
}

