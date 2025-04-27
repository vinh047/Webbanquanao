let currentPage = 1;
const formLoc = document.getElementById("formLoc");
const permissionsElement = document.getElementById('permissions');
let permissions = [];
let cachedQuantities = {};
document.addEventListener('DOMContentLoaded', function () {
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
    // Hàm format giá
    const formatPrice = price => Number(price).toLocaleString('vi-VN');

    formLoc.addEventListener("submit", function (e) {
        e.preventDefault();
        currentPage = 1;
        loadPhieuNhap(currentPage); // lọc từ trang đầu
    });
    document.getElementById('filter-icon').addEventListener('click', function () {
        const filterBox = document.querySelector('.filter-loc');
        filterBox.classList.toggle('d-none');
    });
    
    
    document.getElementById('tatFormLoc').addEventListener('click',function()
    {
    const filterBox = document.querySelector('.filter-loc');
    filterBox.classList.toggle('d-none');
    });

    function adjustPageIfLastItem() {
        const btnCount = document.querySelectorAll(".btn-sua").length;
        if (btnCount === 1 && currentPage > 1) {
            currentPage -= 1;
        }
    }


    document.querySelector('.formSua button[type="button"]').addEventListener('click', function() {
    // Ẩn form khi nhấn Đóng, không xóa dữ liệu
    document.querySelector('.formSua').style.display = 'none';
});

// Khi bấm thêm SP mới
document.getElementById('btnThemSanPhamMoi').addEventListener('click', function () {
    const modal = new bootstrap.Modal(document.getElementById('modalNhapSanPham'));
    modal.show();
});



function capNhatLaiDropdownTenSanPham(id, name) {
    const allSelects = document.querySelectorAll('select[name^="products"][name$="[product_id]"]');
    const latestSelect = allSelects[allSelects.length - 1]; // chỉ lấy dropdown mới nhất
    if (latestSelect) {
        const option = document.createElement('option');
        option.value = id;
        option.textContent = `${id} - ${name}`;
        latestSelect.appendChild(option);
        // Không set selected để giữ nguyên lựa chọn của user
    }
}



document.getElementById('btnLuuSanPham').addEventListener('click', function () {
    const name = document.getElementById('txtTen').value.trim();
    const description = document.getElementById('txtMota').value.trim();
    const category_id = document.getElementById('cbLoai').value;
    const price = document.getElementById('txtGia').value.trim().replace(/\./g, '').replace(',', '.');
    const ptgg = document.getElementById('txtPT').value.trim();

    if (!permissions.includes('write')) {
        const tBquyen = document.querySelector('.thongBaoQuyen');
        tBquyen.style.display = 'block';
        tBquyen.classList.add('show');
        document.querySelector('.formNhapSanPham').style.display = 'none';
        setTimeout(() => tBquyen.classList.remove('show'), 2000);
        return; 
    }

    // Kiểm tra dữ liệu
    if (!name || !category_id || !price || isNaN(price)) {
        alert('Vui lòng nhập đầy đủ và đúng định dạng!');
        return;
    }

    const data = new FormData();
    data.append('name', name);
    data.append('description', description);
    data.append('category_id', category_id);
    data.append('price', price);
    data.append('ptgg', ptgg);

    fetch('./ajax/insertSanPham.php', {
        method: 'POST',
        body: data
    })
    .then(res => res.text()) // chuyển sang text để debug rõ
    .then(text => {
        console.log("Response:", text); // Xem rõ text trả về
        const res = JSON.parse(text);   // tự parse thủ công để dễ debug
    
        if (res.success) {
            const TBsp = document.querySelector('.thongbaoThemSp');
            TBsp.style.display = 'block';
            TBsp.classList.add('show');
            setTimeout(() => TBsp.classList.remove('show'), 2000);
    
            document.getElementById('txtTen').value = '';
            document.getElementById('txtMota').value = '';
            document.getElementById('cbLoai').value = '';
            document.getElementById('txtGia').value = '';
            document.getElementById('txtPT').value = '30';
    
            // ✅ Cập nhật lại dropdown
            capNhatLaiDropdownTenSanPham(res.product_id, res.name);
        } else {
            alert("Thêm thất bại: " + res.message);
        }
    })
    .catch(err => {
        console.error("Lỗi parse JSON hoặc server:", err);
        alert("Lỗi kết nối máy chủ hoặc phản hồi không hợp lệ.");
    });
    
});


function generateOptions(list, valueKey, labelKey) {
    return list.reduce((html, item) => {
        return html + `<option value="${item[valueKey]}">${item[valueKey]} - ${item[labelKey]}</option>`;
    }, `<option value="">-- Chọn --</option>`);
}
function generateProductForm(index) {
    const productOptions = generateOptions(productListFromPHP, 'product_id', 'name');
    const sizeOptions = generateOptions(sizeListFromPHP, 'size_id', 'name');
    const colorOptions = generateOptions(colorListFromPHP, 'color_id', 'name');

    return `
<div class="row g-3 align-items-start mb-3 border rounded p-3 bg-light">
  <!-- Tên sản phẩm -->
  <div class="col-md-3">
    <label class="form-label">Tên sản phẩm</label>
    <select name="products[${index}][product_id]" class="form-select" required>
      ${productOptions}
    </select>
  </div>

<div class="col-md-3 d-flex flex-column justify-content-end">
  <label class="form-label">Hình ảnh</label>
  <input type="file" name="products[${index}][image]" class="form-control previewable mb-2" accept="image/*" required>
  <img src="" alt="preview" class="img-thumbnail preview-img d-none mt-auto" style="height: 80px; width: 80px; object-fit: contain;">
</div>





  <!-- Màu -->
  <div class="col-md-2">
    <label class="form-label">Màu</label>
    <select name="products[${index}][color_id]" class="form-select" required>
      ${colorOptions}
    </select>
  </div>

  <!-- Size -->
  <div class="col-md-2">
    <label class="form-label">Size</label>
    <select name="products[${index}][size_id]" class="form-select" required>
      ${sizeOptions}
    </select>
  </div>

  <!-- Số lượng -->
  <div class="col-md-2">
    <label class="form-label">Số lượng</label>
    <input type="number" name="products[${index}][quantity]" class="form-control" min="1" required>
  </div>

  <!-- Xoá -->
  <div class="col-md-1 d-flex align-items-end">
    <button type="button" class="btn btn-danger btn-remove-form w-100">Xoá</button>
  </div>
</div>

    `;
}


document.getElementById('create_pn').addEventListener('click', function () {
    const modal = new bootstrap.Modal(document.getElementById('modalCreatePN'));
    modal.show();
});
document.getElementById('btnThemSanPham').addEventListener('click', function () {
    const container = document.getElementById('dynamic-product-forms');
    const index = container.children.length;
    const html = generateProductForm(index);
    container.insertAdjacentHTML('beforeend', html);
});

// Xoá dòng sản phẩm
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('btn-remove-form')) {
        e.target.closest('.row').remove();
    }
});

