let productList = [];
let productCount = 0;
let productPendingToAdd = null; 
// fetch_ctphieunhap.js (kiểm tra product_id tồn tại trước khi thêm)
document.addEventListener('DOMContentLoaded', function () {


    let currentPage = 1; // ⚠️ Fix thiếu biến này gây lỗi load lại trang khi xoá

    function loadPhieuNhap(page = 1) {
        fetch('./ajax/quanlyChiTietPhieuNhap_ajax.php?pageproduct=' + page)
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
                                loadPhieuNhap(page);
                            }
                        }
                    });
                }

                document.querySelectorAll('.btn-sua').forEach(button => {
                    button.addEventListener('click', function () {
                        document.querySelector('.formSuaPN').style.display = 'block';
                        document.querySelector('.overlay').style.display = 'block';

                        const idctpn = this.dataset.idct;
                        const idpn = this.dataset.idpn;
                        const idsp = this.dataset.idsp;
                        const idbt = this.dataset.variant;
                        const gia = this.dataset.gia;
                        const ngaylap = this.dataset.ngaylap;
                        const tongtien = this.dataset.tongtien;
                        const soluong = this.dataset.soluong;

                        document.getElementById('txtMaCTPNsua').value = idctpn;
                        document.getElementById('txtMaPNsua').value = idpn;
                        document.getElementById('txtMaSPsua').value = idsp;
                        document.getElementById('txtTongGT').value = formatPrice(tongtien);
                        document.getElementById('txtNgayLap').value = ngaylap;
                        document.getElementById('txtMaBTsua').value = idbt;
                        document.getElementById('txtGiaNhap').value = formatPrice(gia);
                        document.getElementById('txtSlsuaTon').value = soluong;
                    });
                });
            });
    }

    // ⚠️ Gọi hàm để load dữ liệu khi vừa DOM ready
    loadPhieuNhap();

    // (Phần dưới giữ nguyên - đã chuẩn)


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

    function updateProductList() {
        const productTable = document.getElementById('product-list-tamluu');
        productTable.innerHTML = '';

        productList.forEach((product, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${product.import_receipt_id}</td>
                <td>${product.product_id}</td>
                <td><img src="../../assets/img/sanpham/${product.image || ''}" class="img-fluid" style="width: 100%px; max-height: 80px; object-fit: contain;"></td>
                <td>${product.size}</td>
                <td>${product.quantity}</td>
                <td>${product.color}</td>
                <td>
                        <div class="d-flex gap-3 justify-content-center">
                                <div class="">
                                     <button class="btn btn-success btn-edit" data-id="${product.id}" style="width:60px;">Sửa</button>
                                </div>
                                <div class="">
                                      <button class="btn btn-danger btn-delete" data-id="${product.id}" style="width:60px;">Xóa</button>
                                </div>
                        </div>

                </td>`
            ;
            productTable.appendChild(row);

            row.querySelector('.btn-edit').addEventListener('click', () => editProduct(product.id));
            row.querySelector('.btn-delete').addEventListener('click', () => removeProduct(product.id));
        });
    }

    function editProduct(id) {
        const product = productList.find(p => p.id === id);
        if (product) {
            const form = document.querySelector('.formSua');
            form.style.display = 'block';

            document.getElementById('txtSTT').value = product.id;
            document.getElementById('txtMaPNSua').value = product.import_receipt_id;
            document.getElementById('txtMaSua').value = product.product_id;
            document.getElementById('txtSlSua').value = product.quantity;

            document.getElementById('cbSizeSua').value = product.size_id;
            document.getElementById('cbMauSua').value = product.color_id;

            const imgSua = document.querySelector('#hienthianhSua img');
            imgSua.src =` ../../assets/img/sanpham/${product.image || ''}`;
            imgSua.style.display = product.image ? 'block' : 'none';

            document.getElementById('tenFileAnhSua').textContent = product.image || '';
        }
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

    function removeProduct(id) {
        productList = productList.filter(product => product.id !== id);
        if(productList.length === 0)
        {
            document.getElementById('fileAnh').disabled = false;
            document.getElementById('cbMau').disabled = false;
            document.getElementById('txtMaPN').disabled = false;
            document.getElementById('txtMa').disabled = false;
        }
        updateProductList();
    }

    document.querySelector('.formSua button.btn-outline-primary').addEventListener('click', () => {
        document.querySelector('.formSua').style.display = 'none';
    });

    document.getElementById('add_product').addEventListener('click', async function () {
        const import_receipt_id = document.getElementById('txtMaPN').value.trim();
        const product_id = document.getElementById('txtMa').value.trim();
        const cbSizeSelect = document.getElementById('cbSize');
        const cbColorSelect = document.getElementById('cbMau');
        const size_id = cbSizeSelect.value;
        const color_id = cbColorSelect.value;
        const sizeName = cbSizeSelect.options[cbSizeSelect.selectedIndex].text;
        const colorName = cbColorSelect.options[cbColorSelect.selectedIndex].text;
        const quantity = document.getElementById('txtSl').value.trim();

        const imageInput = document.getElementById('fileAnh');
    
        if (!import_receipt_id || !product_id || !size_id || !color_id || !quantity || isNaN(quantity) || quantity <= 0) {
            return showError("Vui lòng nhập đầy đủ và đúng thông tin!");
        }
    
        if (!imageInput.files || imageInput.files.length === 0) {
            return showError("Vui lòng chọn ảnh!");
        }
    
        const file = imageInput.files[0];
        const validImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!validImageTypes.includes(file.type)) {
            return showError("Tệp được chọn không phải là ảnh hợp lệ!");
        }
    
        // ✅ Kiểm tra mã phiếu nhập tồn tại
        try {
            const res = await fetch(`./ajax/checkPN.php?pn_id=${import_receipt_id}`);
            const data = await res.json();
            if (!data.exists) return showError("Mã phiếu nhập không tồn tại!");
        } catch (err) {
            return showError("Lỗi kiểm tra mã phiếu nhập!");
        }
    
        // ✅ Kiểm tra mã sản phẩm tồn tại
        try {
            const res = await fetch(`./ajax/checkID.php?product_id=${product_id}`);
            const data = await res.json();
            if (!data.exists) return showError("Mã sản phẩm không tồn tại!");
        } catch (err) {
            return showError("Lỗi kiểm tra mã sản phẩm!");
        }
    
        // ✅ Tìm variant_id (nếu có)
        let variant_id = '';
        try {
            const filename = file.name;
            const res = await fetch(`./ajax/checkBT.php?product_id=${product_id}&size_id=${size_id}&color_id=${color_id}&image=${encodeURIComponent(filename)}`);
            const data = await res.json();
            if (data.exists) {
                variant_id = data.variant_id;
            } else {
                variant_id = ''; // sẽ tạo mới khi lưu nếu chưa có
            }
        } catch (err) {
            return showError("Không thể kiểm tra biến thể sản phẩm!");
        }
    
        const filename = file.name;
        // ✅ Thêm vào danh sách tạm
        const existedItem = productList.find(item =>
            item.product_id === product_id &&
            item.size_id === size_id &&
            item.color_id === color_id
        );
        
        if (existedItem) {
            // Hiển thị modal xác nhận
            document.getElementById('boxTrungSP').style.display = 'block';
        
            // Lưu tạm sản phẩm đang nhập
            productPendingToAdd = {
                existedItem,
                quantity: parseInt(quantity)
            };
        
            return; // Chờ người dùng chọn Có / Không
        }
            productList.push({
                id: ++productCount,
                import_receipt_id,
                variant_id,
                product_id,
                image: filename,
                size_id,
                size: sizeName,
                color_id,
                color: colorName,
                quantity: parseInt(quantity)
            });
        

    
    
        updateProductList();
        // document.getElementById('formNhapSPbienThe').reset();
        document.getElementById('txtMaPN').disabled = true;
        document.getElementById('txtMa').disabled = true;
        document.getElementById('fileAnh').disabled = true;
        document.getElementById('cbMau').disabled = true;
        document.getElementById('cbSize').value = '';
        document.getElementById('txtSl').value = '';
    });
    
    document.getElementById('block_product').addEventListener('click', function () {
        const anh = document.getElementById('fileAnh');
        const mau = document.getElementById('cbMau');
        const idpn = document.getElementById('txtMaPN');
        const idsp = document.getElementById('txtMa');
        if (anh.disabled && mau.disabled && idpn.disabled && idsp.disabled) {
            anh.disabled = false;
            mau.disabled = false;
            idpn.disabled = false;
            idsp.disabled = false;
        } else {
            anh.disabled = true;
            mau.disabled = true;
            idpn.disabled = true;
            idsp.disabled = true;
        }
    });

    document.getElementById('formNhapSPbienThe').addEventListener('submit', function (e) {
        e.preventDefault();
        const form = document.getElementById('formNhapSPbienThe');
        form.reset();
        document.querySelector('#hienthianh img').style.display = 'none';

        const formData = new FormData(form);

        productList.forEach(product => {
            formData.append('products[]', JSON.stringify(product));
        });

        fetch('./ajax/insertCTphieunhap.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('fileAnh').disabled = false;
                document.getElementById('cbMau').disabled = false;
                document.getElementById('txtMaPN').disabled = false;
                document.getElementById('txtMa').disabled = false;
                const tbTC = document.querySelector(".thongbaoLuuThanhCong");
                tbTC.style.display = "block";
                tbTC.classList.add("show");
                setTimeout(() => tbTC.classList.remove('show'), 2000);
                productList = [];
                productCount = 0;
                updateProductList();
                form.reset();
                loadPhieuNhap(currentPage);
            }else
            {
                const tbTB = document.querySelector(".thongbaoLuuKhongThanhCong");
                tbTB.style.display = "block";
                tbTB.classList.add("show");
                setTimeout(() => tbTB.classList.remove('show'), 2000);
            }
        })
        .catch(err => {
            console.error("Lỗi khi gửi dữ liệu:", err);
        });
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
        const idctpn = document.getElementById("txtMaCTPNsua").value;
        const idpn = document.getElementById("txtMaPNsua").value;
        const idsp = document.getElementById("txtMaSPsua").value;
        let tonggt = document.getElementById("txtTongGT").value;
        const quantity = document.getElementById("txtSlsuaTon").value;
        tonggt = parseFloat(tonggt.replace(/\./g, '').replace(',', '.'));
        const variantId = document.getElementById("txtMaBTsua").value;

        if(!idpn)
        {
            return showError("Mã phiếu nhập không được để trống!");
        }

        if(!idsp)
        {
            return showError("Mã Sản phẩm không được để trống!"); 
        }
        if (!variantId) {
            return showError("Mã biến thể không được để trống!");
        }
        if (isNaN(quantity) || quantity <= 0) {
            return showError("Số lượng phải là số hợp lệ và lớn hơn 0!");
        }
        
        try {
            const res1 = await fetch(`/admin/ajax/checkPN.php?pn_id=${idpn}`);
            const data1 = await res1.json();
            if (!data1.exists) {
                return showError("Mã phiếu nhập không tồn tại!");
            }
    
            const res2 = await fetch(`./ajax/checkID.php?product_id=${idsp}`);
            const data2 = await res2.json();
            if (!data2.exists) {
                return showError("Mã sản phẩm không tồn tại!");
            }
            const res3 = await fetch(`./ajax/checkVariantProduct.php?product_id=${idsp}&variant_id=${variantId}`);
            const data3 = await res3.json();
            if (!data3.match) {
                return showError("Mã biến thể không thuộc sản phẩm này!");
            }
        } catch (error) {
            return showError("Lỗi kiểm tra dữ liệu!");
        }
    
        const formData = new FormData();
        formData.append("txtMaCTPNsua", idctpn);
        formData.append("txtMaPNsua", idpn);
        formData.append("txtMaSPsua", idsp);        
        formData.append("txtTongGT", tonggt);
        formData.append("txtSlsuaTon", quantity);
        formData.append("txtMaBTsua", variantId);

        fetch("./ajax/updateCTPhieuNhap.php", {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const tb = document.querySelector(".thongbaoUpdateThanhCong");
                    tb.style.display = "block";
                    tb.classList.add("show");
                    setTimeout(() => tb.classList.remove("show"), 2000);
                    document.querySelector(".formSuaPN").style.display = "none";
                    document.querySelector(".overlay").style.display = "none";
                    loadPhieuNhap(currentPage);
                } else {
                    alert(data.message || "Cập nhật không thành công!");
                }
            })
            .catch(err => {
                alert("Lỗi kết nối đến máy chủ");
                console.error(err);
            });
    });
    


    document.getElementById('fileAnh').addEventListener('change', function (e) {
        const input = e.target;
        const imgElement = document.querySelector('#hienthianh img');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function (event) {
                imgElement.src = event.target.result;
                imgElement.style.display = 'block';
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            imgElement.style.display = 'none';
            imgElement.src = '';
        }
    });

    document.getElementById('fileAnhSua').addEventListener('change', function (e) {
        const input = e.target;
        const imgElement = document.querySelector('#hienthianhSua img');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function (event) {
                imgElement.src = event.target.result;
                imgElement.style.display = 'block';
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            imgElement.style.display = 'none';
            imgElement.src = '';
        }
    });
    document.getElementById("btn_dong").addEventListener('click',function()
{
    document.querySelector(".formSuaPN").style.display='none';
    document.querySelector(".overlay").style.display='none';
});
}); 