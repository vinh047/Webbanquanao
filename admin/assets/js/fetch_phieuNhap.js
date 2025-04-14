document.addEventListener('DOMContentLoaded', function () {
    let productList = [];
    let productCount = 0;
    let currentPage = 1;

    // Hàm format giá
    function formatPrice(price) {
        // return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        return Number(price).toLocaleString('vi-VN');
    }


    // Cập nhật bảng sản phẩm hiển thị
    function updateProductList() {
        const productTable = document.getElementById('product-list-tamluu');
        productTable.innerHTML = ''; // Làm mới bảng
        productList.forEach((product, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="hienthiid">${index + 1}</td>
                <td class="hienthiid">${product.user_id}</td>
                <td class="hienthiid">${product.supplier_id}</td>
                <td class="tensp">${product.name}</td>
                <td class="hienthiloai">${product.category}</td>
                <td class="mota">${product.description}</td>
                <td class="hienthigia">${formatPrice(product.price)}</td>
                <td class="hienthigia">${product.ptgg}</td>
                <td class="hienthibtn-ne">
                    <div class="d-flex justify-content-center gap-2">
                        <div>
                            <button type="button" class="btn btn-success" id="edit-btn-${product.id}">Sửa</button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-danger" id="delete-btn-${product.id}">Xóa</button>
                        </div>
                    </div>
                </td>
            `;
            productTable.appendChild(row);

            // Thêm sự kiện cho nút "Sửa"
            document.getElementById(`edit-btn-${product.id}`).addEventListener('click', function() {
                editProduct(product.id);
            });

            // Thêm sự kiện cho nút "Xóa"
            document.getElementById(`delete-btn-${product.id}`).addEventListener('click', function() {
                removeProduct(product.id);
            });
        });
    }

    // Sửa sản phẩm
    function editProduct(id) {
        const product = productList.find(p => p.id === id);
        if (product) {
            // Hiển thị form sửa
            const formSua = document.querySelector('.formSua');
            formSua.style.display = 'block'; // Hiển thị form sửa

            // Điền thông tin sản phẩm vào form sửa
            document.getElementById('stt').value = product.id;
            document.getElementById('supplier_idSua').value = product.supplier_id;
            document.getElementById('user_idSua').value = product.user_id;
            document.getElementById('txtTenSua').value = product.name;
            document.getElementById('txtMotaSua').value = product.description;
            document.getElementById('cbLoaiSua').value = product.category_id;
            document.getElementById('txtGiaSua').value = product.price;
            document.getElementById('txtPTSua').value = product.ptgg;
        }
    }

    // Khi nhấn "Xác nhận sửa"
    document.getElementById('btn_add_product_sua').addEventListener('click', function() {
        const productId = document.getElementById('stt').value;
        const productName = document.getElementById('txtTenSua').value;
        const productDescription = document.getElementById('txtMotaSua').value;
        const categoryId = document.getElementById('cbLoaiSua').value;
        const categoryName = document.getElementById('cbLoaiSua').options[document.getElementById('cbLoaiSua').selectedIndex].text;
        const productPrice = document.getElementById('txtGiaSua').value;
        const supplierId = document.getElementById('supplier_idSua').value;
        const supplierName = document.getElementById('supplier_idSua').options[document.getElementById('supplier_idSua').selectedIndex].text;
        const userId = document.getElementById('user_idSua').value;
        const ptggdasua = document.getElementById('txtPTSua').value;

        // Cập nhật sản phẩm trong danh sách
        const productIndex = productList.findIndex(p => p.id === parseInt(productId));
        if (productIndex !== -1) {
            productList[productIndex] = {
                id: parseInt(productId),
                supplier_id: supplierId,
                supplier: supplierName,
                user_id: userId,
                name: productName,
                description: productDescription,
                category_id: categoryId,
                category: categoryName,
                price: productPrice,
                ptgg: ptggdasua
            };
        }

        // Cập nhật lại bảng sản phẩm
        updateProductList();

        // Ẩn form sửa
        document.querySelector('.formSua').style.display = 'none';
    });

    // Khi nhấn "Đóng"
    document.querySelector('.formSua button.btn-outline-primary').addEventListener('click', function() {
    document.querySelector('.formSua').style.display = 'none';
});


    // Xóa sản phẩm khỏi hàng đợi
    function removeProduct(id) {
        // Lọc sản phẩm theo ID và loại bỏ sản phẩm khỏi productList
        productList = productList.filter(product => product.id !== id);
        updateProductList(); // Cập nhật lại bảng
    }

    document.querySelector('.formSua button[type="button"]').addEventListener('click', function() {
    // Ẩn form khi nhấn Đóng, không xóa dữ liệu
    document.querySelector('.formSua').style.display = 'none';
});


    // Thêm sản phẩm vào danh sách
    document.getElementById('add_product').addEventListener('click', function () {
        const productName = document.getElementById('txtTen').value;
        const productDescription = document.getElementById('txtMota').value;
        const categoryId = document.getElementById('cbLoai').value;
        const categoryName = document.getElementById('cbLoai').options[document.getElementById('cbLoai').selectedIndex].text;
        const productPrice = document.getElementById('txtGia').value;
        const supplierId = document.getElementById('supplier_id').value;
        const supplierName = document.getElementById('supplier_id').options[document.getElementById('supplier_id').selectedIndex].text;
        const userId = document.getElementById('user_id').value;
        const thongbaoLoi = document.querySelector('.thongbaoLoi');
        const loiNe = thongbaoLoi.querySelector('p');
        const priceValue = parseFloat(productPrice);
        let loi = "";
        const ptgg = document.getElementById('txtPT').value;


if (!supplierId) {
    loi = "Không được để trống nhà cung cấp";
} else if (!productName) {
    loi = "Không được để trống tên sản phẩm";
} else if (!productDescription) {
    loi = "Không được để trống mô tả sản phẩm";
} else if (!categoryId) {
    loi = "Không được để trống loại sản phẩm";
} else if (!productPrice) {
    loi = "Không được để trống giá sản phẩm";
} else if (isNaN(priceValue)) {
    loi = "Giá sản phẩm phải là dạng số";
} else if (priceValue < 0) {
    loi = "Giá sản phẩm phải là số dương";
}else if(!ptgg)
{
    loi = "Phần trăm không được để trống"
}else if(isNaN(ptgg))
{
    loi = "Phần trăm phải là dạng số";
}else if(ptgg<0)
{
    loi = "Phần trăm phải là số dương";
}else if(ptgg > 1000)
{
    loi = "Phần trăm không được vượt quá 1000";
}

        if(loi === "")
        {
            productList.push({
                id: ++productCount,
                supplier_id: supplierId,
                supplier: supplierName,
                user_id: userId,
                name: productName,
                description: productDescription,
                category_id: categoryId,
                category: categoryName,
                price: productPrice,
                ptgg: ptgg
            });
            updateProductList();
            document.getElementById('txtTen').value = '';
            document.getElementById('txtMota').value = '';
            document.getElementById('cbLoai').value = '';
            document.getElementById('txtGia').value = '';


        } else {
            loiNe.textContent = loi;
            thongbaoLoi.style.display = "block";
            thongbaoLoi.classList.add("show");      
            setTimeout(() => thongbaoLoi.classList.remove('show'), 2000);            
        }
    });

    // Khi nhấn "Lưu phiếu nhập"
    document.getElementById('formNhapPhieuNhap').addEventListener('submit', function (event) {
        event.preventDefault();  // Ngừng hành động mặc định của form

        let totalPrice = 0;
        productList.forEach(product => {
            totalPrice += parseFloat(product.price);
        });

        const supplierId = document.getElementById('supplier_id').value;
        const userId = document.getElementById('user_id').value;

        // Gửi AJAX để lưu phiếu nhập vào cơ sở dữ liệu
        const data = new FormData();
        data.append('supplier_id', supplierId);
        data.append('user_id', userId);
        data.append('total_price', totalPrice);
        data.append('products', JSON.stringify(productList)); // Gửi danh sách sản phẩm dưới dạng JSON

        fetch('../ajax/insertPhieuNhap.php', {
            method: 'POST',
            body: data
        })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                const tbTC = document.querySelector(".thongbaoLuuThanhCong");
                tbTC.style.display = "block";
                tbTC.classList.add("show");
                setTimeout(() => tbTC.classList.remove('show'), 2000);
                productList = [];
                updateProductList();
                document.getElementById('formNhapPhieuNhap').reset();
                loadPhieuNhap();
            } else {
                const tbTB = document.querySelector(".thongbaoLuuKhongThanhCong");
                tbTB.style.display = "block";
                tbTB.classList.add("show");
                setTimeout(() => tbTB.classList.remove('show'), 2000);
            }
        })
        .catch(error => {
            alert('Có lỗi xảy ra khi gửi yêu cầu.');
        });
    });
    function loadPhieuNhap(page = 1) {
    fetch('../ajax/quanlyPhieuNhap_ajax.php?pageproduct=' + page)
        .then(res => res.json())
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
                            // Gửi yêu cầu xóa sản phẩm qua AJAX
                            fetch("../ajax/deletePhieuNhap.php", {
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
                                    loadPhieuNhap(currentPage);
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

    fetch('../ajax/updatePhieuNhap.php', {
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

