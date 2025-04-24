document.addEventListener('DOMContentLoaded', function () {
    let productList = [];
    let productCount = 0;
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

    function adjustPageIfLastItem() {
        const btnCount = document.querySelectorAll(".btn-sua").length;
        if (btnCount === 1 && currentPage > 1) {
            currentPage -= 1;
        }
    }

    // Cập nhật bảng sản phẩm hiển thị
    function updateProductList() {
        const tbody = document.getElementById('product-list-tamluu');
        tbody.innerHTML = '';
        productList.forEach((item, index) => {
            const row = document.createElement('tr');
            row.classList.add('text-center');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${item.user_id}</td>
                <td>${item.supplier_id}</td>
                <td>${item.product_name}</td>
                <td><img src="${item.image_preview}" width="50" height="50" style="object-fit:cover;"></td>
                <td>${item.size_name} - ${item.color_name}</td>
                <td>${item.quantity}</td>
                <td>
                    <button class="btn btn-success btn-sm" onclick="editProduct(${item.id})">Sửa</button>
                    <button class="btn btn-danger btn-sm" onclick="removeProduct(${item.id})">Xoá</button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    // Sửa sản phẩm
    
    function editProduct(id) {
        const product = productList.find(p => p.id === id);
        if (product) {
            const formSua = document.querySelector('.formSua');
            formSua.style.display = 'block';
            document.querySelector('.overlay').style.display='block';
            document.getElementById('supplier_idSua').disabled = true;
            document.getElementById('stt').value = product.id;
            document.getElementById('supplier_idSua').value = product.supplier_id;
            document.getElementById('user_idSua').value = product.user_id;
            document.getElementById('cbTenSua').value = product.product_id;
            document.getElementById('cbSizeSua').value = product.size_id;
            document.getElementById('cbMauSua').value = product.color_id;
            document.getElementById('txtSlSua').value = product.quantity;
    
            const imgEl = document.querySelector('#hienthianhSua img');
            if (product.image_preview) {
                imgEl.src = product.image_preview;
                imgEl.style.display = 'block';
                document.getElementById('tenFileAnhSua').innerText = product.image_name || '';
            } else {
                imgEl.style.display = 'none';
                document.getElementById('tenFileAnhSua').innerText = '';
            }
        }
    }
    
    // ✅ Thêm dòng này để dùng được trong onclick HTML:
    window.editProduct = editProduct;
    
    document.getElementById('fileAnhSua').addEventListener('change', function () {
        const file = this.files[0];
        const imgEl = document.querySelector('#hienthianhSua img');
        const fileNameEl = document.getElementById('tenFileAnhSua');
    
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                imgEl.src = e.target.result;
                imgEl.style.display = 'block';
                fileNameEl.innerText = file.name;
            };
            reader.readAsDataURL(file);
        } else {
            imgEl.src = '';
            imgEl.style.display = 'none';
            fileNameEl.innerText = '';
        }
    });
    

    // Khi nhấn "Xác nhận sửa"
    // document.getElementById('btn_add_product_sua').addEventListener('click', function () {
    //     const productId = parseInt(document.getElementById('stt').value);
    //     const supplierId = document.getElementById('supplier_idSua').value;
    //     const userId = document.getElementById('user_idSua').value;
    //     const product_id = document.getElementById('cbTenSua').value;
    //     const product_name = document.getElementById('cbTenSua').options[document.getElementById('cbTenSua').selectedIndex].text;
    //     const size_id = document.getElementById('cbSizeSua').value;
    //     const size_name = document.getElementById('cbSizeSua').options[document.getElementById('cbSizeSua').selectedIndex].text;
    //     const color_id = document.getElementById('cbMauSua').value;
    //     const color_name = document.getElementById('cbMauSua').options[document.getElementById('cbMauSua').selectedIndex].text;
    //     const quantity = parseInt(document.getElementById('txtSlSua').value);
    //     const file = document.getElementById('fileAnhSua').files[0];

    //     const productIndex = productList.findIndex(p => p.id === productId);
    //     if (productIndex !== -1) {
    //         productList[productIndex] = {
    //             ...productList[productIndex],
    //             supplier_id: supplierId,
    //             user_id: userId,
    //             product_id,
    //             product_name,
    //             size_id,
    //             size_name,
    //             color_id,
    //             color_name,
    //             quantity
    //         };
    
    //         if (file) {
    //             productList[productIndex].image_file = file;
    //             productList[productIndex].image_preview = URL.createObjectURL(file);
    //         }
    //     }
    
    //     updateProductList();
    //     document.querySelector('.formSua').style.display = 'none';
    // });

    document.getElementById('btn_add_product_sua').addEventListener('click', function () {
        const productId = parseInt(document.getElementById('stt').value);
        const supplierId = document.getElementById('supplier_idSua').value;
        const userId = document.getElementById('user_idSua').value;
        const product_id = document.getElementById('cbTenSua').value;
        const product_name = document.getElementById('cbTenSua').options[document.getElementById('cbTenSua').selectedIndex].text;
        const size_id = document.getElementById('cbSizeSua').value;
        const size_name = document.getElementById('cbSizeSua').options[document.getElementById('cbSizeSua').selectedIndex].text;
        const color_id = document.getElementById('cbMauSua').value;
        const color_name = document.getElementById('cbMauSua').options[document.getElementById('cbMauSua').selectedIndex].text;
        const quantity = parseInt(document.getElementById('txtSlSua').value);
        const file = document.getElementById('fileAnhSua').files[0];
        const image_name = file ? file.name : productList.find(p => p.id === productId)?.image_name || '';
    
        const productIndex = productList.findIndex(p => p.id === productId);
    
        // ✅ Tìm sản phẩm khác (không phải cái đang sửa) có cùng cấu hình
        const existingIndex = productList.findIndex(p =>
            p.id !== productId &&
            p.product_id == product_id &&
            p.size_id == size_id &&
            p.color_id == color_id &&
            p.image_name == image_name
        );
    
        if (existingIndex !== -1) {
            // ✅ Có trùng → hiện cảnh báo
            // document.getElementById('boxTrungSP').style.display = 'block';
// Hiển thị cảnh báo trùng
const trungSPText = document.getElementById('trungTenSP');
const trungChiTietText = document.getElementById('trungChiTiet');
trungSPText.textContent = `Sản phẩm "${product_name}" đã có trong hàng đợi!`;
trungChiTietText.textContent = `Cấu hình: ${size_name} - ${color_name}. Bạn có muốn cộng dồn vào không?`;

const box = document.getElementById('boxTrungSP');
box.classList.add('show', 'shake');
document.querySelector('.overlay').style.display = 'block';

// Xóa hiệu ứng rung sau 400ms (chỉ chạy 1 lần)
setTimeout(() => box.classList.remove('shake'), 400);

            
            document.getElementById('btnCoTrung').onclick = function () {
                productList[existingIndex].quantity += quantity;
                productList.splice(productIndex, 1); // Xóa sản phẩm đang sửa
                updateProductList();
                document.querySelector('.formSua').style.display = 'none';
                document.getElementById('boxTrungSP').classList.remove('show');
                document.querySelector('.overlay').style.display = 'none';

            };
    
            document.getElementById('btnKhongTrung').onclick = function () {
                document.getElementById('boxTrungSP').classList.remove('show');
                document.querySelector('.overlay').style.display = 'none';

            };
    
            return; // ✅ Dừng xử lý tiếp
        }
    
        // ✅ Không trùng → cập nhật như thường
        if (productIndex !== -1) {
            productList[productIndex] = {
                ...productList[productIndex],
                supplier_id: supplierId,
                user_id: userId,
                product_id,
                product_name,
                size_id,
                size_name,
                color_id,
                color_name,
                quantity,
                image_name
            };
    
            if (file) {
                productList[productIndex].image_file = file;
                productList[productIndex].image_preview = URL.createObjectURL(file);
            }
        }
    
        updateProductList();
        document.querySelector('.formSua').style.display = 'none';
        document.querySelector('.overlay').style.display = 'none';

    });
    
    

    // Khi nhấn "Đóng"
    document.querySelector('.formSua button.btn-outline-primary').addEventListener('click', function() {
    document.querySelector('.formSua').style.display = 'none';
    document.querySelector('.overlay').style.display = 'none';
});


    // Xóa sản phẩm khỏi hàng đợi
    window.removeProduct = function(id) {
        productList = productList.filter(p => p.id !== id);
        if(productList.length === 0)
        {
            document.getElementById('supplier_id').disabled = false;
        }
        updateProductList();
    };
    window.removeProduct = removeProduct;


    document.querySelector('.formSua button[type="button"]').addEventListener('click', function() {
    // Ẩn form khi nhấn Đóng, không xóa dữ liệu
    document.querySelector('.formSua').style.display = 'none';
});

// check var xem có tồn tại biến thể đó trong database chưa
function checkVariantExists(product_id, size_id, color_id, image_name, current_id = '') {
    const params = new URLSearchParams({
        product_id,
        size_id,
        color_id,
        image: image_name,
        current_id
    });

    return fetch(`./ajax/checkBT.php?${params.toString()}`)
        .then(res => res.json());
}
function showVariantModal({ title, content, onConfirm }) {
    const box = document.getElementById('boxTrungBT');
    const overlay = document.querySelector('.overlay');

    document.getElementById('trungTenBT').textContent = title;
    document.getElementById('trungCTBT').innerHTML = content;

    box.classList.add('show', 'shake');
    overlay.style.display = 'block';
    setTimeout(() => box.classList.remove('shake'), 400);

    // Gỡ sự kiện cũ trước khi gán mới
    const btnXacNhan = document.getElementById('btnXacNhanThem');
    const btnHuy = document.getElementById('btnHuyThem');

    btnXacNhan.onclick = function () {
        onConfirm();
        box.classList.remove('show');
        overlay.style.display = 'none';
    };

    btnHuy.onclick = function () {
        box.classList.remove('show');
        overlay.style.display = 'none';
    };
}


    // Thêm sản phẩm vào danh sách
    document.getElementById('add_product').addEventListener('click', function () {
        const supplier_id = document.getElementById('supplier_id').value;
        const user_id = document.getElementById('user_id').value;
        const product_id = document.getElementById('cbTen').value;
        const product_name = document.getElementById('cbTen').options[document.getElementById('cbTen').selectedIndex].text;
        const color_id = document.getElementById('cbMau').value;
        const color_name = document.getElementById('cbMau').options[document.getElementById('cbMau').selectedIndex].text;
        const size_id = document.getElementById('cbSize').value;
        const size_name = document.getElementById('cbSize').options[document.getElementById('cbSize').selectedIndex].text;
        const quantity = parseInt(document.getElementById('txtSl').value);
        const imageFile = document.getElementById('fileAnh').files[0];
        const formNhap = document.getElementById('formNhapPhieuNhap');
        
        document.getElementById('supplier_id').disabled = true;

        if (!permissions.includes('write')) {
            const tBquyen = document.querySelector('.thongBaoQuyen');
            tBquyen.style.display = 'block';
            tBquyen.classList.add('show');
            setTimeout(() => tBquyen.classList.remove('show'), 2000);
            const img = document.querySelector('#hienthianh img');
            img.style.display = 'none';
            formNhap.reset();
            return; 
        }

        function showError(loinhan) {
            const thongbao = document.querySelector(".thongbaoLoi");
            const loi = thongbao.querySelector("p");
            loi.textContent = loinhan;
            thongbao.style.display = 'block';
            thongbao.classList.add('show');
            setTimeout(() => thongbao.classList.remove('show'), 2000);
        }
    
        if (!supplier_id || !user_id || !product_id || !color_id || !size_id || !quantity || quantity <= 0 || !imageFile) {
            return showError("Vui lòng nhập đầy đủ thông tin");
        }
    
        const imageUrl = URL.createObjectURL(imageFile);

        document.getElementById('cbSize').value = '';
        document.getElementById('txtSl').value = '';

        const newItem = {
            id: ++productCount,
            supplier_id: parseInt(supplier_id),
            user_id: parseInt(user_id),
            product_id: parseInt(product_id),
            product_name: product_name,
            color_id: parseInt(color_id),
            color_name: color_name,
            size_id: parseInt(size_id),
            size_name: size_name,
            quantity: quantity,
            image_preview: imageUrl,
            image_file: imageFile,
            image_name: imageFile.name
        };
    
        const existingIndex = productList.findIndex(p =>
            p.product_id === newItem.product_id &&
            p.color_id === newItem.color_id &&
            p.size_id === newItem.size_id &&
            p.image_name === newItem.image_name
        );


    
        if (existingIndex !== -1) {
            showVariantModal({
                title: `Sản phẩm "${product_name}" đã có trong hàng đợi!`,
                content: `Cấu hình: ${size_name} - ${color_name}. Bạn có muốn cộng dồn vào không?`,
                onConfirm: () => {
                    productList[existingIndex].quantity += quantity;
                    updateProductList();
                }
            });
            return;
        }
    
        checkVariantExists(product_id, size_id, color_id, imageFile.name).then(result => {
            if (result.exists) {
                showVariantModal({
                    title: `Biến thể đã tồn tại trong hệ thống!`,
                    content: `Thông số: ${size_name} - ${color_name} - Ảnh: ${imageFile.name}. <br> Bạn có muốn cộng dồn vào danh sách không?`,
                    onConfirm: () => {
                        productList.push(newItem);
                        updateProductList();
                    }
                });
            } else {
                productList.push(newItem);
                updateProductList();
            }
        });
    });
    
    
    document.getElementById('resetFormProduct').addEventListener("click",function()
{
    document.getElementById('cbTen').value = '';
    document.getElementById('fileAnh').value = '';
    document.getElementById('hienthiimg').style.display = 'none';
    document.getElementById('cbMau').value = '';
    document.getElementById('cbSize').value = '';
    document.getElementById('txtSl').value = '';

});

    // Hiển thị ảnh preview
    document.getElementById('fileAnh').addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const img = document.querySelector('#hienthianh img');
            img.src = URL.createObjectURL(file);
            img.style.display = 'block';
        }
    });

    document.getElementById('btnMoForm').addEventListener('click',function()
{
    document.querySelector('.formNhapSanPham').style.display = 'block';
    document.querySelector('.overlay').style.display = 'block';

});

    document.getElementById('btnDongSanPham').addEventListener('click',function()
{
    document.querySelector('.formNhapSanPham').style.display = 'none';
    document.querySelector('.overlay').style.display = 'none';

});
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
        document.querySelector('.overlay').style.display = 'none';
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
            document.querySelector('.overlay').style.display = 'none';
            document.querySelector('.formNhapSanPham').style.display = 'none';
    
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
function capNhatLaiDropdownTenSanPham(id, name) {
    const cbTen = document.getElementById('cbTen');
    const option = document.createElement('option');
    option.value = id;
    option.textContent = `${id} - ${name}`;
    option.selected = true;
    cbTen.appendChild(option);
}


    // Khi nhấn "Lưu phiếu nhập"
    document.getElementById('formNhapPhieuNhap').addEventListener('submit', function (e) {
        e.preventDefault();
    
        const supplier_id = document.getElementById('supplier_id').value;
        const user_id = document.getElementById('user_id').value;
    
        if (productList.length === 0) {
            const tbLoi = document.querySelector('.thongbaoLuuKhongThanhCong');
            tbLoi.style.display = "block";
            tbLoi.classList.add("show");
            setTimeout(() => tbLoi.classList.remove('show'), 2000);
            return;
        }
    
        const formData = new FormData();
        formData.append('supplier_id', supplier_id);
        formData.append('user_id', user_id);
    
        // const dataToSend = productList.map((item) => {
        //     return {
        //         product_id: item.product_id,
        //         color_id: item.color_id,
        //         size_id: item.size_id,
        //         quantity: item.quantity,
        //         image_name: item.image_name || null
        //     };
        // });
    
        // formData.append('products', JSON.stringify(dataToSend));
        const dataToSend = [];

productList.forEach((item, index) => {  
    dataToSend.push({
        product_id: item.product_id,
        color_id: item.color_id,
        size_id: item.size_id,
        quantity: item.quantity,
        image_name: item.image_name || null
    });

    if (item.image_file) {
        formData.append('images[]', item.image_file, item.image_name); // ✅ gửi đúng file kèm tên
    } else {
        // gửi file rỗng nếu cần đồng bộ chỉ số
        formData.append('images[]', new Blob(), ''); // giữ vị trí đồng bộ với PHP
    }
});

formData.append('products', JSON.stringify(dataToSend));

    
        fetch('./ajax/insertPhieuNhap.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                // ✅ Cập nhật lại image_name thực tế đã được PHP lưu (nếu có trả về)
                if (res.image_names && Array.isArray(res.image_names)) {
                    productList.forEach((item, index) => {
                        if (res.image_names[index]) {
                            item.image_name = res.image_names[index];
                        }
                    });
                }
        
                document.getElementById('supplier_id').disabled = false;
                const tbTC = document.querySelector('.thongbaoLuuThanhCong');
                tbTC.style.display = "block";
                tbTC.classList.add("show");
                setTimeout(() => tbTC.classList.remove('show'), 2000);
                productList = [];
                productCount = 0;
                updateProductList();
                document.getElementById('formNhapPhieuNhap').reset();
                document.querySelector('#hienthianh img').style.display = 'none';
        
                // Tải lại bảng
                loadPhieuNhap(currentPage);
            } else {
                alert("Lỗi khi lưu: " + res.message);
            }
        });
        
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
            document.querySelectorAll('.btn-sua').forEach(button => {
         button.addEventListener('click', function () {
        // Hiện form
        document.querySelector('.formSuaPN').style.display = 'block';
        document.querySelector('.overlay').style.display = 'block';

        // Lấy dữ liệu từ nút
        const idpn = this.dataset.idpn;
        const idnv = this.dataset.idnv;
        const idncc = this.dataset.idncc;
        const gia = this.dataset.gia;
        const ngaylap = this.dataset.ngaylap;

        // Gán dữ liệu vào form
        document.getElementById('txtMaPNsua').value = idpn;
        document.getElementById('user_idSuaPN').value = idnv;
        document.getElementById('supplier_idSuaPN').value = idncc;
        document.getElementById('txtTongGT').value = formatPrice(gia);
        document.getElementById('txtNgayLap').value = ngaylap;
    });
});

