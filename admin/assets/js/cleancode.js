let currentPage = 1;

const formLoc = document.getElementById("formLoc");
const permissionsElement = document.getElementById('permissions');
let permissions = [];
const formatPrice = price => Number(price).toLocaleString('vi-VN');
let cachedQuantities = {};
let deleteAction = null;
document.addEventListener('DOMContentLoaded', function () {
    ktraQuyen();
    xuLyLoc();
    luuSanPham();
    xuLyThemPhieuNhap();
    guiFormPhieuNhap();
    resetForm();
    xuLyAnh();
    loadPhieuNhap();
        const btnThemSanPhamMoi = document.getElementById('btnThemSanPhamMoi');
        if (btnThemSanPhamMoi) {
            btnThemSanPhamMoi.addEventListener('click', function () {
                const modalElement = document.getElementById('modalNhapSanPham');
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            });
        }
    
    
});
function tbThanhCong(mess)
{
    loi.textContent = mess;
    const tbTC = document.querySelector('.thongbaoXoaThanhCong');
    const loi = tbTC.querySelector('p');
    tbTC.style.display = 'block';
    tbTC.classList.add('show');
    setTimeout(() => tbTC.classList.remove('show'), 2000);
}
function showError(mess)
{
    const tbTC = document.querySelector('.thongbaoXoaKhongThanhCong');
    const loi = tbTC.querySelector('p'); // gán loi trước
    loi.textContent = mess; // rồi mới gán text
    tbTC.style.display = 'block';
    tbTC.classList.add('show');
    setTimeout(() => tbTC.classList.remove('show'), 2000);
}

function ktraQuyen()
{
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
}

function xuLyLoc()
{
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
}

function adjustPageIfLastItem() {
    const btnCount = document.querySelectorAll(".btn-sua").length;
    if (btnCount === 1 && currentPage > 1) {
        currentPage -= 1;
    }
}

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

function luuSanPham()
{
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
            setTimeout(() => tBquyen.classList.remove('show'), 2000);
            return; 
        }
    
        // Kiểm tra dữ liệu
        if(!name)
        {
            document.getElementById('txtTen').focus();
            return showError("Không được để trống tên sản phẩm");
        }
        if(!description)
        {
            document.getElementById('txtMota').focus();
            return showError("Không được để trống mô tả sản phẩm");
        }
        if(!category_id)
        {
            document.getElementById('cbLoai').focus();
            return showError("Không được để trống loại sản phẩm");
        }
        if(!price)
        {
            document.getElementById('txtGia').focus();
            return showError("Không được để trống giá nhập");
        }
        const epPrice = parseFloat(price);
        if(epPrice < 0 || epPrice === 0 || isNaN(price))
        {
            document.getElementById('txtGia').focus();
            return showError("Giá phải là số dương");
        }
        if(!ptgg)
        {
            document.getElementById('txtPT').focus();
            return showError("Không được để trống phần trăm tăng giá");
        }
        const epPtgg = parseFloat(ptgg);
        if(epPtgg < 0 || epPtgg === 0 || isNaN(ptgg))
            {
                document.getElementById('txtPT').focus();
                return showError("Phần trăm tăng giá phải là số dương");
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
                const modalElement = document.getElementById('modalNhapSanPham');
const modalInstance = bootstrap.Modal.getInstance(modalElement);
if (modalInstance) {
    modalInstance.hide();
}

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
}
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
    <select name="products[${index}][product_id]" class="form-select">
      ${productOptions}
    </select>
  </div>

<div class="col-md-3 d-flex flex-column justify-content-end">
  <label class="form-label">Hình ảnh</label>
  <input type="file" name="products[${index}][image]" class="form-control previewable mb-2" accept="image/*">
  <img src="" alt="preview" class="img-thumbnail preview-img d-none mt-auto" style="height: 80px; width: 80px; object-fit: contain;">
</div>





  <!-- Màu -->
  <div class="col-md-2">
    <label class="form-label">Màu</label>
    <select name="products[${index}][color_id]" class="form-select">
      ${colorOptions}
    </select>
  </div>

  <!-- Size -->
  <div class="col-md-2">
    <label class="form-label">Size</label>
    <select name="products[${index}][size_id]" class="form-select">
      ${sizeOptions}
    </select>
  </div>

  <!-- Số lượng -->
  <div class="col-md-2">
    <label class="form-label">Số lượng</label>
    <input type="number" name="products[${index}][quantity]" class="form-control" min="1">
  </div>

  <!-- Xoá -->
  <div class="col-md-1 d-flex align-items-end">
    <button type="button" class="btn btn-danger btn-remove-form w-100">Xoá</button>
  </div>
