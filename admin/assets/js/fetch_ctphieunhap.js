let productList = [];
let productCount = 0;
let productPendingToAdd = null; 
document.addEventListener('DOMContentLoaded', function () {
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

    let currentPage = 1; // ⚠️ Fix thiếu biến này gây lỗi load lại trang khi xoá
    function adjustPageIfLastItem() {
        const btnCount = document.querySelectorAll(".btn-sua").length;
        if (btnCount === 1 && currentPage > 1) {
            currentPage -= 1;
        }
    }
    function loadPhieuNhap(page = 1) {
        const formData = new FormData(formLoc);
        formData.append("pageproduct", page); // giữ phân trang
        // ✅ Loại bỏ các field rỗng
        fetch(`./ajax/quanlyChiTietPhieuNhap_ajax.php`,{
            method : "POST",
            body : formData
        })
            .then(res => res.json())
            .then(data => {
                document.getElementById('product-list').innerHTML = data.products;
                document.getElementById("pagination").innerHTML = data.pagination;
                
                document.querySelectorAll(".page-link-custom").forEach(btn => {
                    btn.addEventListener("click", function (e) {
                        e.preventDefault();
                        currentPage = parseInt(this.dataset.page);
                        loadPhieuNhap(this.dataset.page);
                    });
                });

                document.querySelectorAll(".btn-xoa").forEach(btn => {
                    btn.addEventListener("click", function () {
                        const id = this.dataset.idct;
                        const popup = document.querySelector(".thongBaoXoa");
                        const overlay = document.querySelector(".overlay");

                        popup.style.display = "block";
                        overlay.style.display = "block";

                        popup.querySelector(".btn-danger").onclick = function () {
                            if (!permissions.includes('delete')) {
                                const tBquyen = document.querySelector('.thongBaoQuyen');
                                tBquyen.style.display = 'block';
                                tBquyen.classList.add('show');
                                popup.style.display = 'none';
                                overlay.style.display = 'none';
                                setTimeout(() => tBquyen.classList.remove('show'), 2000);
                                return; 
                            }
                            fetch("./ajax/deleteCTphieunhap.php", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded"
                                },
                                body: `id=${id}`                            
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    const tbXoa = document.querySelector(".thongbaoXoaThanhCong");
                                    tbXoa.style.display = "block";
                                    tbXoa.classList.add("show");
                                    setTimeout(() => tbXoa.classList.remove('show'), 2000);
                                    adjustPageIfLastItem();
                                    loadPhieuNhap(currentPage);
                                } else {
                                    const tbXoaTB = document.querySelector(".thongbaoXoaKhongThanhCong");
                                    tbXoaTB.style.display = "block";
                                    tbXoaTB.classList.add("show");
                                    setTimeout(() => tbXoaTB.classList.remove('show'), 2000);

                                }

                                popup.style.display = "none";
                                overlay.style.display = "none";
                            });
                        };

                        popup.querySelector(".btn-primary").onclick = function () {
                            popup.style.display = "none";
                            overlay.style.display = "none";
                        };
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
                                loadPhieuNhap(page);
                            }
                        }
                    });
                }

                // Gán sự kiện đổi trạng thái "Mở" → "Đã đóng"
                document.querySelectorAll('.btn-toggle-status').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const id = this.dataset.idct; // lấy id chi tiết
                
                        // Gán lại ID vào nút xác nhận trong popup
                        const btnXacNhan = document.getElementById('btnXacNhan');
                        btnXacNhan.dataset.type = 'ctpn'; // đánh dấu loại
                        btnXacNhan.dataset.id = id;
                
                        // Hiện popup
                        document.getElementById('xacNhanCho').style.display = 'block';
                        document.querySelector('.overlay').style.display = 'block';

                    });
                });
                document.getElementById('btnXacNhan').addEventListener('click', async function () {
                    const type = this.dataset.type; // 'pn' hoặc 'ctpn'
                    const id = this.dataset.id;
                    if (!permissions.includes('update')) {
                        const tBquyen = document.querySelector('.thongBaoQuyen');
                        tBquyen.style.display = 'block';
                        tBquyen.classList.add('show');
                        document.getElementById('xacNhanCho').style.display = 'none';
                        document.querySelector('.overlay').style.display = 'none';
                        setTimeout(() => tBquyen.classList.remove('show'), 2000);
                        return; 
                    }
                    let url = '';
                    if (type === 'pn') url = './ajax/moDongPN.php';
                    else if (type === 'ctpn') url = './ajax/moDongCTPN.php';
                    else return;
                
                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `id=${id}&status=0`
                        });
                
                        if (!res.ok) throw new Error(`HTTP ${res.status}`);
                
                        const data = await res.json();
                
                        if (data.success) {
                            document.getElementById('xacNhanCho').style.display = 'none';
                            document.querySelector('.overlay').style.display = 'none';
                            loadPhieuNhap(currentPage); // ✅ reload bảng
                        } else {
                            alert("Đóng thất bại: " + data.message);
                        }
                    } catch (err) {
                        alert("Lỗi máy chủ!");
                        console.error('Lỗi:', err);
                    }
                });
                document.getElementById('btnHuy').addEventListener('click', function () {
                    document.getElementById('xacNhanCho').style.display = 'none';
                    document.querySelector('.overlay').style.display = 'none';

                });
                
                
                            

                document.querySelectorAll('.btn-xemchitiet').forEach(button => {
                    button.addEventListener('click', async function () {
                      const id = this.dataset.idct;
                      try {
                        const res = await fetch(`./ajax/infoCTPN.php?id=${id}`);
                        const text = await res.text();
                        console.log("Kết quả trả về:", text);
                  
                        const data = JSON.parse(text);
                  
                        if (data.success) {
                          const info = data.data;
                          document.getElementById('ctbt_image').src = `../../assets/img/sanpham/${info.image}`;
                          document.getElementById('ctbt_tensp').textContent = info.product_name;
                          document.getElementById('ctbt_mau').textContent = info.color_name;
                          document.getElementById('ctbt_size').textContent = info.size_name;
                          document.getElementById('ctbt_sl').textContent = info.quantity;
                  
                          // ✅ Hiển thị giá nhập và tổng tiền
                          document.getElementById('ctbt_gia').textContent = parseInt(info.unit_price).toLocaleString();
                          document.getElementById('ctbt_thanhtien').textContent = parseInt(info.total_price).toLocaleString();
                  
                          document.getElementById('ctbt_ngay').textContent = info.created_at;
                  
                          const modal = new bootstrap.Modal(document.getElementById('modalChiTietBienThe'));
                          modal.show();
                        } else {
                          alert(data.message || 'Không lấy được chi tiết');
                        }
                      } catch (err) {
                        alert('Lỗi kết nối máy chủ!');
                        console.error(err);
                      }
                    });
                  });
                  
                              

                document.querySelectorAll('.btn-sua').forEach(button => {
                    button.addEventListener('click', function () {
                        document.querySelector('.formSuaPN').style.display = 'block';
                        document.querySelector('.overlay').style.display = 'block';

                        const idctpn = this.dataset.idct;
                        const idpn = this.dataset.idpn;
                        const idsp = this.dataset.idsp;
                        const idbt = this.dataset.variant;
                        const ngaylap = this.dataset.ngaylap;
                        const soluong = this.dataset.soluong;

                        document.getElementById('txtMaCTPNsua').value = idctpn;
                        document.getElementById('txtMaPNsua').value = idpn;
                        document.getElementById('txtMaSPsua').value = idsp;
                        document.getElementById('txtNgayLap').value = ngaylap;
                        document.getElementById('txtMaBTsua').value = idbt;
                        document.getElementById('txtSlsuaTon').value = soluong;
                    });
                });
            });

            
    }

    // ⚠️ Gọi hàm để load dữ liệu khi vừa DOM ready
    loadPhieuNhap();

    // (Phần dưới giữ nguyên - đã chuẩn)
    formLoc.addEventListener("submit", function (e) {
        e.preventDefault();
        currentPage = 1;
        loadPhieuNhap(currentPage); // lọc từ trang đầu
    });
    document.getElementById('filter-icon').addEventListener('click', function () {
        const filterBox = document.querySelector('.filter-loc');
        filterBox.classList.toggle('d-none');
    });
    
    document.addEventListener('click', function (e) {
        const filterBox = document.querySelector('.filter-loc');
        const icon = document.getElementById('filter-icon');
    
        // if (!filterBox.contains(e.target) && !icon.contains(e.target)) {
        //     filterBox.classList.add('d-none');
        // }
    });

    document.getElementById('tatFormLoc').addEventListener('click',function()
{
    const filterBox = document.querySelector('.filter-loc');
    filterBox.classList.toggle('d-none');
});

    function formatPrice(price) {
        return Number(price).toLocaleString('vi-VN');
    }

    function showError(message) {
        const thongbao = document.querySelector(".thongbaoLoi");
        const loi = thongbao.querySelector("p");
        loi.textContent = message;
        thongbao.style.display = 'block';
        thongbao.classList.add('show');
        setTimeout(() => thongbao.classList.remove('show'), 2000);
    }


    document.getElementById('btn_add_product_sua').addEventListener('click', async function () {
        const id = parseInt(document.getElementById('txtSTT').value);
        const import_receipt_id = document.getElementById('txtMaPNSua').value.trim();
        const product_id = document.getElementById('txtMaSua').value.trim();
        const quantity = parseFloat(document.getElementById('txtSlSua').value.trim());

        const cbSizeSelect = document.getElementById('cbSizeSua');
        const cbColorSelect = document.getElementById('cbMauSua');
        const size_id = cbSizeSelect.value;
        const color_id = cbColorSelect.value;
        const sizeName = cbSizeSelect.options[cbSizeSelect.selectedIndex].text;
        const colorName = cbColorSelect.options[cbColorSelect.selectedIndex].text;

        let image = document.getElementById('tenFileAnhSua').textContent;
        const newImageInput = document.getElementById('fileAnhSua');
        if (newImageInput.files.length > 0) {
            image = newImageInput.files[0].name;
        }

        if (!import_receipt_id || !product_id || !size_id || !color_id || isNaN(quantity) || quantity <= 0) {
            return showError("Vui lòng nhập đầy đủ và đúng thông tin để sửa");
        }

        try {
            const res = await fetch(`./ajax/checkPN.php?pn_id=${import_receipt_id}`);
            const data = await res.json();
            if (!data.exists) {
                return showError("Mã phiếu nhập không tồn tại!");
            }
                        
        } catch (error) {
            return showError("Lỗi kiểm tra mã phiếu nhập!");

        }

        try {
            const res = await fetch(`./ajax/checkID.php?product_id=${product_id}`);
            const data = await res.json();
            if (!data.exists) {
                return showError("Mã sản phẩm không tồn tại!");
            }
        } catch (error) {
            return showError("Lỗi kiểm tra mã sản phẩm!");
        }

        const productIndex = productList.findIndex(p => p.id === id);
        if (productIndex !== -1) {
            productList[productIndex] = {
                id,
                import_receipt_id,
                product_id,
                image,
                size_id,
                size: sizeName,
                color_id,
                color: colorName,
                quantity
            };
        }
        updateProductList();
        document.querySelector('.formSua').style.display = 'none';
    });


    document.querySelector('.formSua button.btn-outline-primary').addEventListener('click', () => {
        document.querySelector('.formSua').style.display = 'none';
    });

        // Nút Có
        document.getElementById('btnCoTrung').addEventListener('click', () => {
            if (productPendingToAdd) {
                productPendingToAdd.existedItem.quantity += productPendingToAdd.quantity;
                updateProductList();
            }
            document.getElementById('boxTrungSP').style.display = 'none';
            productPendingToAdd = null;
        });
    
        // Nút Không
        document.getElementById('btnKhongTrung').addEventListener('click', () => {
            document.getElementById('boxTrungSP').style.display = 'none';
            productPendingToAdd = null;
        });

        document.getElementById("btn_sua_pn").addEventListener("click", async function () {
            const idctpn = document.getElementById("txtMaCTPNsua").value.trim();
            const idpn = document.getElementById("txtMaPNsua").value.trim();
            const idsp = document.getElementById("txtMaSPsua").value.trim();
            const variantId = document.getElementById("txtMaBTsua").value.trim();
            const quantity = parseInt(document.getElementById("txtSlsuaTon").value.trim());
        
            if (!permissions.includes('update')) {
                const tBquyen = document.querySelector('.thongBaoQuyen');
                tBquyen.style.display = 'block';
                tBquyen.classList.add('show');
                document.querySelector('.formSuaPN').style.display = 'none';
                document.querySelector('.overlay').style.display = 'none';
                setTimeout(() => tBquyen.classList.remove('show'), 2000);
                return; 
            }


            if (!idpn) return showError("Mã phiếu nhập không được để trống!");
            if (!idsp) return showError("Mã sản phẩm không được để trống!");
            if (!variantId) return showError("Mã biến thể không được để trống!");
            if (isNaN(quantity) || quantity <= 0) return showError("Số lượng phải lớn hơn 0!");
        
            try {
                // Kiểm tra mã phiếu nhập có tồn tại không
                const checkPN = await fetch(`./ajax/checkPN.php?pn_id=${idpn}`);
                const dataPN = await checkPN.json();
                if (!dataPN.exists) return showError("Mã phiếu nhập không tồn tại!");
        
                // Kiểm tra sản phẩm có tồn tại không
                const checkSP = await fetch(`./ajax/checkID.php?product_id=${idsp}`);
                const dataSP = await checkSP.json();
                if (!dataSP.exists) return showError("Mã sản phẩm không tồn tại!");
        
        
                // Gửi dữ liệu đi
                const formData = new FormData();
                formData.append("txtMaCTPNsua", idctpn);
                formData.append("txtMaPNsua", idpn);
                formData.append("txtMaSPsua", idsp);
                formData.append("txtSlsuaTon", quantity);
                formData.append("txtMaBTsua", variantId);
        
                const res = await fetch("./ajax/updateCTPhieuNhap.php", {
                    method: "POST",
                    body: formData
                });
                const text = await res.text();   // log để xem lỗi gì
                console.log("Kết quả từ PHP:", text);
                let result = {};
                try {
                    result = JSON.parse(text); // ✔
                } catch (err) {
                    alert("Kết quả trả về không phải JSON. Lỗi máy chủ.");
                    console.error("Raw response:", text);
                    return;
                }
                        
                if (result.success) {
                    const tb = document.querySelector(".thongbaoUpdateThanhCong");
                    tb.style.display = "block";
                    tb.classList.add("show");
                    setTimeout(() => tb.classList.remove("show"), 2000);
        
                    document.querySelector(".formSuaPN").style.display = "none";
                    document.querySelector(".overlay").style.display = "none";
                    adjustPageIfLastItem();

                    loadPhieuNhap(currentPage);
                } else {
                    alert(result.message || "Cập nhật không thành công!");
                }
            } catch (err) {
                alert("Lỗi kết nối đến máy chủ!");
                console.error(err);
            }
        });
        

    document.getElementById("btn_dong").addEventListener('click',function()
{
    document.querySelector(".formSuaPN").style.display='none';
    document.querySelector(".overlay").style.display='none';
});
}); 