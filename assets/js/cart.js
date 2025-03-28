document.addEventListener("DOMContentLoaded", function () {
    const cartItemsContainer = document.getElementById("cart-items");
    const cart = JSON.parse(localStorage.getItem("cart")) || [];

    if (cart.length === 0) {
        return; // Giữ nguyên nội dung "giỏ hàng đang trống"
    }

    let html = `
        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Tên sản phẩm</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th>Xóa</th>
                </tr>
            </thead>
            <tbody>
    `;

    let tongTien = 0;

    cart.forEach((item, index) => {
        const thanhTien = item.price * item.quantity;
        tongTien += thanhTien;

        html += `
            <tr>
                <td>${item.name}</td>
                <td>${item.price.toLocaleString('vi-VN')} VND</td>
                <td>${item.quantity}</td>
                <td>${thanhTien.toLocaleString('vi-VN')} VND</td>
                <td>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeItem(${index})">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    html += `
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                    <td colspan="2"><strong>${tongTien.toLocaleString('vi-VN')} VND</strong></td>
                </tr>
            </tfoot>
        </table>
        <button class="btn btn-danger mt-2" onclick="clearCart()">Xóa toàn bộ giỏ hàng</button>
        <a href="../index.php" class="btn btn-secondary mt-2 ms-2">Tiếp tục mua hàng</a>
    `;

    cartItemsContainer.innerHTML = html;
});

function clearCart() {
    if (confirm("Bạn có chắc muốn xóa toàn bộ giỏ hàng không?")) {
        localStorage.removeItem("cart");
        location.reload();
    }
}

function removeItem(index) {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    cart.splice(index, 1);
    localStorage.setItem("cart", JSON.stringify(cart));
    location.reload();
}