</div>

    `;
}
function xuLyThemPhieuNhap()
{
    document.getElementById('create_pn').addEventListener('click', function () {
        const modal = new bootstrap.Modal(document.getElementById('modalCreatePN'));
        modal.show();
    });
    document.getElementById('btnThemSanPham').addEventListener('click', function () {
        if (!permissions.includes('write')) {
            const tBquyen = document.querySelector('.thongBaoQuyen');
            tBquyen.style.display = 'block';
            tBquyen.classList.add('show');
            setTimeout(() => tBquyen.classList.remove('show'), 2000);
            return; 
        }
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
}
function guiFormPhieuNhap()
{
    document.getElementById('formNhapPhieuNhap').addEventListener('submit', function (e) {
        e.preventDefault();
    
        const supplier_id = document.getElementById('supplier_id').value;
        const user_id = document.getElementById('user_id').value;
    
        const formData = new FormData();
        formData.append('supplier_id', supplier_id);
        formData.append('user_id', user_id);
    
        const productBlocks = document.querySelectorAll('#dynamic-product-forms .row');
        const productList = [];
        let isValid = true; // kiểm tra dữ liệu đầu vào có hợp lệ hay không

        productBlocks.forEach((block, index) => {
            const product_id = block.querySelector(`[name^="products"][name*="[product_id]"]`)?.value;
            const color_id = block.querySelector(`[name^="products"][name*="[color_id]"]`)?.value;
            const size_id = block.querySelector(`[name^="products"][name*="[size_id]"]`)?.value;
            const quantity = block.querySelector(`[name^="products"][name*="[quantity]"]`)?.value;
            const imageInput = block.querySelector(`[name^="products"][name*="[image]"]`);
            const image = imageInput?.files[0];

            if (!product_id || !color_id || !size_id || !quantity || !image)
            {
                isValid = false;
                return;
            }

                        // ✅ Kiểm tra định dạng file ảnh
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                        if (!allowedTypes.includes(image.type)) {
                            isValid = false;
                            alert(`Ảnh sản phẩm ${index + 1} phải là file JPG hoặc PNG!`);
                            return;
                        }
    
            productList.push({
                product_id,
                color_id,
                size_id,
                quantity,
                image_name: image.name
            });
    
            formData.append('images[]', image, image.name);
        });
    
        if (!isValid || productList.length === 0) {
            const TBsp = document.querySelector('.thongbaoLuuKhongThanhCong');
            TBsp.style.display = 'block';
            TBsp.classList.add('show');
            setTimeout(() => TBsp.classList.remove('show'), 2000);            
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
                const tbtcthem = document.querySelector('.thongbaoLuuThanhCong');
                tbtcthem.style.display = 'block';
                tbtcthem.classList.add('show');
                setTimeout(() => tbtcthem.classList.remove('show'), 2000);  
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
}
function resetForm()
{
    document.getElementById('resetFormProduct').addEventListener('click', function () {
        document.getElementById('dynamic-product-forms').innerHTML = '';
    });
}

function xuLyAnh()
{
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
}
function loadPhieuNhap(page = 1) {
    const formData = new FormData(formLoc);
    formData.append("pageproduct", page);
    
    fetch(`./ajax/quanlyPhieuNhap_ajax.php`, {
        method: "POST",
        body: formData
    })
    .then(res => res.text()) // CHUYỂN .text() để debug lỗi
    .then(text => {
        const data = JSON.parse(text); // Parse JSON sau khi kiểm tra text
        document.getElementById('product-list').innerHTML = data.products;
        document.getElementById('pagination').innerHTML = data.pagination;

        phantrang();
        xacNhanCho();
        xemChiTiet();
        xoaPhieuNhap();
        suaPhieuNhap();
    })
    .catch(error => {
        console.error('Lỗi khi tải phiếu nhập:', error);
    });
}


function phantrang()
{
            // Gán lại sự kiện cho nút chuyển trang
            document.querySelectorAll(".page-link-custom").forEach(btn => {
                btn.addEventListener("click", function (e) {
                    e.preventDefault();
                    currentPage = parseInt(this.dataset.page); // lưu lại trang hiện tại
                     loadPhieuNhap(this.dataset.page);
    
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
}
function xacNhanCho()
{
                // Khi người dùng ấn nút Hủy
                document.getElementById('btnHuy').addEventListener('click', function () {
                    document.getElementById('xacNhanCho').style.display = 'none';
                    document.querySelector('.overlay').style.display = 'none';
    
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
}
function xemChiTiet()
{
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
}

// Xóa phiếu nhập + chi tiết phiếu nhập
function xoaPhieuNhap(){};
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-xoa')) {
        const idpn = e.target.dataset.idpn;
        document.getElementById('btnXacNhanXoaPN').dataset.idpn = idpn;

        fetch(`./ajax/getCTPhieuNhap.php?idpn=${idpn}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const tbody = document.getElementById('body-xoa-ctpn');
                tbody.innerHTML = '';

                data.details.forEach(item => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="text-center">${item.importreceipt_details_id}</td>
                        <td class="text-center">${item.product_id}</td>
                        <td class="text-center">${item.variant_id}</td>
                        <td class="text-center">${item.quantity}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-danger btn-xoa-ctpn" data-idctpn="${item.importreceipt_details_id}">Xóa</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });

                const anhien = document.getElementById('anhienxoa');
                if (data.details.length === 0) {
                    anhien.style.display = 'inline-block';
                } else {
                    anhien.style.display = 'none';
                }

                const modal = new bootstrap.Modal(document.getElementById('modalXoaChiTietPN'));
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
document.body.classList.remove('modal-open');
document.body.style = '';
                modal.show();
            }
        });
    }

    if (e.target.classList.contains('btn-xoa-ctpn')) {
        const idctpn = e.target.dataset.idctpn;
        showConfirmBox('Bạn có chắc chắn muốn xóa chi tiết này?', function() {
            fetch('./ajax/deleteCTphieunhap.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `idctpn=${idctpn}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    e.target.closest('tr').remove();
                    loadPhieuNhap(currentPage);
                    const tbXoaTC = document.querySelector('.thongbaoXoaThanhCong');
                    tbXoaTC.style.display = 'block';
                    tbXoaTC.classList.add('show');
                    setTimeout(() => tbXoaTC.classList.remove('show'), 2000);

                    const remainingRows = document.querySelectorAll('#body-xoa-ctpn tr').length;
                    const anhien = document.getElementById('anhienxoa');
                    if (remainingRows === 0) {
                        anhien.style.display = 'inline-block';
                    }
                } else {
                    const tbXoaTB = document.querySelector('.thongbaoXoaKhongThanhCong');
                    tbXoaTB.style.display = 'block';
                    tbXoaTB.classList.add('show');
                    setTimeout(() => tbXoaTB.classList.remove('show'), 2000);                
                }
            });
        });
    }

    if (e.target.id === 'btnXacNhanXoaPN') {
        const idpn = e.target.dataset.idpn;
        showConfirmBox('Bạn có chắc chắn muốn xóa Phiếu nhập?', function() {
            fetch('./ajax/deletePhieuNhap.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id=${idpn}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const tbXoaTC = document.querySelector('.thongbaoXoaPNthanhcong');
                    tbXoaTC.style.display = 'block';
                    tbXoaTC.classList.add('show');
                    setTimeout(() => tbXoaTC.classList.remove('show'), 2000);

                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalXoaChiTietPN'));
                    modal.hide();
                    loadPhieuNhap(currentPage);
                } else {
                    const tbXoaTC = document.querySelector('.thongbaoXoaPNKhongThanhCong');
                    tbXoaTC.style.display = 'block';
                    tbXoaTC.classList.add('show');
                    setTimeout(() => tbXoaTC.classList.remove('show'), 2000);                }
            });
        });
    }
});