document.getElementById('formNhapPhieuNhap').addEventListener('submit', function (e) {
    e.preventDefault();

    const supplier_id = document.getElementById('supplier_id').value;
    const user_id = document.getElementById('user_id').value;

    const formData = new FormData();
    formData.append('supplier_id', supplier_id);
    formData.append('user_id', user_id);

    const productBlocks = document.querySelectorAll('#dynamic-product-forms .row');
    const productList = [];

    productBlocks.forEach((block, index) => {
        const product_id = block.querySelector(`[name^="products"][name*="[product_id]"]`)?.value;
        const color_id = block.querySelector(`[name^="products"][name*="[color_id]"]`)?.value;
        const size_id = block.querySelector(`[name^="products"][name*="[size_id]"]`)?.value;
        const quantity = block.querySelector(`[name^="products"][name*="[quantity]"]`)?.value;
        const image = block.querySelector(`[name^="products"][name*="[image]"]`)?.files[0];

        if (!product_id || !color_id || !size_id || !quantity || !image) return;

        productList.push({
            product_id,
            color_id,
            size_id,
            quantity,
            image_name: image.name
        });

        formData.append('images[]', image, image.name);
    });

    if (productList.length === 0) {
        alert("Vui lòng thêm ít nhất một sản phẩm đầy đủ!");
        return;
    }

    formData.append('products', JSON.stringify(productList));

    fetch('./ajax/insertPhieuNhap.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            alert("✅ " + res.message);
            // reset form
            document.getElementById('formNhapPhieuNhap').reset();
            document.getElementById('dynamic-product-forms').innerHTML = '';
            bootstrap.Modal.getInstance(document.getElementById('modalCreatePN')).hide(); // ẩn modal
        } else {
            alert("❌ " + res.message);
        }
    })
    .catch(error => {
        console.error("Lỗi gửi Ajax:", error);
        alert("❌ Đã xảy ra lỗi khi gửi dữ liệu!");
    });
});