document.getElementById('btn_sua_pn').addEventListener('click', function () {
    const form = document.getElementById('formSuaPN');
    const tbThanhCong = document.querySelector(".thongbaoUpdateThanhCong");
    const tbThatBai = document.querySelector(".thongbaoUpdateKhongThanhCong");
    document.querySelector('.overlay').style.display = 'none';
    const formData = new FormData(form);
    let rawGia = formData.get('txtTongGT');
    let cleanGia = rawGia.replace(/\./g, '');
    formData.set('txtTongGT', cleanGia);

    if (!permissions.includes('update')) {
        const tBquyen = document.querySelector('.thongBaoQuyen');
        tBquyen.style.display = 'block';
        tBquyen.classList.add('show');
        document.querySelector('.formSuaPN').style.display='none';
        setTimeout(() => tBquyen.classList.remove('show'), 2000);
        return; 
    }

    fetch('./ajax/updatePhieuNhap.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            tbThanhCong.style.display = "block";
            tbThanhCong.classList.add("show");
            setTimeout(() => tbThanhCong.classList.remove('show'), 2000);
            document.querySelector('.formSuaPN').style.display = 'none';
            adjustPageIfLastItem();

            loadPhieuNhap(currentPage);
        } else {
            tbThatBai.style.display = "block";
            tbThatBai.classList.add("show");
            setTimeout(() => tbThatBai.classList.remove('show'), 2000);
            document.querySelector('.formSuaPN').style.display = 'none';        }
    })
    .catch(error => {
        alert('Có lỗi xảy ra khi gửi yêu cầu.');
        console.error(error);
    });
});









        })
        .catch(error => {
            console.error('Lỗi khi tải phiếu nhập:', error);
        });
}



// Gọi hàm này khi trang vừa load
loadPhieuNhap();
// Gán sự kiện click cho nút Sửa phiếu nhập
document.querySelector('.formSuaPN button.btn-outline-primary').addEventListener('click', function () {
    document.querySelector('.formSuaPN').style.display = 'none';
    document.querySelector('.overlay').style.display = 'none';

});

});