// Hàm hiển thị Confirm Box
function showConfirmBox(message, callback) {
    const thongBao = document.querySelector('.thongBaoXoa');
    const overlay = document.querySelector('.overlay');

    thongBao.querySelector('p').textContent = message;
    thongBao.style.display = 'block';
    overlay.style.display = 'block';

    // Lưu hành động callback
    deleteAction = callback;
}

// Bấm nút "Có"
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-confirm-yes')) {
        if (!permissions.includes('delete')) {
            const tBquyen = document.querySelector('.thongBaoQuyen');
            tBquyen.style.display = 'block';
            tBquyen.classList.add('show');
            document.querySelector(".overlay").style.display = "none";
            document.querySelector('.thongBaoXoa').style.display = "none";
            setTimeout(() => tBquyen.classList.remove('show'), 2000);
            return; 
        }
        if (deleteAction) {
            deleteAction();
            deleteAction = null; // clear sau khi thực hiện
        }
        closeConfirmBox();
    }
});

// Bấm nút "Không"
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-confirm-no')) {
        closeConfirmBox();
    }
});

// Đóng popup
function closeConfirmBox() {
    document.querySelector('.thongBaoXoa').style.display = 'none';
    document.querySelector('.overlay').style.display = 'none';
}



document.getElementById('modalXoaChiTietPN').addEventListener('hidden.bs.modal', function () {
    // Loại bỏ backdrop nếu có
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
    document.body.style = '';
});

