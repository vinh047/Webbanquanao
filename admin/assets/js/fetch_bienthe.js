document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formNhapSPbienThe");
    const thongbao = document.querySelector(".thongbaoLoi");
    const loi = thongbao.querySelector("p");
    const tbThanhCong = document.querySelector(".thongbaoThanhCong");
    const tc = tbThanhCong.querySelector("p");
    const formSua = document.getElementById("formSuaSPbienThe");

    function fetchBienThe(page = 1) {
        fetch(`../ajax/quanlyBienThe_ajax.php?pageproduct=${page}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById("product-list").innerHTML = data.products;
                document.getElementById("pagination").innerHTML = data.pagination;

                document.querySelectorAll(".page-link-custom").forEach(btn => {
                    btn.addEventListener("click", function (e) {
                        e.preventDefault();
                        fetchBienThe(this.dataset.page);
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
                                fetchBienThe(page); // ✅ đúng
                            }
                        }
                    });
                }
                document.querySelectorAll(".btn-sua").forEach(btn => {
                    btn.addEventListener("click", function (e) {
                        const idvr = this.dataset.idvr;
                        const idsp = this.dataset.idsp;
                        const anh = this.dataset.anh;
                        const size = this.dataset.size;
                        const soluong = this.dataset.soluong;
                        const mau = this.dataset.mau;

                        document.querySelector(".formSua").style.display = "block";
                        document.querySelector(".overlay").style.display = "block";

                        // Truyền dữ liệu vào form
                        formSua.querySelector("input[name='txtMaBt']").value = idvr;
                        document.getElementById("txtMaSua").value = idsp;
                        document.getElementById("fileAnhSua").value = ""; // không thể gán đường dẫn file trực tiếp
                        document.getElementById("cbSizeSua").value = size;
                        document.getElementById("txtSlSua").value = soluong;
                        document.getElementById("cbMauSua").value = mau;
                        document.getElementById("tenFileAnhSua").textContent = anh;

                        // Gán ảnh hiển thị
                        const imgPreview = formSua.querySelector("#hienthianhSua img");
                        imgPreview.src = "../../assets/img/sanpham/" + anh;
                        imgPreview.style.display = "block";
                        
                    });
                });


                    document.querySelectorAll(".btn-xoa").forEach(btn => {
                    btn.addEventListener("click", function () {
                        const id = this.dataset.id; // Lấy ID của sản phẩm
                        const popup = document.querySelector(".thongBaoXoa"); // Popup xóa
                        const overlay = document.querySelector(".overlay"); // Overlay đen mờ

                        // Hiển thị popup và overlay
                        popup.style.display = "block";
                        overlay.style.display = "block";

                        // Xử lý khi nhấn nút "Có"
                        popup.querySelector(".btn-danger").onclick = function () {
                            // Gửi yêu cầu xóa sản phẩm qua AJAX
                            fetch("../ajax/deleteBienThe.php", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded"
                                },
                                body: `id=${id}`
                            })
                            .then(res => res.json())
                            .then(data => {
                                console.log("Xoá biến thể trả về:", data); // ✅ debug
                                if (data.success) {
                                    // Hiển thị thông báo xóa thành công
                                    const tbXoa = document.querySelector(".thongbaoXoaThanhCong");
                                    tbXoa.style.display = "block";
                                    tbXoa.classList.add("show");

                                    setTimeout(() => tbXoa.classList.remove('show'), 2000);


                                    // Tải lại danh sách sản phẩm sau khi xóa
                                    fetchBienThe();
                                } else {
                                    const tbXoaTB = document.querySelector(".thongbaoXoaThatBai");
                                    tbXoaTB.style.display = "block";
                                    tbXoaTB.classList.add("show");      
                                    setTimeout(() => tbXoaTB.classList.remove('show'), 2000);
                                }

                                // Ẩn popup và overlay sau khi xử lý xong
                                popup.style.display = "none";
                                overlay.style.display = "none";
                            });
                        };

                        // Xử lý khi nhấn nút "Không"
                        popup.querySelector(".btn-primary").onclick = function () {
                            // Ẩn popup và overlay khi không xóa
                            popup.style.display = "none";
                            overlay.style.display = "none";
                        };
                    });
                });
            })


            .catch(err => console.error("Lỗi khi fetch biến thể:", err));

    }

    fetchBienThe(); // load ban đầu

    form.addEventListener("submit", function (e) {

        const idsp = document.getElementById("txtMa").value.trim();
        const img = document.getElementById("fileAnh").value;
        const size = document.getElementById("cbSize").value.trim();
        const mau = document.getElementById("cbMau").value;
        const sl = document.getElementById("txtSl").value.trim();
        e.preventDefault();

    
        if (!idsp) {
            loi.textContent = "Không được để trống ID sản phẩm";
            thongbao.style.display = 'block';
            thongbao.classList.add('show');
            setTimeout(() => thongbao.classList.remove('show'), 2000);
            document.getElementById("txtMa").focus();
            return;
        }
    
        if(isNaN(idsp))
        {
            loi.textContent = "ID sản phẩm phải ở dạng số";
            thongbao.style.display = 'block';
            thongbao.classList.add('show');
            setTimeout(() => thongbao.classList.remove('show'), 2000);
            document.getElementById("txtMa").focus();
            return;  
        }
    
        if(idsp < 0)
        {
            loi.textContent = "ID sản phẩm phải lớn hơn 0";
            thongbao.style.display = 'block';
            thongbao.classList.add('show');
            setTimeout(() => thongbao.classList.remove('show'), 2000);
            document.getElementById("txtMa").focus();
            return;  
        }
    
        if(!img)
        {
            loi.textContent = "Không được để trống hỉnh ảnh";
            thongbao.style.display = 'block';
            thongbao.classList.add('show');
            setTimeout(() => thongbao.classList.remove('show'), 2000);
            document.getElementById("fileAnh").focus();
            return;
        }
    
        const file = document.getElementById("fileAnh").files[0]; 
        const validImageTypes = ['image/jpeg', 'image/png', 'image/gif']; 
        if (file && !validImageTypes.includes(file.type)) {
            loi.textContent = "Tệp được chọn không phải là ảnh (chỉ chấp nhận .jpg, .png, .gif)";
            thongbao.style.display = 'block';
            thongbao.classList.add('show');
            setTimeout(() => thongbao.classList.remove('show'), 2000);
            document.getElementById("fileAnh").focus();
            return;
        }
    
        if(!size)
        {
            loi.textContent = "Không được để trống size";
            thongbao.style.display = 'block';
            thongbao.classList.add('show');
            setTimeout(() => thongbao.classList.remove('show'), 2000);
            document.getElementById("cbSize").focus();
            return;
        }
    
        if(!sl){
            loi.textContent = "Không được để trống số lượng";
            thongbao.style.display = 'block';
            thongbao.classList.add('show');
            setTimeout(() => thongbao.classList.remove('show'), 2000);
            document.getElementById("txtSl").focus();
            return; 
        }
    
        if(isNaN(sl))
        {
            loi.textContent = "Số lượng phải ở dạng số";
            thongbao.style.display = 'block';
            thongbao.classList.add('show');
            setTimeout(() => thongbao.classList.remove('show'), 2000);
            document.getElementById("txtSl").focus();
            return;  
        }
    
        if(sl < 0)
        {
            loi.textContent = "Số lượng phải lớn hơn 0";
            thongbao.style.display = 'block';
            thongbao.classList.add('show');
            setTimeout(() => thongbao.classList.remove('show'), 2000);
            document.getElementById("txtSl").focus();
            return;  
        }
    
        if(!mau)
        {
            loi.textContent = "Không được để trống màu";
            thongbao.style.display = 'block';
            thongbao.classList.add('show');
            setTimeout(() => thongbao.classList.remove('show'), 2000);
            document.getElementById("cbMau").focus();
            return;  
        }


        fetch(`../ajax/checkID.php?product_id=${idsp}`)
            .then(res => res.json())
            .then(data => {
                if (!data.exists) {
                    loi.textContent = "Mã sản phẩm không tồn tại!";
                    return showError();
                }

                // ✅ Nếu hợp lệ và tồn tại, tiếp tục thêm
                const formData = new FormData(form);
                fetch('../ajax/insertBienThe.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchBienThe();
                        form.reset();
                        document.querySelector("#hienthianh img").style.display = "none";
                        thongbao.classList.remove('show');
                        thongbao.style.display = 'none';
                        tc.textContent = data.message;
                        tbThanhCong.style.display = 'block';
                        tbThanhCong.classList.add('show');
                        setTimeout(() => tbThanhCong.classList.remove('show'), 2000);

                    } else {
                        loi.textContent = data.message || "Thêm sản phẩm không thành công!";
                        showError();
                    }
                })
                .catch(err => {
                    console.error("Lỗi:", err);
                });
            });

        function showError() {
            thongbao.style.display = 'block';
            thongbao.classList.add('show');
            setTimeout(() => thongbao.classList.remove('show'), 2000);
        }
    });

    formSua.addEventListener("submit", function (e) {
        e.preventDefault();
        const idsp = document.getElementById("txtMaSua").value.trim();
        const img = document.getElementById("fileAnhSua").value;
        const size = document.getElementById("cbSizeSua").value.trim();
        const mau = document.getElementById("cbMauSua").value;
        const sl = document.getElementById("txtSlSua").value.trim();
        e.preventDefault();

    
        if (!idsp) {
            loi.textContent = "Không được để trống ID sản phẩm";
            thongbao.style.display = 'block';
            thongbao.classList.add('show');
            setTimeout(() => thongbao.classList.remove('show'), 2000);
            document.getElementById("txtMaSua").focus();
            return;
        }
    
        if(isNaN(idsp))
        {
            loi.textContent = "ID sản phẩm phải ở dạng số";
            thongbao.style.display = 'block';
            thongbao.classList.add('show');
            setTimeout(() => thongbao.classList.remove('show'), 2000);
            document.getElementById("txtMaSua").focus();
            return;  
        }
    
        if(idsp < 0)
        {
            loi.textContent = "ID sản phẩm phải lớn hơn 0";
            thongbao.style.display = 'block';
            thongbao.classList.add('show');
            setTimeout(() => thongbao.classList.remove('show'), 2000);
            document.getElementById("txtMaSua").focus();
            return;  
        }
    
        const file = document.getElementById("fileAnh").files[0]; 
        const validImageTypes = ['image/jpeg', 'image/png', 'image/gif']; 
        if (file && !validImageTypes.includes(file.type)) {
            loi.textContent = "Tệp được chọn không phải là ảnh (chỉ chấp nhận .jpg, .png, .gif)";
            thongbao.style.display = 'block';
            thongbao.classList.add('show');
            setTimeout(() => thongbao.classList.remove('show'), 2000);
            document.getElementById("fileAnhSua").focus();
            return;
        }
    
        if(!size)
        {
            loi.textContent = "Không được để trống size";
            thongbao.style.display = 'block';
            thongbao.classList.add('show');
            setTimeout(() => thongbao.classList.remove('show'), 2000);
            document.getElementById("cbSizeSua").focus();
            return;
        }
    
        if(!sl){
            loi.textContent = "Không được để trống số lượng";
            thongbao.style.display = 'block';
            thongbao.classList.add('show');
            setTimeout(() => thongbao.classList.remove('show'), 2000);
            document.getElementById("txtSlSua").focus();
            return; 
        }
    
        if(isNaN(sl))
        {
            loi.textContent = "Số lượng phải ở dạng số";
            thongbao.style.display = 'block';
            thongbao.classList.add('show');
            setTimeout(() => thongbao.classList.remove('show'), 2000);
            document.getElementById("txtSlSua").focus();
            return;  
        }
    
        if(sl < 0)
        {
            loi.textContent = "Số lượng phải lớn hơn 0";
            thongbao.style.display = 'block';
            thongbao.classList.add('show');
            setTimeout(() => thongbao.classList.remove('show'), 2000);
            document.getElementById("txtSlSua").focus();
            return;  
        }
    
        if(!mau)
        {
            loi.textContent = "Không được để trống màu";
            thongbao.style.display = 'block';
            thongbao.classList.add('show');
            setTimeout(() => thongbao.classList.remove('show'), 2000);
            document.getElementById("cbMauSua").focus();
            return;  
        }
    
        // Kiểm tra mã sản phẩm trước khi cập nhật
        fetch(`../ajax/checkID.php?product_id=${idsp}`)
            .then(res => res.json())
            .then(data => {
                if (!data.exists) {
                    loi.textContent = "Mã sản phẩm không tồn tại!";
                    document.getElementById("txtMaSua").focus();
                    return showError();
                }
    
                // Nếu mã sản phẩm hợp lệ → tiếp tục gửi form update
                const formData = new FormData(formSua);
    
                fetch("../ajax/updateBienThe.php", {
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

    
                            fetchBienThe(); // reload danh sách
                        } else {
                            alert(data.message || "Lỗi cập nhật");
                        }
                    });
            })
            .catch(error => {
                console.error("Lỗi khi kiểm tra mã sản phẩm:", error);
            });
            function showError() {
                thongbao.style.display = 'block';
                thongbao.classList.add('show');
                setTimeout(() => thongbao.classList.remove('show'), 2000);
            }
    });
    
    

    // Hiển thị ảnh ngay khi chọn
    document.getElementById("fileAnh").addEventListener("change", function () {
        const file = this.files[0];
        const imgPreview = document.querySelector("#hienthianh img");
        if (file) {
            imgPreview.src = URL.createObjectURL(file);
            imgPreview.style.display = "block";
        } else {
            imgPreview.src = "";
            imgPreview.style.display = "none";
        }
    });
    document.getElementById("fileAnhSua").addEventListener("change", function () {
        const file = this.files[0];
        const imgPreview = document.querySelector("#hienthianhSua img");
        if (file) {
            imgPreview.src = URL.createObjectURL(file);
            imgPreview.style.display = "block";
        } else {
            imgPreview.src = "";
            imgPreview.style.display = "none";
        }
    });

    document.querySelector(".formSua .btn-danger").addEventListener("click", function (e) {
        e.preventDefault();
        document.querySelector(".formSua").style.display = "none";
        document.querySelector(".overlay").style.display = "none";
        formSua.reset();
    });
    document.getElementById("fileAnhSua").addEventListener("change", function () {
        const file = this.files[0];
        const tenFile = document.getElementById("tenFileAnhSua");
        const imgPreview = document.querySelector("#hienthianhSua img");
        const thongbao = document.querySelector(".thongbaoLoi");
        const loi = thongbao.querySelector("p");
    
        if (file) {
            const validTypes = ["image/jpeg", "image/png", "image/gif"];
            if (!validTypes.includes(file.type)) {
                // ❌ Không hợp lệ → báo lỗi
                loi.textContent = "Sai định dạng ảnh!";
                thongbao.style.display = "block";
                thongbao.classList.add("show");
                setTimeout(() => thongbao.classList.remove("show"), 2000);
    
                this.value = ""; // reset input
                tenFile.textContent = ""; // xoá tên file
                imgPreview.src = "";
                imgPreview.style.display = "none";
                return;
            }
    
            // ✅ Hợp lệ → hiển thị ảnh và tên
            imgPreview.src = URL.createObjectURL(file);
            imgPreview.style.display = "block";
            tenFile.textContent = file.name;
        } else {
            imgPreview.src = "";
            imgPreview.style.display = "none";
            tenFile.textContent = "";
        }
    });
    
    document.getElementById("fileAnh").addEventListener("change", function () {
        const file = this.files[0];
        const imgPreview = document.querySelector("#hienthianh img");
        const thongbao = document.querySelector(".thongbaoLoi");
        const loi = thongbao.querySelector("p");
    
        if (file) {
            const validTypes = ["image/jpeg", "image/png", "image/gif"];
            if (!validTypes.includes(file.type)) {
                // ❌ Báo lỗi
                loi.textContent = "Sai định dạng ảnh!";
                thongbao.style.display = "block";
                thongbao.classList.add("show");
                setTimeout(() => thongbao.classList.remove("show"), 2000);
    
                this.value = ""; // reset input
                imgPreview.src = "";
                imgPreview.style.display = "none";
                return;
            }
    
            // ✅ Hiển thị ảnh
            imgPreview.src = URL.createObjectURL(file);
            imgPreview.style.display = "block";
        } else {
            imgPreview.src = "";
            imgPreview.style.display = "none";
        }
    });
    
});
