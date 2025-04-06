document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById("formNhapSP"); 
    const tbLoai = document.querySelector(".thongbaoLoi");
    const loi = tbLoai.querySelector("p");
    const tbLoaiThanhCong = document.querySelector(".thongbaoThanhCong");
    const tc = tbLoaiThanhCong.querySelector("p");
    form.addEventListener("submit", function(event) {
        event.preventDefault();

        const ten = document.getElementById("txtTen").value.trim();
        const mota = document.getElementById("txtMota").value.trim();
        const gia = document.getElementById("txtGia").value.trim();
        const loai = document.getElementById("cbLoai").value.trim();

        // Ẩn thông báo lỗi trước khi kiểm tra
        tbLoai.classList.remove('show');
        tbLoai.style.display = 'none';

        if (!ten) {
            loi.textContent = "Tên không được để trống!";
            tbLoai.style.display = 'block';
            tbLoai.classList.add('show');
            setTimeout(function() {
                tbLoai.classList.remove('show'); 
            }, 2000);
            document.getElementById("txtTen").focus();
            return;
        }

        if (!mota) {
            loi.textContent = "Mô tả không được để trống!";
            tbLoai.style.display = 'block';
            tbLoai.classList.add('show');
            setTimeout(function() {
                tbLoai.classList.remove('show'); 
            }, 2000);
            document.getElementById("txtMota").focus();
            return;
        }

        if (!loai) {
            loi.textContent = "Loại sản phẩm không được để trống!";
            tbLoai.style.display = 'block';
            tbLoai.classList.add('show');
            setTimeout(function() {
                tbLoai.classList.remove('show'); 
            }, 2000);
            document.getElementById("cbLoai").focus();
            return;
        }

        if (!gia) {
            loi.textContent = "Giá không được để trống!";
            tbLoai.style.display = 'block';
            tbLoai.classList.add('show');
            setTimeout(function() {
                tbLoai.classList.remove('show'); 
            }, 2000);
            document.getElementById("txtGia").focus();
            return;
        }

        if (isNaN(gia)) {
            loi.textContent = "Giá phải ở dạng số!";
            tbLoai.style.display = 'block';
            tbLoai.classList.add('show');
            setTimeout(function() {
                tbLoai.classList.remove('show'); 
            }, 2000);
            document.getElementById("txtGia").focus();
            return;
        }

        var formData = new FormData(this);

        // Gửi dữ liệu qua AJAX
        fetch('/webbanquanao/admin/ajax/insertSanPham.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())  // Giả sử server trả về JSON
        .then(data => {
            if (data.success) {
                // Nếu thêm thành công, cập nhật bảng mà không cần tải lại trang
                var newRow = `
                    <tr class="text-center">
                        <td>${data.product_id}</td>
                        <td>${data.name}</td>
                        <td>${data.category_name}</td>
                        <td>${data.description}</td>
                        <td>${data.price}</td>
                        <td>
                            <button class="btn btn-success">Sửa</button>
                            <button class="btn btn-danger">Xóa</button>
                        </td>
                    </tr>
                `;
                document.querySelector('table tbody').insertAdjacentHTML('beforeend', newRow);
                document.getElementById('formNhapSP').reset();  // Reset form
                                tc.textContent = "Sản phẩm đã được thêm thành công!";
                                tbLoaiThanhCong.style.display = 'block';
                                tbLoaiThanhCong.classList.add('show');
                setTimeout(function() {
                    tbLoaiThanhCong.classList.remove('show'); 
                }, 2000);
            } else {
                alert('Thêm sản phẩm không thành công');
            }
        })
        .catch(error => {
            console.error('Có lỗi xảy ra:', error);
        });
        
    });

    // Hàm loadSanPham để tải lại danh sách sản phẩm
});
