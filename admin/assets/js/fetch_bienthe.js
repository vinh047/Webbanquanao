document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formNhapSPbienThe");
    const thongbao = document.querySelector(".thongbaoLoi");
    const loi = thongbao.querySelector("p");
    const tbThanhCong = document.querySelector(".thongbaoThanhCong");
    const tc = tbThanhCong.querySelector("p");
    const formSua = document.getElementById("formSuaSPbienThe");
    let currentPage = 1;
    const formLoc = document.getElementById("formLoc");
    const permissionsElement = document.getElementById('permissions');
    let permissions = [];

    // Lấy dữ liệu từ thuộc tính data-permissions
    if (permissionsElement && permissionsElement.getAttribute('data-permissions')) {
        try {
            permissions = JSON.parse(permissionsElement.getAttribute('data-permissions'));
            console.log('Permissions received:', permissions); // Kiểm tra giá trị permissions
        } catch (error) {
            console.error('Lỗi phân tích cú pháp JSON:', error);
        }
    } else {
        console.log('Không có dữ liệu permissions hợp lệ');
    }


    function adjustPageIfLastItem() {
        const btnCount = document.querySelectorAll(".btn-sua").length;
        if (btnCount === 1 && currentPage > 1) {
            currentPage -= 1;
        }
    }
    function fetchBienThe(page = 1) {
        const formData = new FormData(formLoc);
        formData.append("pageproduct", page); // giữ phân trang

        fetch(`./ajax/quanlyBienThe_ajax.php`, {
            method: "POST",
            body: formData
        })
                    .then(res => res.json())
            .then(data => {
                document.getElementById("product-list").innerHTML = data.products;
                document.getElementById("pagination").innerHTML = data.pagination;

                document.querySelectorAll(".page-link-custom").forEach(btn => {
                    btn.addEventListener("click", function (e) {
                        e.preventDefault();
                        currentPage = parseInt(this.dataset.page); // ✅ lưu lại
                        fetchBienThe(currentPage);
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
                                currentPage = page; 
                                fetchBienThe(page); // ✅ đúng
                            }
                        }
                    });
                }

                document.addEventListener("click", function (e) {
                    if (e.target.classList.contains("btn-xemchitietPN")) {
                      const variantId = e.target.dataset.id; // lấy variant_id từ data-id
                      let currentVariantId = variantId;
                  
                      function renderChiTietBienThe(data) {
                        const tbody = document.querySelector('#chitiet-phieunhap tbody');
                        tbody.innerHTML = '';
                  
                        const currentPage = data.pagination?.current || 1;
                        const totalPages = data.pagination?.total || 1;
                  
                        // Render các dòng chi tiết phiếu nhập
                        data.chitiet.forEach((item, index) => {
                          const row = document.createElement('tr');
                          row.innerHTML = `
                            <td class="text-center">${(currentPage - 1) * 5 + index + 1}</td>
                            <td class="text-center">${item.id_ctpn}</td>
                            <td class="text-center">${item.id_pn}</td>
                            <td class="text-center">${item.id_sp}</td>
                            <td class="text-center">${item.id_bt}</td>
                            <td class="text-center">${item.so_luong}</td>
                            <td class="text-center">${item.ngay_nhap}</td>
                          `;
                          tbody.appendChild(row);
                        });
                  
                        // Hiển thị info biến thể
                        const info = data.info;
                        if (info) {
                          document.getElementById('ctbt_image').src = `../../assets/img/sanpham/${info.anh}`;
                          document.getElementById('ctbt_tensp').textContent = info.ten_sp;
                          document.getElementById('ctbt_mau').textContent = info.mau;
                          document.getElementById('ctbt_size').textContent = info.size;
                          document.getElementById('ctbt_sl').textContent = info.stock;
                          document.getElementById('idbt_sp').textContent = info.id_bt_sp;
                        }
                  
                        // Phân trang
                        const paginationWrap = document.getElementById("modal-pagination");
                        paginationWrap.innerHTML = '';
                  
                        if (totalPages > 1) {
                          const btnPrev = document.createElement("button");
                          btnPrev.innerHTML = '<i class="fa fa-chevron-left text-dark"></i>';
                          btnPrev.className = "btn btn-outline-secondary";
                          btnPrev.disabled = currentPage === 1;
                          btnPrev.onclick = () => fetchPage(currentPage - 1);
                  
                          const inputPage = document.createElement("input");
                          inputPage.type = "number";
                          inputPage.min = 1;
                          inputPage.max = totalPages;
                          inputPage.value = currentPage;
                          inputPage.style.width = "60px";
                          inputPage.className = "form-control d-inline-block text-center mx-2";
                          inputPage.addEventListener("keypress", function (e) {
                            if (e.key === "Enter") {
                              let value = parseInt(this.value);
                              if (isNaN(value)) return;
                              if (value < 1) value = 1;
                              if (value > totalPages) value = totalPages;
                              fetchPage(value);
                            }
                          });
                  
                          const spanTotal = document.createElement("span");
                          spanTotal.innerHTML = `/ ${totalPages}`;
                          spanTotal.classList.add("mx-1");
                  
                          const btnNext = document.createElement("button");
                          btnNext.innerHTML = '<i class="fa fa-chevron-right text-dark"></i>';
                          btnNext.className = "btn btn-outline-secondary";
                          btnNext.disabled = currentPage === totalPages;
                          btnNext.onclick = () => fetchPage(currentPage + 1);
                  
                          paginationWrap.appendChild(btnPrev);
                          paginationWrap.appendChild(inputPage);
                          paginationWrap.appendChild(spanTotal);
                          paginationWrap.appendChild(btnNext);
                        }
                      }
                  
                      function fetchPage(page) {
                        fetch(`./ajax/get_chitiet_phieunhap.php`, {
                          method: 'POST',
                          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                          body: `variant_id=${currentVariantId}&page=${page}`
                        })
                          .then(res => res.text())
                          .then(text => {
                            try {
                              const data = JSON.parse(text);
                              renderChiTietBienThe(data);
                              const modalElement = document.getElementById('modalChiTietBienThe');
                              const existingModal = bootstrap.Modal.getOrCreateInstance(modalElement);
                              existingModal.show();
                            } catch (e) {
                              console.error("❌ Lỗi parse JSON:", e);
                              console.log("Phản hồi server:", text);
                            }
                          });
                      }
                  
                      fetchPage(1);
                    }
                  });
                  
                  // Khi modal đóng, dọn lại giao diện
                  document.getElementById('modalChiTietBienThe').addEventListener('hidden.bs.modal', function () {
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style = '';
                  });
                  
                  
                
                  document.querySelectorAll(".btn-sua").forEach(btn => {
                    btn.addEventListener("click", function (e) {
                        const idvr = this.dataset.idvr;
                        const idsp = this.dataset.idsp;
                        const anh = this.dataset.anh;
                        const size = this.dataset.size;
                        const soluong = this.dataset.soluong;
                        const mau = this.dataset.mau;
                        const idctpn = this.dataset.idct;
                
                        const formSua = document.getElementById("formSuaSPbienThe");
                
                        // Truyền dữ liệu vào form
                        formSua.querySelector("input[name='txtMaBt']").value = idvr;
                        formSua.querySelector("input[name='txtMaSua']").value = idsp;
                        formSua.querySelector("input[name='txtMaCTPN']").value = idctpn;
                        formSua.querySelector("select[name='cbSizeSua']").value = size;
                        formSua.querySelector("select[name='cbMauSua']").value = mau;
                        formSua.querySelector("input[name='txtSlSua']").value = soluong;
                
                        // Gán tên file ảnh vào khu vực hiển thị tên file
                        document.getElementById("tenFileAnhSua").textContent = anh;
                
                        // Gán ảnh preview
                        const imgPreview = document.querySelector("#hienthianhSua img");
                        imgPreview.src = "../../assets/img/sanpham/" + anh;
                        imgPreview.style.display = "block";
                
                        // ✅ Mở modal Bootstrap
                        const modalSuaBienThe = new bootstrap.Modal(document.getElementById('modalSuaBienThe'));
                        modalSuaBienThe.show();
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
                                if (!permissions.includes('delete')) {
                                const tBquyen = document.querySelector('.thongBaoQuyen');
                                tBquyen.style.display = 'block';
                                tBquyen.classList.add('show');
                                popup.style.display = "none";
                                document.querySelector(".overlay").style.display = "none";

                                setTimeout(() => tBquyen.classList.remove('show'), 2000);
                                return; 
                            }
                            // Gửi yêu cầu xóa sản phẩm qua AJAX
                            fetch("./ajax/deleteBienThe.php", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded"
                                },
                                body: `variant_id=${id}`
                            })
                            .then(res => res.json())
                            .then(data => {
                                console.log("Xoá biến thể trả về:", data); // ✅ debug
                                const action = data.action; // 👈 thiếu dòng này!
                                if (data.success) {
                                    // Hiển thị thông báo xóa thành công
                                    if(action === 'hidden')
                                    {
                                        const tbXoane = document.querySelector(".thongbaoXoaHiddenThanhCong");
                                        tbXoane.style.display = "block";
                                        tbXoane.classList.add("show");
    
                                        setTimeout(() => tbXoane.classList.remove('show'), 2000);
    
                                        if (document.querySelectorAll(".btn-sua").length === 1 && currentPage > 1) {
                                            currentPage -= 1; // nếu chỉ còn 1 sản phẩm → lùi trang
                                        }
                                        // Tải lại danh sách sản phẩm sau khi xóa
                                        adjustPageIfLastItem();
                                        fetchBienThe(currentPage);
                                    }else
                                    {
                                        const tbXoa = document.querySelector(".thongbaoXoaThanhCong");
                                        tbXoa.style.display = "block";
                                        tbXoa.classList.add("show");
    
                                        setTimeout(() => tbXoa.classList.remove('show'), 2000);
    
                                        if (document.querySelectorAll(".btn-sua").length === 1 && currentPage > 1) {
                                            currentPage -= 1; // nếu chỉ còn 1 sản phẩm → lùi trang
                                        }
                                        // Tải lại danh sách sản phẩm sau khi xóa
                                        fetchBienThe(currentPage);
                                    }
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

    document.getElementById('tatFormLoc').addEventListener('click',function()
    {
        const filterBox = document.querySelector('.filter-loc');
        filterBox.classList.toggle('d-none');
    });
    formLoc.addEventListener("submit", function (e) {
        e.preventDefault();
        currentPage = 1;
        fetchBienThe(currentPage); // lọc từ trang đầu
    });
    fetchBienThe(currentPage); // load ban đầu
    document.querySelector(".filter-icon").addEventListener("click", function () {
        const filterBox = document.querySelector(".filter-loc");
        filterBox.classList.toggle("d-none"); // toggle hiện/ẩn
    });
    
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


        fetch(`./ajax/checkID.php?product_id=${idsp}`)
            .then(res => res.json())
            .then(data => {
                if (!data.exists) {
                    loi.textContent = "Mã sản phẩm không tồn tại!";
                    return showError();
                }

                // ✅ Nếu hợp lệ và tồn tại, tiếp tục thêm
                const formData = new FormData(form);
                fetch('./ajax/insertBienThe.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchBienThe(currentPage);
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
    if (!formSua) {
        console.error("Không tìm thấy formSuaSPbienThe");
        return;
    }
    formSua.addEventListener("submit", async function (e) {
        e.preventDefault();
        console.log("Đã submit form!");

        const idsp = document.getElementById("txtMaSua").value.trim();
        const img = document.getElementById("fileAnhSua").value;
        const size = document.getElementById("cbSizeSua").value.trim();
        const mau = document.getElementById("cbMauSua").value;
        const sl = document.getElementById("txtSlSua").value.trim();
        const idBienThe = document.getElementById("txtMaBt").value; // 👈 mã biến thể (ẩn)
        // Lấy tên ảnh hiện tại trong thẻ <div id="tenFileAnhSua">
        const tenAnh = document.getElementById("tenFileAnhSua").textContent.trim();
        
        if (!permissions.includes('update')) {
            const tBquyen = document.querySelector('.thongBaoQuyen');
            tBquyen.style.display = 'block';
            tBquyen.classList.add('show');
            setTimeout(() => tBquyen.classList.remove('show'), 2000);
            document.querySelector('.formSua').style.display = 'none';
            document.querySelector('.overlay').style.display = 'none';
            return; 
        }
    
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
    
        const file = document.getElementById("fileAnhSua").files[0]; 
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
        try {
            const resID = await fetch(`./ajax/checkID.php?product_id=${idsp}`);
            const dataID = await resID.json();
            if (!dataID.exists) return showError("Mã sản phẩm không tồn tại!");
    
            // 🧠 Kiểm tra biến thể đã tồn tại chưa
            const urlBT = `./ajax/checkBT.php?product_id=${idsp}&size_id=${size}&color_id=${mau}&image=${encodeURIComponent(tenAnh)}&current_id=${idBienThe}`;
            const resBT = await fetch(urlBT);
            const dataBT = await resBT.json();
    
            if (dataBT.exists) return showError("Đã tồn tại biến thể này rồi!");
    
            // ✅ Tiến hành gửi form
            const formData = new FormData(formSua);
            const resUpdate = await fetch("./ajax/updateBienThe.php", {
                method: "POST",
                body: formData
            });
            const result = await resUpdate.json();
    
            if (result.success) {
                const tbUpdate = document.querySelector(".thongbaoUpdateThanhCong");
                tbUpdate.style.display = "block";
                tbUpdate.classList.add("show");
                setTimeout(() => tbUpdate.classList.remove('show'), 2000);
                            // ✅ Ẩn modal sau khi cập nhật thành công
            const modalElement = document.getElementById('modalSuaBienThe');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.hide();
            }
                adjustPageIfLastItem();
                fetchBienThe(currentPage);
            } else {
                alert(result.message || "Lỗi cập nhật");
            }
    
        } catch (err) {
            console.error("Lỗi mạng hoặc máy chủ:", err);
            showError("Lỗi kết nối tới máy chủ!");
        }
        function showError(message) {
            loi.textContent = message; // ⚠️ Đây là dòng bạn thiếu!
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
