document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById("formNhapSP");
    const formSua = document.getElementById("formSua");
    const tbLoai = document.querySelector(".thongbaoLoi");
    const loi = tbLoai.querySelector("p");
    const tbLoaiThanhCong = document.querySelector(".thongbaoThanhCong");
    const tc = tbLoaiThanhCong.querySelector("p");
    let currentPage = 1;

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
                        currentPage = parseInt(this.dataset.page); // lưu lại trang hiện tại
                        fetchSanPham(this.dataset.page);
                    });
                });
                const input = document.getElementById("pageInput");
                if (input) {
                    input.addEventListener("keypress", function (e) {
                        if (e.key === "Enter") {
                            e.preventDefault();
                            let page = parseInt(this.value);
                            const max = parseInt(this.max);
                
                            if (page < 1) page = 1;
                            if (page > max) page = max;
                
                            if (page >= 1 && page <= max) {
                                fetchSanPham(page); // ✅ đúng
                            }
                        }
                    });
                }
                
                // Xử lý nút Sửa
                document.querySelectorAll(".btn-sua").forEach(btn => {
                    btn.addEventListener("click", function () {
                        const id = this.dataset.id;
                        const ten = this.dataset.ten;
                        const mota = this.dataset.mota;
                        const gia = this.dataset.gia;
                        const giaban = this.dataset.giaban;
                        const loai = this.dataset.loaiid;
                        const pttg = this.dataset.pttg;

                        document.querySelector(".formSua").style.display = "block";
                        document.querySelector(".overlay").style.display = "block";

                        formSua.querySelector("input[name='id']").value = id;
                        formSua.querySelector("input[name='ten']").value = ten;
                        formSua.querySelector("textarea[name='mota']").value = mota;
                        formSua.querySelector("select[name='loai']").value = loai;
                        formSua.querySelector("input[name='gia']").value = parseFloat(gia).toLocaleString('vi-VN');
                        formSua.querySelector("input[name='giaban']").value = parseFloat(giaban).toLocaleString('vi-VN');
                        formSua.querySelector("input[name='pttg']").value = parseFloat(pttg);
                        formSua.dataset.giaNhapCu = parseFloat(gia.replace(/\./g, "").replace(",", "."));
                        formSua.dataset.giaBanCu = parseFloat(giaban.replace(/\./g, "").replace(",", "."));

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

                                    
                                        if (document.querySelectorAll(".btn-sua").length === 1 && currentPage > 1) {
                                            currentPage -= 1; // nếu chỉ còn 1 sản phẩm → lùi trang
                                        }
                                        fetchSanPham(currentPage);                                    }
                                    else {
                                        const tbXoaTB = document.querySelector(".thongbaoXoaThatBai");
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
                    fetchSanPham(currentPage);
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

    function validatePrice(value, selector) {
        const cleaned = value.replace(/\./g, "").replace(",", ".");
        const number = parseFloat(cleaned);
        if (isNaN(number) || number <= 0) {
            const tbGia = document.querySelector(selector);
            tbGia.classList.add("show");
            tbGia.style.display = "block";
            setTimeout(() => tbGia.classList.remove('show'), 2000);
            return null;
        }
        return number;
    }
    
    
    formSua.addEventListener("submit", function (e) {
        e.preventDefault();
    
        const gia = validatePrice(formSua.querySelector("input[name='gia']").value, ".thongBaoLoiGia");
        const giaban = validatePrice(formSua.querySelector("input[name='giaban']").value, ".thongBaoLoiGia");
    
        if (gia === null || giaban === null) return;
    
        const giaNhap = parseFloat(gia);
        const giaBan = parseFloat(giaban);
    
        const giaBanCu = parseFloat(formSua.dataset.giaBanCu);
    
        // ✅ Nếu người dùng đã sửa giá bán & giá bán mới < giá nhập → cảnh báo
        if (giaBan !== giaBanCu && giaBan < giaNhap) {
            document.querySelector(".thongBaoGia").style.display = "block";
            document.querySelector(".overlay").style.display = "block";
    
            document.querySelector(".btn-xacnhan-gia").onclick = function () {
                document.querySelector(".thongBaoGia").style.display = "none";
                document.querySelector(".overlay").style.display = "none";
                sendCapNhatSanPham(giaNhap, giaBan);
            };
    
            document.querySelector(".btn-khong-gia").onclick = function () {
                document.querySelector(".thongBaoGia").style.display = "none";
            };
    
            return;
        }
    
        sendCapNhatSanPham(giaNhap, giaBan);
    });
    
    
    
    
    
    function sendCapNhatSanPham(gia, giaban) {
        const formData = new FormData(formSua);
        formData.set("gia", gia);
        formData.set("giaban", giaban);
    
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
    
                fetchSanPham(currentPage);
            } else {
                alert(data.message || "Lỗi cập nhật");
            }
        });
    }
    

    // Hủy form sửa
    document.querySelector(".formSua .btn-danger").addEventListener("click", function (e) {
        e.preventDefault();
        document.querySelector(".formSua").style.display = "none";
        document.querySelector(".overlay").style.display = "none";
    });
});