function suaPhieuNhap(){};
// Sửa phiếu nhập
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('btn-sua')) {
        const idpn = e.target.dataset.idpn;
        const idnv = e.target.dataset.idnv;
        const idncc = e.target.dataset.idncc;
        const gia = e.target.dataset.gia;
        const ngaylap = e.target.dataset.ngaylap;
        const tennv = e.target.dataset.tennv;
        cachedQuantities = {};
        document.getElementById('txtMaPNsua').value = idpn;
        document.getElementById('user_idSuaPN').value = idnv;
        document.getElementById('supplier_idSuaPN').value = idncc;
        document.getElementById('txtTongGT').value = formatPrice(gia);
        document.getElementById('txtNgayLap').value = ngaylap;
        document.getElementById('user_Name').value = tennv;

        fetchCTPhieuNhap(idpn,1);

        const modal = new bootstrap.Modal(document.getElementById('modalSuaPhieuNhap'));
        // Trước khi show modal: XÓA modal backdrop cũ
document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
document.body.classList.remove('modal-open');
document.body.style = '';

        modal.show();
    }
});

// Hàm cập nhật chi tiết phiếu nhập

document.getElementById('btn_sua_pn').addEventListener('click', function () {
    const formElement = document.getElementById('formSuaPN');
    const formData = new FormData(formElement);

    // 🔥 Bổ sung thêm chi tiết sản phẩm từ bảng (table nằm ngoài form)
    const detailIds = document.querySelectorAll('#tableChiTietPhieuNhap [name="detail_ids[]"]');
    const productIds = document.querySelectorAll('#tableChiTietPhieuNhap [name="product_ids[]"]');
    const variantIds = document.querySelectorAll('#tableChiTietPhieuNhap [name="variant_ids[]"]');
    const quantities = document.querySelectorAll('#tableChiTietPhieuNhap [name="quantities[]"]');

    detailIds.forEach(input => {
        formData.append('detail_ids[]', input.value);
    });
    productIds.forEach(input => {
        formData.append('product_ids[]', input.value);
    });
    variantIds.forEach(input => {
        formData.append('variant_ids[]', input.value);
    });
    quantities.forEach(input => {
        formData.append('quantities[]', input.value);
    });

    // Tiến hành fetch
    fetch('./ajax/updateCTPhieuNhap.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        console.log("DEBUG SERVER:", data); // Xem phản hồi

        if (data.success) {
            const tbTC = document.querySelector('.thongbaoUpdateThanhCong');
            tbTC.style.display = 'block';
            tbTC.classList.add('show');
            setTimeout(() => tbTC.classList.remove('show'), 2000);

            const modal = bootstrap.Modal.getInstance(document.getElementById('modalSuaPhieuNhap'));
            modal.hide();
            loadPhieuNhap(currentPage);
        } else {
            const TBtb = document.querySelector('.thongbaoUpdateKhongThanhCong');
            TBtb.style.display = 'block';
            TBtb.classList.add('show');
            setTimeout(() => TBtb.classList.remove('show'), 2000);
        }
    })
    .catch(err => {
        console.error('Lỗi kết nối:', err);
        alert('Lỗi máy chủ.');
    });
});