document.getElementById('resetFormProduct').addEventListener('click', function () {
    document.getElementById('dynamic-product-forms').innerHTML = '';
});

document.addEventListener('change', function (e) {
    if (e.target.matches('input.previewable[type="file"]')) {
      const file = e.target.files[0];
      const container = e.target.closest('.col-md-3');
      const imgPreview = container.querySelector('.preview-img');
  
      if (file) {
        const reader = new FileReader();
        reader.onload = function (evt) {
          imgPreview.src = evt.target.result;
          imgPreview.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
      } else {
        imgPreview.src = '';
        imgPreview.classList.add('d-none');
      }
    }
  });
  
  
    function loadPhieuNhap(page = 1) {
        const formData = new FormData(formLoc);
        formData.append("pageproduct", page); // giữ phân trang
        fetch(`./ajax/quanlyPhieuNhap_ajax.php`,{
            method : "POST",
            body : formData
        })        .then(res => res.json())
        .then(data => {
            document.getElementById('product-list').innerHTML = data.products;
            document.getElementById("pagination").innerHTML = data.pagination;


            // Gán lại sự kiện cho nút chuyển trang
            document.querySelectorAll(".page-link-custom").forEach(btn => {
                btn.addEventListener("click", function (e) {
                    e.preventDefault();
                    currentPage = parseInt(this.dataset.page); // lưu lại trang hiện tại
                     loadPhieuNhap(this.dataset.page);
    
                });
            });

                // Gán sự kiện đổi trạng thái "Mở" → "Đã đóng"
                document.querySelectorAll('.btn-toggle-status').forEach(btn => {
                    
                    btn.addEventListener('click', function () {
                        const id = this.dataset.idpn;
                        
                        // Lưu ID vào nút xác nhận
                        document.getElementById('btnXacNhan').dataset.idpn = id;
                
                        // Hiện thông báo xác nhận tùy biến
                        document.getElementById('xacNhanCho').style.display = 'block';
                        document.querySelector('.overlay').style.display = 'block';
                    });
                });
                
                // Khi người dùng ấn nút Hủy
                document.getElementById('btnHuy').addEventListener('click', function () {
                    document.getElementById('xacNhanCho').style.display = 'none';
                    document.querySelector('.overlay').style.display = 'none';

                });
                
                // Khi người dùng ấn nút Xác nhận trong popup
                document.getElementById('btnXacNhan').addEventListener('click', async function () {
                    const id = this.dataset.idpn;
                    if (!permissions.includes('update')) {
                        const tBquyen = document.querySelector('.thongBaoQuyen');
                        tBquyen.style.display = 'block';
                        tBquyen.classList.add('show');
                        document.getElementById('xacNhanCho').style.display = 'none';
                        document.querySelector('.overlay').style.display = 'none';
                        setTimeout(() => tBquyen.classList.remove('show'), 2000);
                        return; 
                    }
                    try {
                        const res = await fetch('./ajax/moDongPN.php', {
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

                            loadPhieuNhap(currentPage);
                        } else {
                            alert("Đóng thất bại: " + data.message);
                        }
                    } catch (err) {
                        alert("Lỗi máy chủ!");
                        console.error('Lỗi:', err);
                    }
                });
                 

                document.addEventListener("click", function (e) {
                    if (e.target.classList.contains("btn-xemchitietPN")) {
                        const idpn = e.target.dataset.idpn;
                        let idpnGlobal = idpn;
                
                        function renderChiTietPhieuNhap(data) {
                            const tbody = document.querySelector('#chitiet-phieunhap tbody');
                            tbody.innerHTML = '';
                            const currentPage = data.pagination?.current || 1;
                            const totalPages = data.pagination?.total || 1;
                
                            data.data.forEach((item, index) => {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                                    <td>${(currentPage - 1) * 8 + index + 1}</td>
                                    <td>${item.product_name}</td>
                                    <td>${item.size_name}</td>
                                    <td>${item.color_name}</td>
                                    <td>${item.quantity}</td>
                                    <td>${item.stock}</td>
                                `;
                                tbody.appendChild(row);
                            });
                
                            const info = data.info;
                            if (info) {
                                document.getElementById('tenNCCPN').textContent = info.supplier_name;
                                document.getElementById('tenNVPN').textContent = info.user_name;
                                document.getElementById('tongSoLuongPN').textContent = info.tong_soluong;
                                document.getElementById('tongGiaTriPN').textContent = Number(info.tong_giatri).toLocaleString('vi-VN');
                            }
                
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
                            fetch(`./ajax/infoPN.php?idpn=${idpnGlobal}&page=${page}`)
                                .then(res => res.json())
                                .then(newData => {
                                    renderChiTietPhieuNhap(newData);
                        
                                    // ✅ Mở modal nếu chưa hiển thị
                                    const modalElement = document.getElementById('modalChiTietPhieuNhap');
                                    const existingModal = bootstrap.Modal.getInstance(modalElement);
                        
                                    if (!existingModal) {
                                        const modal = new bootstrap.Modal(modalElement);
                                        modal.show();
                                    } else {
                                        existingModal.show(); // nếu modal đã có thể gọi lại
                                    }
                                });
                        }
                        
                
                        fetchPage(1);
                    }
                });
                
                document.getElementById('modalChiTietPhieuNhap').addEventListener('hidden.bs.modal', function () {
                    // Loại bỏ backdrop nếu có
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style = '';
                });
                
            
            

                 document.querySelectorAll(".btn-xoa").forEach(btn => {
                    btn.addEventListener("click", function () {
                        const id = this.dataset.idpn; // Lấy ID của sản phẩm
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
                                popup.style.display='none';
                                overlay.style.display = 'none';
                                setTimeout(() => tBquyen.classList.remove('show'), 2000);
                                return; 
                            }
                            // Gửi yêu cầu xóa sản phẩm qua AJAX
                            fetch("./ajax/deletePhieuNhap.php", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded"
                                },
                                body: `id=${id}`
                            })
                            .then(res => res.text()) // nhận dạng lỗi trả về
                            .then(text => {
                                console.log("Kết quả xoá (text):", text); // ✅ để debug
                            
                                let data;
                                try {
                                    data = JSON.parse(text);
                                } catch (err) {
                                    console.error("Lỗi JSON parse:", err);
                                    const tbXoaTB = document.querySelector(".thongbaoXoaThatBai");
                                    tbXoaTB.style.display = "block";
                                    tbXoaTB.classList.add("show");
                                    setTimeout(() => tbXoaTB.classList.remove('show'), 2000);
                                    return;
                                }
                            
                                // tiếp tục như cũ nếu parse thành công
                                if (data.success) {
                                    const tbXoa = document.querySelector(".thongbaoXoaThanhCong");
                                    tbXoa.style.display = "block";
                                    tbXoa.classList.add("show");
                                    setTimeout(() => tbXoa.classList.remove('show'), 2000);
                                    adjustPageIfLastItem();
                                    loadPhieuNhap(currentPage);
                                } else {
                                    const tbXoaTB = document.querySelector(".thongbaoXoaThatBai");
                                    tbXoaTB.style.display = "block";
                                    tbXoaTB.classList.add("show");
                                    setTimeout(() => tbXoaTB.classList.remove('show'), 2000);
                                }
                            
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
            // Sự kiện nhập số trang
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
            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('btn-sua')) {
                    const idpn = e.target.dataset.idpn;
                    const idnv = e.target.dataset.idnv;
                    const idncc = e.target.dataset.idncc;
                    const gia = e.target.dataset.gia;
                    const ngaylap = e.target.dataset.ngaylap;
            
                    document.getElementById('txtMaPNsua').value = idpn;
                    document.getElementById('user_idSuaPN').value = idnv;
                    document.getElementById('supplier_idSuaPN').value = idncc;
                    document.getElementById('txtTongGT').value = formatPrice(gia);
                    document.getElementById('txtNgayLap').value = ngaylap;
            
                    fetchCTPhieuNhap(idpn);
            
                    const modal = new bootstrap.Modal(document.getElementById('modalSuaPhieuNhap'));
                    modal.show();
                }
            });

  // Hàm cập nhật chi tiết phiếu nhập

document.getElementById('btn_sua_pn').addEventListener('click', function () {
    const formData = new FormData();
    
    formData.append('txtMaPNsua', document.getElementById('txtMaPNsua').value);
    formData.append('supplier_idSuaPN', document.getElementById('supplier_idSuaPN').value);
    formData.append('user_idSuaPN', document.getElementById('user_idSuaPN').value);
    formData.append('txtTongGT', document.getElementById('txtTongGT').value);

    const productIds = document.querySelectorAll('.product_id');
    const variantIds = document.querySelectorAll('.variant_id');
    const quantities = document.querySelectorAll('.quantity');

    productIds.forEach(input => {
        formData.append('product_ids[]', input.value);
    });

    variantIds.forEach(input => {
        formData.append('variant_ids[]', input.value);
    });

    quantities.forEach(input => {
        formData.append('quantities[]', input.value);
    });

    fetch('./ajax/updateCTPhieuNhap.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Sửa phiếu nhập thành công!');
            // Ẩn modal và reload danh sách nếu muốn
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalSuaPhieuNhap'));
            modal.hide();
            loadPhieuNhap(); // Nếu bạn có hàm reload danh sách
        } else {
            alert('Sửa phiếu nhập thất bại: ' + data.message);
        }
    })
    .catch(err => {
        console.error('Lỗi kết nối:', err);
        alert('Lỗi máy chủ.');
    });
});
  
  
function fetchCTPhieuNhap(idpn, page = 1) {
    const tbody = document.querySelector('#tableChiTietPhieuNhap tbody');
    const paginationWrap = document.getElementById("pagination-sua-phieunhap");

    if (!tbody || !paginationWrap) return;

    tbody.innerHTML = '<tr><td colspan="3" class="text-center">Đang tải...</td></tr>';
    paginationWrap.innerHTML = '';

    fetch(`./ajax/getCTPhieuNhap.php?idpn=${idpn}&page=${page}`)
    .then(res => res.text())
    .then(text => {
        console.log('📦 Response Text:', text); // 👈 in ra text nhận từ PHP server
        const data = JSON.parse(text); // 👈 parse thủ công

        console.log('🛠 Parsed Data:', data); // 👈 in ra object JSON

        if (!data.success) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Không lấy được dữ liệu</td></tr>';
            return;
        }

        tbody.innerHTML = '';
        const currentPage = data.pagination?.current || 1;
        const totalPages = data.pagination?.total || 1;

        console.log('🔎 currentPage:', currentPage, 'totalPages:', totalPages); // 👈 in phân trang

        data.details.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td><input type="text" class="form-control bg-light product_id" value="${item.product_id}" readonly></td>
              <td><input type="text" class="form-control bg-light variant_id" value="${item.variant_id}" readonly></td>
              <td><input type="number" class="form-control quantity" name="quantities[]" value="${item.quantity}" min="1" required></td>
            `;
            tbody.appendChild(tr);
        });

        renderPaginationSuaPhieuNhap(idpn, currentPage, totalPages);
    })
    .catch(err => {
        console.error('❌ Lỗi fetch chi tiết phiếu nhập:', err);
        tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Lỗi kết nối máy chủ</td></tr>';
    });

}


function renderPaginationSuaPhieuNhap(idpn, currentPage, totalPages) {
    const paginationWrap = document.getElementById("pagination-sua-phieunhap");
    if (!paginationWrap) return;
    paginationWrap.innerHTML = '';

    // ✨ BỎ ĐI điều kiện if (totalPages > 1)
    const btnPrev = document.createElement("button");
    btnPrev.innerHTML = '<i class="fa fa-chevron-left text-dark"></i>';
    btnPrev.className = "btn btn-outline-secondary";
    btnPrev.disabled = currentPage === 1;
    btnPrev.onclick = () => fetchCTPhieuNhap(idpn, currentPage - 1);

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
            fetchCTPhieuNhap(idpn, value);
        }
    });

    const spanTotal = document.createElement("span");
    spanTotal.innerHTML = `/ ${totalPages}`;
    spanTotal.classList.add("mx-1");

    const btnNext = document.createElement("button");
    btnNext.innerHTML = '<i class="fa fa-chevron-right text-dark"></i>';
    btnNext.className = "btn btn-outline-secondary";
    btnNext.disabled = currentPage === totalPages;
    btnNext.onclick = () => fetchCTPhieuNhap(idpn, currentPage + 1);

    paginationWrap.appendChild(btnPrev);
    paginationWrap.appendChild(inputPage);
    paginationWrap.appendChild(spanTotal);
    paginationWrap.appendChild(btnNext);
}




  
  
        })
        .catch(error => {
            console.error('Lỗi khi tải phiếu nhập:', error);
        });
}

document.getElementById('modalSuaPhieuNhap').addEventListener('hidden.bs.modal', function () {
    // Loại bỏ backdrop nếu có
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
    document.body.style = '';
});

// Gọi hàm này khi trang vừa load
loadPhieuNhap();

});

