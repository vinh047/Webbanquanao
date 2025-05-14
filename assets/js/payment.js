// assets/js/payment.js

/**
 * Thu thập dữ liệu đặt hàng trước khi gửi lên server.
 * Với địa chỉ mới, mình lấy text (tên) của option để lưu vào DB.
 */
function gatherOrderData(paymentMethodId) {
  const name  = document.getElementById('name')?.value.trim();
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
    address.ward     = wardSel.options[wardSel.selectedIndex]?.text || '';
    address.detail   = document.getElementById('specific-address')?.value.trim() || '';
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
    payment_method: paymentMethodId,
    discount: window.discount || 0,
    total_price: totalPrice
  };
}

document.addEventListener('DOMContentLoaded', () => {
  // Dữ liệu ngân hàng
  const MY_BANK = window.bankAccount || { BANK_ID: 'MBBank', ACCOUNT_NO: '0000000000' };

  // Reset alertShown
  localStorage.setItem('alertShown', 'false');

  // Các phần tử DOM
  const btnPay      = document.getElementById('btnPay');
  const paidPriceEl = document.getElementById('paid_price');
  const qrSection   = document.getElementById('qr-section');
  const orderItemsEl= document.getElementById('order-items');
  const subtotalEl  = document.getElementById('subtotal');
  const totalEl     = document.getElementById('total');
  const voucherInput= document.querySelector('.input-group input');
  const voucherBtn  = document.querySelector('.input-group button');

  // Lấy cart và tính subtotal
  const cart    = JSON.parse(sessionStorage.getItem('selectedCartItems')) || [];
  let subtotal  = 0;
  window.discount = 0;

  orderItemsEl.innerHTML = '';
  cart.forEach(item => {
    subtotal += item.price * item.quantity;
    const div = document.createElement('div');
    div.className = 'd-flex border-bottom py-2 align-items-center';
    const imgSrc = item.image?.includes('/') 
      ? item.image 
      : `/assets/img/sanpham/${item.image || 'sp1.jpg'}`;
    div.innerHTML = `
      <img src="${imgSrc}" class="me-2 rounded" style="width:60px;height:60px;object-fit:cover;">
      <div class="flex-grow-1">
        <p class="mb-0 fw-bold">${item.name}</p>
        <small>${item.color || 'Màu'} - ${item.size || 'Size'}</small>
        <div><small><strong>Số lượng: ${item.quantity}</strong></small></div>
        <p class="text-danger fw-bold mb-0">${(item.price * item.quantity).toLocaleString()}đ</p>
      </div>`;
    orderItemsEl.appendChild(div);
  });

  // Hiển thị subtotal và total lúc đầu
  subtotalEl.textContent = subtotal.toLocaleString() + 'đ';
  paidPriceEl.textContent = (subtotal - window.discount).toLocaleString() + 'đ';
  totalEl.textContent     = paidPriceEl.textContent;

  // Áp dụng voucher
  voucherBtn.addEventListener('click', e => {
    e.preventDefault();
    const code = voucherInput.value.trim().toUpperCase();
    if (code === 'HUYDEPTRAI')      window.discount = subtotal * 0.1;
    else if (code === 'MINHHUY')    window.discount = 0;
    else { window.discount = 0; alert('Mã giảm giá không hợp lệ!'); }

    // Cập nhật discount và total
    document.querySelector('.list-group-item:nth-child(3) span:nth-child(2)').textContent =
      window.discount > 0 
        ? '-' + window.discount.toLocaleString() + 'đ' 
        : '0đ';

    const newTotal = (subtotal - window.discount).toLocaleString() + 'đ';
    paidPriceEl.textContent = newTotal;
    totalEl.textContent     = newTotal;
  });

  // Xử lý nút Đặt hàng
  if (btnPay && paidPriceEl && qrSection) {
    btnPay.addEventListener('click', async e => {
      e.preventDefault();
      qrSection.innerHTML = '';

      const method = document.querySelector("input[name='payment_method']:checked").value;
      const orderData = gatherOrderData(method);

      // COD
      if (method === '1') {
        try {
          const res    = await fetch('./ajax/save_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(orderData)
          });
          if (!res.ok) throw new Error(await res.text());
          const result = await res.json();

          if (result.success) {
            // Cập nhật lại cart localStorage/sessionStorage
            const fullCart  = JSON.parse(localStorage.getItem('cart')) || [];
            const selected  = cart;
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
      const raw    = paidPriceEl.textContent || '';
      const amount = parseInt(raw.replace(/\D/g, ''), 10) || 0;
      const qrUrl  = `https://img.vietqr.io/image/${MY_BANK.BANK_ID}-${MY_BANK.ACCOUNT_NO}-compact2.png?amount=${amount}&accountName=SAGKUTO`;

      qrSection.innerHTML = `
        <div class="text-center">
          <p class="mb-3 h5">Vui lòng quét mã QR thanh toán</p>
          <img src="${qrUrl}" style="width:300px;height:100%;" alt="QR Code">
        </div>`;

      setTimeout(() => {
        checkInterval = setInterval(() => checkPaid(amount), 1000);
      }, 30000);
    });
  }
});

// Kiểm tra thanh toán QR
let is_success  = false;
let is_checking = false;
let checkInterval;
async function checkPaid(price) {
  if (is_success || is_checking) return;
  is_checking = true;
  try {
    const response = await fetch("https://script.google.com/macros/s/YOUR_SCRIPT_ID/exec");
    const data     = await response.json();
    const lastPaid = data.data[data.data.length - 1];
    const lastPrice= lastPaid["Giá trị"];

    if (lastPrice >= price) {
      if (!localStorage.getItem('alertShown')) {
        alert("Đã thanh toán thành công");
        localStorage.setItem('alertShown', 'true');
        window.location.href = 'index.php';
      }
      is_success = true;
      clearInterval(checkInterval);
    }
  } catch (err) {
    console.error("Lỗi kiểm tra thanh toán:", err);
  } finally {
    is_checking = false;
  }
}