document.addEventListener('input', function(e) {
if (e.target.classList.contains('quantity')) {
    const row = e.target.closest('tr');
    const variantIdInput = row.querySelector('.variant_id');
    if (variantIdInput) {
        const variantId = variantIdInput.value;
        cachedQuantities[variantId] = e.target.value;
    }
}
});

function fetchCTPhieuNhap(idpn, page = 1) {
    const tbody = document.querySelector('#tableChiTietPhieuNhap tbody');
    const paginationWrap = document.getElementById("pagination-sua-phieunhap");

    if (!tbody || !paginationWrap) return;

    tbody.innerHTML = '<tr><td colspan="3" class="text-center">Đang tải...</td></tr>';
    paginationWrap.innerHTML = '';

    fetch(`./ajax/getCTPhieuNhap.php?idpn=${idpn}&page=${page}`)
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Không lấy được dữ liệu</td></tr>';
                return;
            }

            tbody.innerHTML = '';
            const currentPage = data.pagination?.current || 1;
            const totalPages = data.pagination?.total || 1;

            data.details.forEach(item => {
                const tr = document.createElement('tr');
                const quantityValue = cachedQuantities[item.variant_id] ?? item.quantity;
                tr.innerHTML = `
<td>
  <input type="hidden" name="detail_ids[]" class="detail_id" value="${item.importreceipt_details_id}">
  <input type="text" name="product_ids[]" class="form-control bg-light product_id" value="${item.product_id}" readonly>
</td>
<td>
  <input type="text" name="variant_ids[]" class="form-control bg-light variant_id" value="${item.variant_id}" readonly>
</td>
<td>
  <input type="number" name="quantities[]" class="form-control quantity" value="${quantityValue}" min="1" required>
</td>`;
                tbody.appendChild(tr);
            });

            renderPaginationSuaPhieuNhap(idpn, currentPage, totalPages);
        })
        .catch(err => {
            console.error('Lỗi fetch chi tiết phiếu nhập:', err);
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Lỗi kết nối máy chủ</td></tr>';
        });
}

function renderPaginationSuaPhieuNhap(idpn, currentPage, totalPages) {
const paginationWrap = document.getElementById("pagination-sua-phieunhap");
if (!paginationWrap) return;
paginationWrap.innerHTML = '';
if (totalPages <= 1) {
    // ✨ Nếu chỉ có 1 trang thì không render nút phân trang luôn
    return;
}
const btnPrev = document.createElement("button");
btnPrev.innerHTML = '<i class="fa fa-chevron-left text-dark"></i>';
btnPrev.className = "btn btn-outline-secondary";
btnPrev.disabled = currentPage === 1;

const inputPage = document.createElement("input");
inputPage.type = "number";
inputPage.min = 1;
inputPage.max = totalPages;
inputPage.value = currentPage;
inputPage.style.width = "60px";
inputPage.className = "form-control d-inline-block text-center mx-2";

const spanTotal = document.createElement("span");
spanTotal.innerHTML = `/ ${totalPages}`;
spanTotal.classList.add("me-2");

const btnNext = document.createElement("button");
btnNext.innerHTML = '<i class="fa fa-chevron-right text-dark"></i>';
btnNext.className = "btn btn-outline-secondary";
btnNext.disabled = currentPage === totalPages;

// Sửa ở đây nè:
btnPrev.onclick = () => {
    let newPage = parseInt(inputPage.value) - 1;
    if (newPage < 1) newPage = 1;
    fetchCTPhieuNhap(idpn, newPage);
};

btnNext.onclick = () => {
    let newPage = parseInt(inputPage.value) + 1;
    if (newPage > totalPages) newPage = totalPages;
    fetchCTPhieuNhap(idpn, newPage);
};

inputPage.addEventListener("keypress", function (e) {
    if (e.key === "Enter") {
        let value = parseInt(this.value);
        if (isNaN(value)) return;
        if (value < 1) value = 1;
        if (value > totalPages) value = totalPages;
        fetchCTPhieuNhap(idpn, value);
    }
});

paginationWrap.appendChild(btnPrev);
paginationWrap.appendChild(inputPage);
paginationWrap.appendChild(spanTotal);
paginationWrap.appendChild(btnNext);
}

document.getElementById('modalSuaPhieuNhap').addEventListener('hidden.bs.modal', function () {
    // Loại bỏ backdrop nếu có
    cachedQuantities = {};
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
    document.body.style = '';
});
