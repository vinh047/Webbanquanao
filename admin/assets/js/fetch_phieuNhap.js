let currentPage = 1;
let variantIndex = 0;
const formLoc = document.getElementById("formLoc");
const permissionsElement = document.getElementById('permissions');
let permissions = [];
const formatPrice = price => Number(price).toLocaleString('vi-VN');
let cachedQuantities = {};
let deleteAction = null;
// console.log('✅ productListFromPHP:', productListFromPHP);

document.addEventListener('DOMContentLoaded', function () {
    ktraQuyen();
    xuLyLoc();
    luuSanPham();
    xuLyThemPhieuNhap();
    guiFormPhieuNhap();
    resetForm();
    xuLyAnh();
    loadPhieuNhap();
    loadFiltersFromURL();
        const btnThemSanPhamMoi = document.getElementById('btnThemSanPhamMoi');
        if (btnThemSanPhamMoi) {
            btnThemSanPhamMoi.addEventListener('click', function () {
                const modalElement = document.getElementById('modalNhapSanPham');
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            });
        }
        cleanModalBackdropChonBienThe();
    resetFormLoc();
});

function resetFormLoc()
{
        document.getElementById('formLoc').addEventListener('reset', function () {
        setTimeout(() => {
            $('#txtIDncc').val('').trigger('change'); // reset Select2 nhà cung cấp
            $('#txtIDnv').val('').trigger('change');  // nếu nhân viên cũng dùng select2
        }, 0);
    });
}
function tbThanhCong(mess)
{
    const tbTC = document.querySelector('.thongbaoXoaThanhCong');
    const loi = tbTC.querySelector('p');
    loi.textContent = mess;
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
function loadFiltersFromURL() {
    const params = new URLSearchParams(window.location.search);
    for (let [key, value] of params.entries()) {
        const el = document.querySelector(`[name="${key}"]`);
        if (el) {
            if (el.tagName === 'SELECT' || el.tagName === 'INPUT') {
                el.value = value;
                if ($(el).hasClass('select2')) {
                    $(el).val(value).trigger('change');
                }
            }
        }
    }

    const pageadmin = parseInt(params.get('pageadmin')) || 1;
    currentPage = pageadmin;
    loadPhieuNhap(currentPage);
}

function xuLyLoc()
{
    formLoc.addEventListener("submit", function (e) {
        e.preventDefault();
        currentPage = 1;
    
        const formData = new FormData(formLoc);
        const filters = [];
    
        // 👉 Tạo mảng các filter, bỏ trống thì không thêm
        for (let [key, value] of formData.entries()) {
            if (value) {
                filters.push(`${encodeURIComponent(key)}=${encodeURIComponent(value)}`);
            }
        }
    
        // 👉 Ghép thủ công theo đúng thứ tự bạn muốn
        const queryParts = [
            'page=phieunhap',
            ...filters,
            `pageadmin=${currentPage}`
        ];
    
        const newUrl = `${location.pathname}?${queryParts.join('&')}`;
        window.history.pushState({}, '', newUrl);
    
        loadPhieuNhap(currentPage);
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

    $(document).ready(function () {
        $('#txtIDncc').select2({
            dropdownParent: $('.filter-loc'), // đặt parent là vùng lọc để tránh bị che
            width: '100%'
        });
                $('#txtIDnv').select2({
            dropdownParent: $('.filter-loc'), // đặt parent là vùng lọc để tránh bị che
            width: '100%'
        });
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
document.getElementById('btnMoModalBienThe').addEventListener('click', () => {
    const modal = new bootstrap.Modal(document.getElementById('modalThemBienThe'));
    modal.show();
    $('#modalThemBienThe').on('shown.bs.modal', function () {
        $('#id_sanpham').select2({
            width: '100%',
            dropdownParent: $('#modalThemBienThe')
        });
    });
  });
  
  function createVariantRow(index) {
    const sizeOptions = generateOptions(sizeListFromPHP, 'size_id', 'name');
    const colorOptions = generateOptions(colorListFromPHP, 'color_id', 'name');
  
    return `
      <div class="variant-row border rounded p-3 my-1 bg-light">
        <div class="row g-3 align-items-end"> <!-- ✅ align-items-end giúp canh đều đáy -->
  
          <!-- CỘT ẢNH: CHIA LẠI LAYOUT -->
<div class="col-md-3 pb-0"> <!-- ✅ có sẵn pb-0 -->
  <label class="form-label mb-1">Chọn ảnh</label>
  <div class="d-flex align-items-center gap-2">
    <input type="file" name="images[]" accept="image/*" class="form-control previewable" style="width: 70%;">
    <img class="preview-img d-none img-thumbnail" style="height: 50px; width: 50px; object-fit: contain;">
  </div>
</div>

  
          <!-- CỘT MÀU -->
          <div class="col-md-3">
            <label class="form-label mb-1">Màu sắc</label>
            <select name="colors[]" class="form-select select2">
              ${colorOptions}
            </select>
          </div>
  
          <!-- CỘT SIZE -->
          <div class="col-md-3 me-auto">
            <label class="form-label mb-1">Kích thước</label>
            <select name="sizes[]" class="form-select select2">
              ${sizeOptions}
            </select>
          </div>
  
          <!-- CỘT NÚT -->
          <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-outline-danger btn-remove-variant w-100">
              <i class="fa fa-trash me-1"></i> Xóa dòng
            </button>
          </div>
  
        </div>
      </div>
    `;
  }
  
  
  
  // Thêm dòng khi nhấn btn
document.getElementById('btnAddVariantRow').addEventListener('click', () => {
    if (!permissions.includes('write')) {
        const tBquyen = document.querySelector('.thongBaoQuyen');
        tBquyen.style.display = 'block';
        tBquyen.classList.add('show');
        setTimeout(() => tBquyen.classList.remove('show'), 2000);
        return; 
    }
    const container = document.getElementById('variant-container');
    const wrapper = document.createElement('div');
    wrapper.innerHTML = createVariantRow(variantIndex++);
    container.appendChild(wrapper);
    wrapper.querySelectorAll('.select2').forEach(el => $(el).select2({ dropdownParent: $('#modalThemBienThe'), width: '100%' }));
  });
  
  // Xóa dòng
  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-remove-variant')) {
      e.target.closest('.variant-row').remove();
    }
  });
  
  
  // Preview ảnh
  document.addEventListener('change', function (e) {
    if (e.target.classList.contains('previewable')) {
      const file = e.target.files[0];
      const preview = e.target.closest('.d-flex').querySelector('.preview-img');
  
      if (file) {
        const reader = new FileReader();
        reader.onload = function (event) {
          preview.src = event.target.result;
          preview.classList.remove('d-none');
  
          // ✅ Thêm padding cho các cột còn lại
          const row = e.target.closest('.row');
  
          // Chọn cột màu, size, xóa (trừ ảnh)
          const otherCols = row.querySelectorAll('.col-md-3:not(:first-child), .col-md-2');
          otherCols.forEach(col => {
            col.classList.add('pb-2'); // hoặc pb-3 nếu bạn muốn đẩy xuống
          });
        };
        reader.readAsDataURL(file);
      }
    }
  });
  
  
  function showModalThongBao(message) {
    const modalBody = document.querySelector('#modalThongBao .modal-body');
    modalBody.textContent = message;
    const modal = new bootstrap.Modal(document.getElementById('modalThongBao'));
    modal.show();
  }
  
  // Gửi dữ liệu
  document.getElementById('formBienThe').addEventListener('submit', function (e) {
    e.preventDefault();

    const productId = document.getElementById('id_sanpham').value;
    const rows = document.querySelectorAll('.variant-row');

    if (rows.length === 0) {
        return showError("Lưu biến thể thất bại");
    }

    const variantKeys = new Set();
    let isValid = true;
    let firstErrorRow = null;
    const errorRows = [];

    for (let row of rows) {
        const color = row.querySelector('[name="colors[]"]').value;
        const size = row.querySelector('[name="sizes[]"]').value;
        const fileInput = row.querySelector('[name="images[]"]');
        const file = fileInput?.files?.[0];

        // ❌ Thiếu thông tin
        if (!color || !size || !file) {
            isValid = false;
            if (!firstErrorRow) firstErrorRow = row;
            errorRows.push(row);
            continue;
        }

        const filename = file.name.trim().toLowerCase();
        const key = `${productId}_${color}_${size}_${filename}`;

        // ❌ Trùng hàng đợi
        if (variantKeys.has(key)) {
            Swal.fire({
                icon: 'warning',
                title: 'Biến thể trùng',
                text: 'Biến thể (màu, size, ảnh) đã tồn tại trong hàng đợi.',
                confirmButtonText: 'OK'
            });
            return;
        }

        variantKeys.add(key);
    }

    if (!isValid) {
        Swal.fire({
            icon: 'warning',
            title: 'Thiếu thông tin',
            text: 'Vui lòng nhập đầy đủ màu, size và ảnh cho tất cả các dòng.',
            confirmButtonText: 'OK',
            didClose: () => {
                firstErrorRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                errorRows.forEach(row => {
                    row.classList.add('row-error-highlight');
                    setTimeout(() => {
                        row.classList.remove('row-error-highlight');
                    }, 3000);
                });
            }
        });
        return;
    }

    // ✅ Gửi lên server
    const formData = new FormData(this);
    fetch('./ajax/insertBienThe.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            const tbTC = document.querySelector('.thongbaoThemBTThanhCong');
            tbTC.style.display = 'block';
            tbTC.classList.add('show');
            setTimeout(() => tbTC.classList.remove('show'), 2000);
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalThemBienThe'));
            if (modal) modal.hide();

            // Xoá tất cả dòng
            document.querySelectorAll('.variant-row').forEach(row => row.remove());

            // Reset sản phẩm
            const selectSanPham = document.getElementById('id_sanpham');
            if (selectSanPham) {
                $(selectSanPham).val(null).trigger('change');
            }

            // Cập nhật lại biến thể ở các dòng phiếu nhập
            document.querySelectorAll('.product-row').forEach(row => {
                const selectProduct = row.querySelector('[name*="[product_id]"]');
                if (selectProduct && selectProduct.value == productId) {
                    handleProductChange(selectProduct, row);
                }
            });

            if (typeof reloadVariantsInPhieuNhap === 'function') reloadVariantsInPhieuNhap();
        } else {
            showModalThongBao(res.message || 'Đã tồn tại biến thể trong hệ thống.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Lỗi khi gửi dữ liệu!');
    });
});

  
  document.querySelector('#modalThemBienThe .btn-danger').addEventListener('click', function () {
    // 1. Reset select sản phẩm
    $('#id_sanpham').val(null).trigger('change');
  
    // 2. Xóa toàn bộ dòng biến thể
    document.querySelectorAll('#variant-container .variant-row').forEach(row => row.remove());
  
    // 3. Reset lại form ảnh và inputs
    document.getElementById('formBienThe').reset();
  
    // // (Tuỳ chọn) 4. Tự thêm lại 1 dòng trống
    // const container = document.getElementById('variant-container');
    // const wrapper = document.createElement('div');
    // wrapper.innerHTML = createVariantRow(0); // hoặc variantIndex++ nếu bạn dùng biến đếm
    // container.appendChild(wrapper);
  
    // Kích hoạt lại Select2 trong dòng vừa thêm
    // wrapper.querySelectorAll('.select2').forEach(select => {
    //   $(select).select2({
    //     width: '100%',
    //     dropdownParent: $('#modalThemBienThe')
    //   });
    // });
  });
  

function luuSanPham()
{
    document.addEventListener('change', function (e) {
        if (e.target.matches('#variantImage')) {
          const file = e.target.files[0];
          const preview = document.getElementById('previewImage');
      
          if (file) {
            const reader = new FileReader();
            reader.onload = function (evt) {
              preview.src = evt.target.result;
              preview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
          } else {
            preview.src = '';
            preview.classList.add('d-none');
          }
        }
      });
      

    document.getElementById('btnLuuSanPham').addEventListener('click', function () {
        const name = document.getElementById('txtTen').value.trim();
        const description = document.getElementById('txtMota').value.trim();
        const category_id = document.getElementById('cbLoai').value;
        const ptgg = document.getElementById('txtPT').value.trim().replace('%', '');
        const regexCheck = /[`~+=\-\/;'\><\\|@#$%^&*()]/; 

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
            return showError("Tên không được để trống tên sản phẩm");
        }

        if(regexCheck.test(name))
            {
                document.getElementById('txtTen').focus();
                return showError("Tên không được chứa các ký tự đặc biệt");
            }

        if(!description)
        {
            document.getElementById('txtMota').focus();
            return showError("Không được để trống mô tả sản phẩm");
        }

        if(regexCheck.test(description))
            {
                document.getElementById('txtMota').focus();
                return showError("Mô tả không được chứa các ký tự đặc biệt");
            }
        if(!category_id)
        {
            document.getElementById('cbLoai').focus();
            return showError("Không được để trống loại sản phẩm");
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
            if(epPtgg > 100)
                {
                    document.getElementById('txtPT').focus();
                    return showError("Không được vướt mức 100%");
                }
        const data = new FormData();
        data.append('name', name);
        data.append('description', description);
        data.append('category_id', category_id);
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
                // 1. Hiện thông báo
                const TBsp = document.querySelector('.thongbaoThemSp');
                TBsp.style.display = 'block';
                TBsp.classList.add('show');
                setTimeout(() => TBsp.classList.remove('show'), 2000);
            
                // 2. Đóng modal
                const modalElement = document.getElementById('modalNhapSanPham');
                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                if (modalInstance) modalInstance.hide();
            
                // 3. Reset form
                document.getElementById('txtTen').value = '';
                document.getElementById('txtMota').value = '';
                document.getElementById('cbLoai').value = '';
                document.getElementById('txtPT').value = '30%';
            
                // ✅ 4. Cập nhật dropdown trong modal biến thể
                const selectBienThe = document.getElementById('id_sanpham');
                if (selectBienThe) {
                    const newOption = new Option(`${res.product_id} - ${res.name}`, res.product_id, false, false);
                    $('#id_sanpham').append(newOption).trigger('change.select2');
                }
            
                // ✅ 5. Cập nhật productListFromPHP để đồng bộ dropdown phiếu nhập
                productListFromPHP.push({
                    product_id: res.product_id,
                    name: res.name
                });
            
                // ✅ 6. Nếu cần, cũng gọi lại capNhatLaiDropdownTenSanPham để thêm vào dòng đang hiển thị
                capNhatLaiDropdownTenSanPham(res.product_id, res.name);
            }
             else {
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

function xuLyThemPhieuNhap()
{
    document.getElementById('create_pn').addEventListener('click', function () {
        const modal = new bootstrap.Modal(document.getElementById('modalCreatePN'));
        modal.show();
        $('#modalCreatePN').on('shown.bs.modal', function () {
            $('#supplier_id').select2({
                width: '100%',
                dropdownParent: $('#modalCreatePN')
            });
        });
        
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
    
        const temp = document.createElement('div');
        temp.innerHTML = html;
        const newRow = temp.firstElementChild;
    
        if (!newRow) {
            console.error('❌ Không tạo được newRow từ template HTML');
            return;
        }
    
        container.appendChild(newRow);
    
        // Khởi tạo Select2
        newRow.querySelectorAll('.select2').forEach(select => {
            $(select).select2({
                width: '100%',
                dropdownParent: $('#modalCreatePN')
            });
        });
    
        // ✅ Bây giờ mới được truy cập newRow
        let productSelect = newRow.querySelector('[name*="[product_id]"]');
        let variantSelect = newRow.querySelector('[name*="[variant_id]"]');
        
    
        if (!productSelect || !variantSelect) {
            console.error('❌ Không tìm thấy select trong newRow');
            return;
        }
    
        $(productSelect).on('select2:select', function () {
            console.log('🔥 select2:select gọi rồi nha');
            handleProductChange(this, newRow);
        });
        
    
        $(variantSelect).on('select2:select', function () {
            console.log('🔥 Biến thể đã chọn');
            handleVariantChange(this, newRow);
        });
        
    });
    
    
    
    // Xoá dòng sản phẩm
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-remove-form')) {
            e.target.closest('.row').remove();
        }
    });
    
}
document.addEventListener('click', async function (e) {
    if (e.target.classList.contains('btn-open-variant-modal')) {
        const index = e.target.dataset.index;
        const row = document.querySelectorAll('.product-row')[index];
        const productId = row.querySelector('[name*="[product_id]"]').value;

        if (!productId) {
            return showError("Vui lòng chọn sản phẩm trước");
        }

        window._currentVariantIndex = index;  // lưu lại index toàn cục
        window._currentProductId = productId;

        fetchVariantPage(1);
    }
});
function cleanModalBackdropChonBienThe() {
    const modal = document.getElementById('modalChonBienThe');
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function () {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style = '';
        });
    }
}

function fetchVariantPage(page = 1) {
    fetch(`./ajax/load_variants.php?product_id=${_currentProductId}&page=${page}`)
        .then(res => res.json())
        .then(json => {
            const tbody = document.querySelector('#variant-table tbody');
            const paginationWrap = document.getElementById("variant-pagination");

            tbody.innerHTML = json.data.map(v => `
                <tr class="text-center">
                  <td>${v.variant_id}</td>
                  <td><img src="../../assets/img/sanpham/${v.image}" class="img-thumbnail img-phongto" style="max-height:70px;"></td>
                  <td>${v.size_name}</td>
                  <td>${v.color_name}</td>
                  <td>${v.stock}</td>
                  <td>
                    <button class="btn btn-sm btn-success select-variant-btn" 
                            data-index="${_currentVariantIndex}"
                            data-variant='${JSON.stringify(v)}'>Chọn</button>
                  </td>
                </tr>
            `).join('');

            // Chỉ mở modal nếu chưa mở
            const modalEl = document.getElementById('modalChonBienThe');
            const existingModal = bootstrap.Modal.getInstance(modalEl);

            if (!existingModal) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            } else {
                modalEl.classList.contains('show') || existingModal.show(); // chỉ show nếu chưa mở
            }

            // Render phân trang như trước
            const { current, total } = json.pagination;
            paginationWrap.innerHTML = '';
            if (total > 1) {
                const btnPrev = document.createElement("button");
                btnPrev.innerHTML = '<i class="fa fa-chevron-left text-dark"></i>';
                btnPrev.className = "btn btn-outline-secondary";
                btnPrev.disabled = current === 1;
                btnPrev.onclick = () => fetchVariantPage(current - 1);

                const inputPage = document.createElement("input");
                inputPage.type = "number";
                inputPage.min = 1;
                inputPage.max = total;
                inputPage.value = current;
                inputPage.style.width = "60px";
                inputPage.className = "form-control d-inline-block text-center mx-2";
                inputPage.addEventListener("keypress", function (e) {
                    if (e.key === "Enter") {
                        let value = parseInt(this.value);
                        if (isNaN(value)) return;
                        if (value < 1) value = 1;
                        if (value > total) value = total;
                        fetchVariantPage(value);
                    }
                });

                const spanTotal = document.createElement("span");
                spanTotal.innerHTML = `/ ${total}`;
                spanTotal.classList.add("mx-1");

                const btnNext = document.createElement("button");
                btnNext.innerHTML = '<i class="fa fa-chevron-right text-dark"></i>';
                btnNext.className = "btn btn-outline-secondary";
                btnNext.disabled = current === total;
                btnNext.onclick = () => fetchVariantPage(current + 1);

                paginationWrap.appendChild(btnPrev);
                paginationWrap.appendChild(inputPage);
                paginationWrap.appendChild(spanTotal);
                paginationWrap.appendChild(btnNext);
            }
        })
        .catch(err => console.error("❌ Lỗi load biến thể:", err));
}


document.addEventListener('click', function (e) {
    if (e.target.classList.contains('select-variant-btn')) {
        const data = JSON.parse(e.target.dataset.variant);
        const index = e.target.dataset.index;
        const row = document.querySelectorAll('.product-row')[index];

        // Gán ID biến thể
        row.querySelector('.variant-id-input').value = data.variant_id;

        // ✅ Gán hiển thị mã biến thể ra thẻ <p>
        const pDisplay = row.querySelector('.btn-open-variant-modal').nextElementSibling;
        if (pDisplay) {
            pDisplay.textContent = `Mã: ${data.variant_id}`;
            pDisplay.classList.add('text-muted', 'small');
        }

        // Gán size
        const sizeSelect = row.querySelector('[name*="[size_id]"]');
        sizeSelect.innerHTML = `<option value="${data.size_id}" selected>${data.size_name}</option>`;

        // Gán màu
        const colorSelect = row.querySelector('[name*="[color_id]"]');
        colorSelect.innerHTML = `<option value="${data.color_id}" selected>${data.color_name}</option>`;

        bootstrap.Modal.getInstance(document.getElementById('modalChonBienThe')).hide();
    }
});


function generateProductForm(index) {
    const productOptions = generateOptions(productListFromPHP, 'product_id', 'name');
    const sizeOptions = generateOptions(sizeListFromPHP, 'size_id', 'name');
    const colorOptions = generateOptions(colorListFromPHP, 'color_id', 'name');

    return `
<div class="row g-3 align-items-end my-1 bg-white border rounded shadow-sm p-3 product-row">
  <div class="col-md-3">
    <label class="form-label">Tên sản phẩm</label>
    <select name="products[${index}][product_id]" class="form-control select2">
      ${productOptions}
    </select>
  </div>

  <div class="col-md-2">
    <label class="form-label">Giá nhập</label>
    <input type="text" name="products[${index}][unit_price]" class="form-control input-unit-price" placeholder="VD: 100000">
  </div>

<div class="col-md-2">
  <label class="form-label">Chọn biến thể</label>
  <div class="d-flex align-items-center gap-2">
    <input type="hidden" name="products[${index}][variant_id]" class="variant-id-input">
    <button type="button" class="btn btn-outline-primary btn-sm btn-open-variant-modal" data-index="${index}">
      Chọn
    </button>
<p class="mb-1 variant-display"></p>
  </div>
</div>


  <div class="col-md-2">
    <label class="form-label">Màu</label>
    <select name="products[${index}][color_id]" class="form-control select2">
      ${colorOptions}
    </select>
  </div>

  <div class="col-md-2">
    <label class="form-label">Size</label>
    <select name="products[${index}][size_id]" class="form-control select2">
      ${sizeOptions}
    </select>
  </div>

  <div class="col-md-1">
    <label class="form-label">SL</label>
    <input type="number" name="products[${index}][quantity]" class="form-control" min="1" placeholder="999">
  </div>

  <div class="col-12 text-end pt-2">
    <button type="button" class="btn btn-outline-danger btn-sm btn-remove-form">
      <i class="fa fa-trash me-1"></i> Xoá dòng này
    </button>
  </div>
</div>`;
}


function formatCurrency(value) {
    return parseFloat(value).toLocaleString('vi-VN');
}


async function handleProductChange(selectEl, row) {
    const productId = selectEl.value;
    const unitPriceInput = row.querySelector('.input-unit-price');

    if (!productId) return;

    try {
        const resPrice = await fetch(`./ajax/get_product_price.php?product_id=${productId}`);
        const dataPrice = await resPrice.json();
        unitPriceInput.value = dataPrice.unit_price ? formatCurrency(dataPrice.unit_price) : '';
    } catch (err) {
        console.error("Lỗi khi xử lý sản phẩm:", err);
    }
}

async function handleVariantChange(selectEl, row) {
    const variantId = selectEl.value;
    const productId = row.querySelector(`[name*="[product_id]"]`)?.value;
    if (!variantId || !productId) return;

    const dropdownParent = $('#modalCreatePN');

    try {
        const res = await fetch(`./ajax/get_variant_detail.php?variant_id=${variantId}&product_id=${productId}`);
        const data = await res.json();

        // console.log("📦 Biến thể chi tiết nhận được:", data);

        // ✅ Cập nhật duy nhất 1 màu
        const colorSelect = row.querySelector(`[name*="[color_id]"]`);
        const colorName = colorListFromPHP.find(c => c.color_id == data.color_id)?.name || `Màu ${data.color_id}`;
        colorSelect.innerHTML = `<option value="${data.color_id}" selected>${data.color_id} - ${colorName}</option>`;
        $(colorSelect).select2({ width: '100%', dropdownParent });

        // ✅ Cập nhật duy nhất 1 size
        const sizeSelect = row.querySelector(`[name*="[size_id]"]`);
        const sizeName = sizeListFromPHP.find(s => s.size_id == data.size_id)?.name || `Size ${data.size_id}`;
        sizeSelect.innerHTML = `<option value="${data.size_id}" selected>${data.size_id} - ${sizeName}</option>`;
        $(sizeSelect).select2({ width: '100%', dropdownParent });

    } catch (err) {
        console.error("❌ Lỗi khi xử lý variant:", err);
    }
}


function guiFormPhieuNhap() {
    document.getElementById('formNhapPhieuNhap').addEventListener('submit', function (e) {
        e.preventDefault();

        const supplier_id = document.getElementById('supplier_id').value;
        const user_id = document.getElementById('user_id').value;

        const formData = new FormData();
        formData.append('supplier_id', supplier_id);
        formData.append('user_id', user_id);

        const productBlocks = document.querySelectorAll('#dynamic-product-forms .row');
        const productList = [];
        let isValid = true;
        let errorType = ''; // 'missing' | 'variant' | 'price'
        let errorRows = [];
        let firstErrorRow = null;

        productBlocks.forEach((block) => {
            const product_id = block.querySelector(`[name^="products"][name*="[product_id]"]`)?.value;
const raw_price = block.querySelector(`[name^="products"][name*="[unit_price]"]`)?.value || '';
const unit_price = raw_price.replace(/\./g, '').replace(',', '.');

// ❌ Giá nhập không hợp lệ (NaN hoặc < 0)
if (!unit_price || isNaN(unit_price) || Number(unit_price) <= 0) {
    isValid = false;
    if (!firstErrorRow) {
        errorType = 'invalid_price';
        firstErrorRow = block;
    }
    errorRows.push(block);
    return;
}

            const color_id = block.querySelector(`[name^="products"][name*="[color_id]"]`)?.value;
            const size_id = block.querySelector(`[name^="products"][name*="[size_id]"]`)?.value;
            const quantity = block.querySelector(`[name^="products"][name*="[quantity]"]`)?.value;

// ❌ Số lượng không hợp lệ (không phải số hoặc <= 0)
if (!quantity || isNaN(quantity) || Number(quantity) <= 0) {
    isValid = false;
    if (!firstErrorRow) {
        errorType = 'invalid_quantity';
        firstErrorRow = block;
    }
    errorRows.push(block);
    return;
}

            const variantDisplay = block.querySelector('.variant-display');

            // ❌ Thiếu trường bắt buộc
            if (!product_id || !color_id || !size_id || !quantity || !unit_price) {
                isValid = false;
                if (!firstErrorRow) {
                    errorType = 'missing';
                    firstErrorRow = block;
                }
                errorRows.push(block);
                return;
            }

            // ❌ Thiếu biến thể
            if (!variantDisplay || !variantDisplay.textContent.trim()) {
                isValid = false;
                if (!firstErrorRow) {
                    errorType = 'variant';
                    firstErrorRow = block;
                }
                errorRows.push(block);
                return;
            }

            // ❌ Trùng mã sản phẩm nhưng giá khác nhau
            const existing = productList.find(p =>
                p.product_id === product_id &&
                p.unit_price !== unit_price
            );
            if (existing) {
                isValid = false;
                if (!firstErrorRow) {
                    errorType = 'price';
                    firstErrorRow = block;
                }
                errorRows.push(block);
                return;
            }

            // ✅ Dòng hợp lệ
            productList.push({
                product_id,
                color_id,
                size_id,
                quantity,
                unit_price
            });
        });

        // Nếu có lỗi
        if (!isValid) {
            let title = 'Lỗi dữ liệu';
            let text = 'Hệ thống sẽ cuộn đến dòng đầu tiên lỗi và đánh dấu các dòng sai.';

if (errorType === 'missing') {
    title = 'Thiếu thông tin';
    text = 'Vui lòng điền đầy đủ tất cả các trường bắt buộc.';
} else if (errorType === 'variant') {
    title = 'Thiếu biến thể';
    text = 'Bạn chưa chọn biến thể cho một sản phẩm.';
} else if (errorType === 'price') {
    title = 'Sai lệch giá nhập';
    text = 'Có mã sản phẩm trùng nhưng giá nhập khác nhau.';
} else if (errorType === 'invalid_price') {
    title = 'Giá không hợp lệ';
    text = 'Giá nhập không hợp lệ. Vui lòng nhập số hợp lệ lớn hơn 0.';
} else if (errorType === 'invalid_quantity') {
    title = 'Số lượng không hợp lệ';
    text = 'Số lượng nhập phải là số và lớn hơn 0.';
}


            Swal.fire({
                icon: 'warning',
                title: title,
                text: text,
                confirmButtonText: 'OK',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didClose: () => {
                    firstErrorRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    errorRows.forEach(row => {
                        row.classList.add('row-error-highlight');
                        setTimeout(() => {
                            row.classList.remove('row-error-highlight');
                        }, 3000);
                        
                    });
                }
            });

            return;
        }

        if (productList.length === 0) {
            return showError("Lưu phiếu nhập thất bại");
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
                    document.getElementById('formNhapPhieuNhap').reset();
                    document.getElementById('dynamic-product-forms').innerHTML = '';
                    bootstrap.Modal.getInstance(document.getElementById('modalCreatePN')).hide();
                    loadPhieuNhap(currentPage);
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
function updateUrlWithPage(page) {
    const url = new URL(window.location.href);
    url.searchParams.set('pageadmin', page); // cập nhật hoặc thêm mới
    window.history.pushState({}, '', url);    // thay đổi URL trên trình duyệt
}

function phantrang() {
    document.querySelectorAll(".page-link-custom").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            currentPage = parseInt(this.dataset.page);
            updateUrlWithPage(currentPage);
            loadPhieuNhap(currentPage);
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
                    updateUrlWithPage(currentPage);
                    loadPhieuNhap(page);
                }
            }
        });
    }
}

// function phantrang()
// {
//             // Gán lại sự kiện cho nút chuyển trang
//             document.querySelectorAll(".page-link-custom").forEach(btn => {
//                 btn.addEventListener("click", function (e) {
//                     e.preventDefault();
//                     currentPage = parseInt(this.dataset.page); // lưu lại trang hiện tại
//                      loadPhieuNhap(this.dataset.page);
    
//                 });
//             });
//             // Sự kiện nhập số trang
//             const input = document.getElementById("pageInput");
//             if (input) {
//                 input.addEventListener("keypress", function (e) {
//                     if (e.key === "Enter") {
//                         e.preventDefault();
//                         let page = parseInt(this.value);
//                         const max = parseInt(this.max);
    
//                         if (page < 1) page = 1;
//                         if (page > max) page = max;
    
//                         if (page >= 1 && page <= max) {
//                             currentPage = page;
//                             loadPhieuNhap(page);
//                         }
//                     }
//                 });
//             } 
// }
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
    
if (Array.isArray(data.data)) {
    data.data.forEach((item, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${(currentPage - 1) * 8 + index + 1}</td>
            <td>${item.variant_id}</td>
            <td>${item.product_name}</td>
            <td>${item.size_name}</td>
            <td>${item.color_name}</td>
            <td>${Number(item.unit_price).toLocaleString('vi-VN')}</td>
            <td>${item.quantity}</td>
            <td>${item.stock}</td>
        `;
        tbody.appendChild(row);
    });
} else {
    tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Không có dữ liệu chi tiết phiếu nhập</td></tr>`;
}

    
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
// document.addEventListener('click', function(e) {
//     if (e.target.classList.contains('btn-xoa')) {
//         const idpn = e.target.dataset.idpn;
//         document.getElementById('btnXacNhanXoaPN').dataset.idpn = idpn;

//         fetch(`./ajax/getCTPhieuNhap.php?idpn=${idpn}`)
//         .then(res => res.json())
//         .then(data => {
//             if (data.success) {
//                 const tbody = document.getElementById('body-xoa-ctpn');
//                 tbody.innerHTML = '';

//                 data.details.forEach(item => {
//                     const tr = document.createElement('tr');
//                     tr.innerHTML = `
//                         <td class="text-center">${item.importreceipt_details_id}</td>
//                         <td class="text-center">${item.product_id}</td>
//                         <td class="text-center">${item.variant_id}</td>
//                         <td class="text-center">${item.quantity}</td>
//                         <td class="text-center">
//                             <button class="btn btn-sm btn-danger btn-xoa-ctpn" data-idctpn="${item.importreceipt_details_id}">Xóa</button>
//                         </td>
//                     `;
//                     tbody.appendChild(tr);
                    
//                 });

//                 // const anhien = document.getElementById('anhienxoa');
//                 // if (data.details.length === 0) {
//                 //     anhien.style.display = 'inline-block';
//                 // } else {
//                 //     anhien.style.display = 'none';
//                 // }

//                 const modal = new bootstrap.Modal(document.getElementById('modalXoaChiTietPN'));
//                 document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
// document.body.classList.remove('modal-open');
// document.body.style = '';
//                 modal.show();
//             }
//         });
//     }

//     if (e.target.classList.contains('btn-xoa-ctpn')) {
//         const idctpn = e.target.dataset.idctpn;
//         showConfirmBox('Bạn có chắc chắn muốn xóa chi tiết này?', function() {
//             fetch('./ajax/deleteCTphieunhap.php', {
//                 method: 'POST',
//                 headers: {
//                     'Content-Type': 'application/x-www-form-urlencoded'
//                 },
//                 body: `idctpn=${idctpn}`
//             })
//             .then(res => res.json())
//             .then(data => {
//                 if (data.success) {
//                     e.target.closest('tr').remove();
//                     loadPhieuNhap(currentPage);
//                     const tbXoaTC = document.querySelector('.thongbaoXoaThanhCong');
//                     tbXoaTC.style.display = 'block';
//                     tbXoaTC.classList.add('show');
//                     setTimeout(() => tbXoaTC.classList.remove('show'), 2000);

//                     const remainingRows = document.querySelectorAll('#body-xoa-ctpn tr').length;
//                     const anhien = document.getElementById('anhienxoa');
//                     if (remainingRows === 0) {
//                         anhien.style.display = 'inline-block';
//                     }
//                 } else {
//                     const tbXoaTB = document.querySelector('.thongbaoXoaKhongThanhCong');
//                     tbXoaTB.style.display = 'block';
//                     tbXoaTB.classList.add('show');
//                     setTimeout(() => tbXoaTB.classList.remove('show'), 2000);                
//                 }
//             });
//         });
//     }

//     if (e.target.id === 'btnXacNhanXoaPN') {
//         const idpn = e.target.dataset.idpn;
//         showConfirmBox('Bạn có chắc chắn muốn xóa Phiếu nhập?', function() {
//             fetch('./ajax/deletePhieuNhap.php', {
//                 method: 'POST',
//                 headers: {
//                     'Content-Type': 'application/x-www-form-urlencoded'
//                 },
//                 body: `id=${idpn}`
//             })
//             .then(res => res.json())
//             .then(data => {
//                 if (data.success) {
//                     const tbXoaTC = document.querySelector('.thongbaoXoaPNthanhcong');
//                     tbXoaTC.style.display = 'block';
//                     tbXoaTC.classList.add('show');
//                     setTimeout(() => tbXoaTC.classList.remove('show'), 2000);

//                     const modal = bootstrap.Modal.getInstance(document.getElementById('modalXoaChiTietPN'));
//                     modal.hide();
//                     loadPhieuNhap(currentPage);
//                 } else {
//                     const tbXoaTC = document.querySelector('.thongbaoXoaPNKhongThanhCong');
//                     tbXoaTC.style.display = 'block';
//                     tbXoaTC.classList.add('show');
//                     setTimeout(() => tbXoaTC.classList.remove('show'), 2000);                }
//             });
//         });
//     }
// });

document.addEventListener('click', function (e) {
    // 1. Khi click nút xoá phiếu nhập
    if (e.target.classList.contains('btn-xoa')) {
        const idpn = e.target.dataset.idpn;
        document.getElementById('btnXacNhanXoaPN').dataset.idpn = idpn;

        fetchChiTietXoa(idpn, 1); // gọi lần đầu với trang 1

        const modal = new bootstrap.Modal(document.getElementById('modalXoaChiTietPN'));
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style = '';
        modal.show();
    }

    // 2. Xoá từng chi tiết
    if (e.target.classList.contains('btn-xoa-ctpn')) {
        const idctpn = e.target.dataset.idctpn;
        showConfirmBox('Bạn có chắc chắn muốn xóa chi tiết này?', function () {
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

    // 3. Xác nhận xoá toàn bộ phiếu nhập
    if (e.target.id === 'btnXacNhanXoaPN') {
        const idpn = e.target.dataset.idpn;
        showConfirmBox('Bạn có chắc chắn muốn xóa Phiếu nhập?', function () {
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
                        setTimeout(() => tbXoaTC.classList.remove('show'), 2000);
                    }
                });
        });
    }
});

// ✅ Hàm fetch chi tiết phiếu nhập có phân trang
function fetchChiTietXoa(idpn, page = 1) {
    fetch(`./ajax/getCTPhieuNhap.php?idpn=${idpn}&page=${page}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const tbody = document.getElementById('body-xoa-ctpn');
                tbody.innerHTML = '';

                data.details.forEach(item => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="text-center">${item.importreceipt_details_id}</td>
                        <td class="text-center">${item.product_id} - ${item.product_name}</td>
                        <td class="text-center">${item.variant_id}</td>
                        <td class="text-center">${item.quantity}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-danger btn-xoa-ctpn" data-idctpn="${item.importreceipt_details_id}">Xóa</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });

                renderPaginationXoaCT(idpn, data.pagination);
            }
        });
}

// ✅ Hàm render phân trang
function renderPaginationXoaCT(idpn, pagination) {
    const wrap = document.getElementById('phantrang-xoa-ctpn');
    if (!pagination || !wrap) return;

    const current = pagination.current;
    const total = pagination.total;
    wrap.innerHTML = '';

    if (total <= 1) return;

    const btnPrev = document.createElement('button');
    btnPrev.innerHTML = '<i class="fa fa-chevron-left text-dark"></i>';
    btnPrev.className = "btn btn-outline-secondary";
    btnPrev.disabled = current === 1;

    const inputPage = document.createElement("input");
    inputPage.type = "number";
    inputPage.min = 1;
    inputPage.max = total;
    inputPage.value = current;
    inputPage.style.width = "60px";
    inputPage.className = "form-control d-inline-block text-center mx-2";

    const spanTotal = document.createElement("span");
    spanTotal.innerHTML = `/ ${total}`;
    spanTotal.classList.add("mt-2", "me-2");

    const btnNext = document.createElement("button");
    btnNext.innerHTML = '<i class="fa fa-chevron-right text-dark"></i>';
    btnNext.className = "btn btn-outline-secondary";
    btnNext.disabled = current === total;

    btnPrev.onclick = () => fetchChiTietXoa(idpn, current - 1);
    btnNext.onclick = () => fetchChiTietXoa(idpn, current + 1);

    inputPage.addEventListener("keypress", function (e) {
        if (e.key === "Enter") {
            let val = parseInt(this.value);
            if (isNaN(val)) return;
            if (val < 1) val = 1;
            if (val > total) val = total;
            fetchChiTietXoa(idpn, val);
        }
    });

    wrap.appendChild(btnPrev);
    wrap.appendChild(inputPage);
    wrap.appendChild(spanTotal);
    wrap.appendChild(btnNext);
}


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
        $(document).ready(function () {
            $('#modalSuaPhieuNhap').on('shown.bs.modal', function () {
                $('#supplier_idSuaPN').select2({
                    dropdownParent: $('#modalSuaPhieuNhap'),
                    width: '100%'
                });
            });
        });
        
    }
});

// Hàm cập nhật chi tiết phiếu nhập

document.getElementById('btn_sua_pn').addEventListener('click', function () {
    if (!permissions.includes('update')) {
        const tBquyen = document.querySelector('.thongBaoQuyen');
        tBquyen.style.display = 'block';
        tBquyen.classList.add('show');
        setTimeout(() => tBquyen.classList.remove('show'), 2000);
        return; 
    }
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
              <input type="text" class="form-control bg-light" value="${item.product_id} - ${item.product_name}" readonly>
              <input type="hidden" name="product_ids[]" class="product_id" value="${item.product_id}">
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
