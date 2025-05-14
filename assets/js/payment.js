function gatherOrderData(paymentMethodId) {
  const name = document.getElementById('name')?.value.trim();
  const phone = document.getElementById('sdt')?.value.trim();
  const email = document.getElementById('email')?.value.trim();

  const addressType = document.querySelector('input[name="address_option"]:checked')?.value;
  let address = {};

  if (addressType === 'saved') {
    address.saved_id = document.getElementById('saved-address')?.value;
  } else {
    address.province = document.getElementById('province')?.value;
    address.district = document.getElementById('district')?.value;
    address.ward = document.getElementById('ward')?.value;
    address.detail = document.getElementById('specific-address')?.value.trim();
  }

  const cartItems = JSON.parse(sessionStorage.getItem('selectedCartItems') || '[]');

  return {
    name,
    phone,
    email,
    address,
    cart: cartItems,
    payment_method: paymentMethodId,
    discount: window.discount || 0,
    shipping_fee: window.shippingFee || 0,
    total_price: parseFloat(document.getElementById('paid_price')?.textContent?.replace(/\D/g, '') || 0)
  };
}

document.addEventListener('DOMContentLoaded', () => {
  localStorage.setItem('alertShown', 'false'); 
  const btnPay = document.getElementById('btnPay');
  const paidPriceEl = document.getElementById('paid_price');
  const qrSection = document.getElementById('qr-section');

  const MY_BANK = window.bankAccount || { BANK_ID: 'MBBank', ACCOUNT_NO: '0000000000' };

  const cart = JSON.parse(sessionStorage.getItem('selectedCartItems')) || [];
  let subtotal = 0;
  window.discount = 0;
  window.shippingFee = 30000;

  const orderItemsEl = document.getElementById('order-items');
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

  document.getElementById('subtotal').textContent = subtotal.toLocaleString() + 'đ';
  document.querySelector('.list-group-item:nth-child(2) span:nth-child(2)').textContent = window.shippingFee.toLocaleString() + 'đ';
  document.getElementById('paid_price').textContent = (subtotal + window.shippingFee - window.discount).toLocaleString() + 'đ';
  document.getElementById('total').textContent = document.getElementById('paid_price').textContent;

  // Voucher Logic
  const voucherInput = document.querySelector('.input-group input');
  const applyVoucherBtn = document.querySelector('.input-group button');
  applyVoucherBtn.addEventListener('click', (e) => {
    e.preventDefault();
    const code = voucherInput.value.trim().toUpperCase();
    if (code === 'HUYDEPTRAI') window.discount = subtotal * 0.1;
    else if (code === 'MINHHUY') window.discount = window.shippingFee;
    else { window.discount = 0; alert('Mã giảm giá không hợp lệ!'); }

    document.querySelector('.list-group-item:nth-child(3) span:nth-child(2)').textContent =
      window.discount > 0 ? '-' + window.discount.toLocaleString() + 'đ' : '0đ';

    const newTotal = subtotal + window.shippingFee - window.discount;
    document.getElementById('paid_price').textContent = newTotal.toLocaleString() + 'đ';
    document.getElementById('total').textContent = document.getElementById('paid_price').textContent;
  });

  if (btnPay && paidPriceEl && qrSection) {
    btnPay.addEventListener('click', async (e) => {
      e.preventDefault();
      qrSection.innerHTML = '';

const method = document.querySelector("input[name='payment_method']:checked").value;

if (method === '1') {
  const orderData = gatherOrderData(method);

  try {
    const res = await fetch('./ajax/save_order.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(orderData)
    });

    if (!res.ok) {
      const text = await res.text(); // Bắt lỗi HTML hoặc phản hồi sai JSON
      console.error("❌ Server response is not OK:", text);
      alert("Máy chủ trả về lỗi không hợp lệ. Xem console để biết thêm chi tiết.");
      return;
    }

    const result = await res.json();

    if (result.success) {
      const isQR = method === '2'; // ⚠️ sửa: '2' là chuyển khoản QR
    
      // ✅ Cập nhật giỏ hàng cho cả QR và COD
      const fullCart = JSON.parse(localStorage.getItem('cart')) || [];
      const selected = JSON.parse(sessionStorage.getItem('selectedCartItems')) || [];
      const remainingCart = fullCart.filter(item =>
        !selected.some(sel =>
          sel.product_id === item.product_id &&
          sel.variant_id === item.variant_id
        )
      );
      localStorage.setItem('cart', JSON.stringify(remainingCart));
      sessionStorage.removeItem('selectedCartItems');
    
      if (!isQR && localStorage.getItem('alertShown') !== 'true') {
        alert('Đặt hàng thành công');
        localStorage.setItem('alertShown', 'true');
        window.location.href = 'index.php'; // ✅ chuyển trang cho COD
      }
    }
    
     else {
      alert('Lỗi đặt hàng: ' + (result.message || 'Không rõ nguyên nhân'));
    }

  } catch (err) {
    console.error('❌ Lỗi gửi đơn hàng:', err);
    alert('Không thể gửi đơn hàng. Vui lòng thử lại sau.');
  } 
  return;
}

      

      const raw = paidPriceEl.textContent || '';
      const amount = parseInt(raw.replace(/\D/g, ''), 10) || 0;

      const qrUrl = `https://img.vietqr.io/image/${MY_BANK.BANK_ID}-${MY_BANK.ACCOUNT_NO}-compact2.png?amount=${amount}&accountName=SAGKUTO`;

      qrSection.innerHTML = `
              <div class="text-center">
                  <p class="mb-3 h5">Vui lòng quét mã QR thanh toán</p>
                  <img src="${qrUrl}" style="width:300px;height:100%;" alt="QR Code">
              </div>`;

      setTimeout(() => {
        checkInterval = setInterval(() => {
          checkPaid(amount);
        }, 1000);
      }, 30000);
    });
  }
});

let is_success = false;
let is_checking = false;
let checkInterval;
async function checkPaid(price) {
  if (is_success || is_checking) return;

  is_checking = true;
  try {
    const response = await fetch("https://script.google.com/macros/s/YOUR_SCRIPT_ID/exec");
    const data = await response.json();
    const lastPaid = data.data[data.data.length - 1];
    const lastPrice = lastPaid["Giá trị"];

    if (lastPrice >= price) {
      const alreadyShown = localStorage.getItem('alertShown') === 'true';
      if (!alreadyShown) {
        alert("Đã thanh toán thành công");
        localStorage.setItem('alertShown', 'true');
    
        // ✅ Chỉ chuyển trang tại đây
        window.location.href = 'index.php';
      }
    
      is_success = true;
      clearInterval(checkInterval);
    }
      
     else {
      console.log("Chưa thanh toán đủ");
    }
  } catch (err) {
    console.error("Lỗi kiểm tra thanh toán:", err);
  } finally {
    is_checking = false;
  }
}
