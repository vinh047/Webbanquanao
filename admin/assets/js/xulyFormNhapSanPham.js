document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById("formNhapSP");
    const formSua = document.getElementById("formSua");
    const tbLoai = document.querySelector(".thongbaoLoi");
    const loi = tbLoai.querySelector("p");
    const tbLoaiThanhCong = document.querySelector(".thongbaoThanhCong");
    const tc = tbLoaiThanhCong.querySelector("p");

    function fetchSanPham(page = 1) {
        fetch(`../ajax/quanlySanPham_ajax.php?pageproduct=${page}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById("product-list").innerHTML = data.products;
                document.getElementById("pagination").innerHTML = data.pagination;

                // Phân trang
                document.querySelectorAll(".page-link-custom").forEach(btn => {
                    btn.addEventListener("click", function (e) {
                        e.preventDefault();
                        fetchSanPham(this.dataset.page);
                    });
                });

                // Xử lý nút Sửa
                document.querySelectorAll(".btn-sua").forEach(btn => {
                    btn.addEventListener("click", function () {
                        const id = this.dataset.id;
                        const ten = this.dataset.ten;
                        const mota = this.dataset.mota;
                        const gia = this.dataset.gia;
                        const loai = this.dataset.loaiid;

                        document.querySelector(".formSua").style.display = "block";
                        document.querySelector(".overlay").style.display = "block";

                        formSua.querySelector("input[name='id']").value = id;
                        formSua.querySelector("input[name='ten']").value = ten;
                        formSua.querySelector("textarea[name='mota']").value = mota;
                        formSua.querySelector("select[name='loai']").value = loai;
                        formSua.querySelector("input[name='gia']").value = parseFloat(gia).toLocaleString('vi-VN');
                    });
                });

                // Xử lý nút Xóa
                document.querySelectorAll(".btn-xoa").forEach(btn => {
                    btn.addEventListener("click", function () {
                        const id = this.dataset.id;
                        const popup = document.querySelector(".thongBaoXoa");
                        popup.style.display = "block";
                        document.querySelector(".overlay").style.display = "block";

                        const btnCo = popup.querySelector(".btn-danger");
                        const btnKhong = popup.querySelector(".btn-primary");

                        btnKhong.onclick = () => {
                            popup.style.display = "none";
                            document.querySelector(".overlay").style.display = "none";
                        };

                        btnCo.onclick = () => {
                            fetch("../ajax/deleteSanPham.php", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded"
                                },
                                body: `id=${id}`
                            })
                                .then(res => res.json())
                                .then(data => {
                                    popup.style.display = "none";
                                    document.querySelector(".overlay").style.display = "none";
                                    if (data.success) {
                                        const tbXoa = document.querySelector(".thongbaoXoaThanhCong");
                                        tbXoa.style.display = "block";
                                        tbXoa.classList.add("show");
                                    
                                        setTimeout(() => tbXoa.classList.remove('show'), 2000);

                                    
                                        fetchSanPham();
                                    }
                                     else {
                                        const tbXoaTB = document.querySelector(".thongbaoXoaKhongThanhCong");
                                        tbXoaTB.style.display = "block";
                                        tbXoaTB.classList.add("show");      
                                        setTimeout(() => tbXoaTB.classList.remove('show'), 2000);
                              
                                    }
                                });
                        };
                    });
                });
            });
    }

    // Lấy dữ liệu lúc đầu
    fetchSanPham();

    // Thêm sản phẩm
    form.addEventListener("submit", function (event) {
        event.preventDefault();

        const ten = document.getElementById("txtTen").value.trim();
        const mota = document.getElementById("txtMota").value.trim();
        const gia = document.getElementById("txtGia").value.trim().replace(/\./g, '').replace(',', '.');
        const loai = document.getElementById("cbLoai").value.trim();

        tbLoai.classList.remove('show');
        tbLoai.style.display = 'none';

        if (!ten || !mota || !loai || !gia || isNaN(gia)) {
            let message = !ten ? "Tên không được để trống!" :
                !mota ? "Mô tả không được để trống!" :
                !loai ? "Loại sản phẩm không được để trống!" :
                !gia ? "Giá không được để trống!" :
                "Giá phải ở dạng số!";
            loi.textContent = message;
            tbLoai.style.display = 'block';
            tbLoai.classList.add('show');
            setTimeout(() => tbLoai.classList.remove('show'), 2000);
            return;
        }

        const formData = new FormData(this);
        formData.set("txtGia", gia);

        fetch('../ajax/insertSanPham.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchSanPham();
                    form.reset();
                    tc.textContent = "Sản phẩm đã được thêm thành công!";
                    tbLoaiThanhCong.style.display = 'block';
                    tbLoaiThanhCong.classList.add('show');
                    setTimeout(() => tbLoaiThanhCong.classList.remove('show'), 2000);
                } else {
                    alert('Thêm sản phẩm không thành công');
                }
            })
            .catch(error => {
                console.error('Có lỗi xảy ra:', error);
            });
    });

    // Cập nhật sản phẩm
    formSua.addEventListener("submit", function (e) {
        e.preventDefault();

        let giaFormatted = formSua.querySelector("input[name='gia']").value;
        let gia = giaFormatted.replace(/\./g, "").replace(",", ".");

        if (isNaN(gia) || gia <= 0) {
            if (isNaN(gia) || gia <= 0) {
                const tbGia = document.querySelector(".thongBaoLoiGia");
                tbGia.classList.add("show");
                tbGia.style.display = "block";
                
                setTimeout(() => tbGia.classList.remove('show'), 2000);

                return;
            }
            
            return;
        }

        const formData = new FormData(this);
        formData.set("gia", gia);

        fetch("../ajax/updateSanPham.php", {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.querySelector(".formSua").style.display = "none";
                    document.querySelector(".overlay").style.display = "none";
                
                    const tbUpdate = document.querySelector(".thongbaoUpdateThanhCong");
                    tbUpdate.style.display = "block";
                    tbUpdate.classList.add("show");
                
                    setTimeout(() => tbUpdate.classList.remove('show'), 2000);

                
                    fetchSanPham();
                }
                 else {
                    alert(data.message || "Lỗi cập nhật");
                }
            });
    });

    // Hủy form sửa
    document.querySelector(".formSua .btn-danger").addEventListener("click", function (e) {
        e.preventDefault();
        document.querySelector(".formSua").style.display = "none";
        document.querySelector(".overlay").style.display = "none";
    });
});
