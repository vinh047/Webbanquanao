// assets/js/payment.js
document.addEventListener('DOMContentLoaded', () => {
    // --- Bank QR Demo Integration ---
    const MY_BANK = { BANK_ID: 'Vietcombank', ACCOUNT_NO: '1025574990' };
    const btnPay = document.getElementById('btnPay');
    const paidPriceEl = document.getElementById('paid_price');
    const qrSection = document.getElementById('qr-section');

    // --- Cart & Summary (giữ nguyên) ---
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
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
          <p class="text-danger fw-bold mb-0">
            ${(item.price * item.quantity).toLocaleString()}đ
          </p>
        </div>`;
        orderItemsEl.appendChild(div);
    });
    document.getElementById('subtotal').textContent = subtotal.toLocaleString() + 'đ';
    document.querySelector('.list-group-item:nth-child(2) span:nth-child(2)')
        .textContent = window.shippingFee.toLocaleString() + 'đ';
    document.getElementById('paid_price').textContent =
        (subtotal + window.shippingFee - window.discount).toLocaleString() + 'đ';
    document.getElementById('total').textContent =
        document.getElementById('paid_price').textContent;

    // --- Voucher Logic (giữ nguyên) ---
    const voucherInput = document.querySelector('.input-group input');
    const applyVoucherBtn = document.querySelector('.input-group button');
    applyVoucherBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const code = voucherInput.value.trim().toUpperCase();
        if (code === 'HUYDEPTRAI') window.discount = subtotal * 0.1;
        else if (code === 'MINHHUY') window.discount = window.shippingFee;
        else { window.discount = 0; alert('Mã giảm giá không hợp lệ!'); }

        document.querySelector('.list-group-item:nth-child(3) span:nth-child(2)')
            .textContent = window.discount > 0
                ? '-' + window.discount.toLocaleString() + 'đ'
                : '0đ';

        const newTotal = subtotal + window.shippingFee - window.discount;
        document.getElementById('paid_price').textContent =
            newTotal.toLocaleString() + 'đ';
        document.getElementById('total').textContent =
            document.getElementById('paid_price').textContent;
    });

    // --- Chỉ một listener trên nút Đặt hàng ---
    if (btnPay && paidPriceEl && qrSection) {
        btnPay.addEventListener('click', (e) => {
            e.preventDefault();  // chặn submit mặc định nếu có form

            // Xóa QR cũ (nếu có)
            qrSection.innerHTML = '';

            const method = document.querySelector("input[name='payment_method']:checked").value;

            // 1) COD (giả sử id = 1) → thông báo thành công và dừng
            if (method === '1') {
                alert('Đặt hàng thành công');
                return;
            }

            // 2) Ngược lại: chuyển khoản → show QR + dòng hướng dẫn
            const raw = paidPriceEl.textContent || '';
            const amount = parseInt(raw.replace(/\D/g, ''), 10) || 0;
            const qrUrl = `https://img.vietqr.io/image/${MY_BANK.BANK_ID}-${MY_BANK.ACCOUNT_NO}-compact2.png?amount=${amount}&accountName=SAGKUTO`;

            qrSection.innerHTML = `
          <div class="text-center">
            <p class="mb-3 h5">Vui lòng quét mã QR thanh toán</p>
            <img src="${qrUrl}" style="width:300px;height:100%;" alt="QR Code">
          </div>`;
        });
    }
});
