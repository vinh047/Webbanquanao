const form = document.getElementById("formNhapSP");
const formSua = document.getElementById("formSua");
const tbLoai = document.querySelector(".thongbaoLoi");
const loi = tbLoai.querySelector("p");
const tbLoaiThanhCong = document.querySelector(".thongbaoThanhCong");
const tc = tbLoaiThanhCong.querySelector("p");
const formLoc = document.getElementById("formLoc");
let currentPage = 1;
let permissions = [];

document.addEventListener('DOMContentLoaded', function () {
    // Xử lý phân quyền người dùng
    getPhanQuyen();

    // Chuyển trang khi chỉ còn 1
    adjustPageIfLastItem();

    // Xử lý ajax sản phẩm (thêm, sửa, xóa)
    fetchSanPham();

    // Xử lý lọc sản phẩm
    locsanpham();

    // Thêm sản phẩm
    themsanpham();

    // Sửa sản phẩm
    suasanpham();

    // Tự động tăng giá bán khi sửa đổi thông tin như giá nhập || %
    tudongtanggia();
    const btnThemSanPhamMoi = document.getElementById('btnThemSanPhamMoi');
    if (btnThemSanPhamMoi) {
        btnThemSanPhamMoi.addEventListener('click', function () {
            const modalElement = document.getElementById('modalNhapSanPham');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        });
    }

});

function getPhanQuyen()
{
    const permissionsElement = document.getElementById('permissions');
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
}

function adjustPageIfLastItem() {
    const btnCount = document.querySelectorAll(".btn-sua").length;
    if (btnCount === 1 && currentPage > 1) {
        currentPage -= 1;
    }
}

function fetchSanPham(page = 1) {
    const formData = new FormData(formLoc);
    formData.append("pageproduct", page); // giữ phân trang
    fetch(`./ajax/quanlySanPham_ajax.php`,{
        method : "POST",
        body : formData
    })
        .then(res => res.json())
        .then(data => {
            document.getElementById("product-list").innerHTML = data.products;
            document.getElementById("pagination").innerHTML = data.pagination;

            phantrang();
            chitietsanpham();
            hienthisua();
            xoasanpham();
        });
}

fetchSanPham();


function phantrang()
{
                // Phân trang
                document.querySelectorAll(".page-link-custom").forEach(btn => {
                    btn.addEventListener("click", function (e) {
                        e.preventDefault();
                        console.log("Page clicked:", this.dataset.page); // 👈 THÊM DÒNG NÀY
    
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
                                currentPage = page;
                                fetchSanPham(page); // ✅ đúng
                            }
                        }
                    });
                }
}

function chitietsanpham()
{
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("btn-xemchitietPN")) {
          const idsp = e.target.dataset.idpn;
          let idspGlobal = idsp;
      
          function renderChiTietSanPham(data) {
            const tbody = document.querySelector('#chitiet-phieunhap tbody');
            tbody.innerHTML = '';
            const currentPage = data.pagination?.current || 1;
            const totalPages = data.pagination?.total || 1;
      
            data.variants.forEach((item, index) => {
              const row = document.createElement('tr');
              row.innerHTML = `
                <td class="text-center">${(currentPage - 1) * 5 + index + 1}</td>
                <td class="text-center">${item.variant_id}</td>
                <td>${item.product_name}</td>
                <td class="text-center">${item.size}</td>
                <td class="text-center">${item.color}</td>
                <td class="text-center"><img src="../../assets/img/sanpham/${item.image}" style="height: 100px;"></td>
                <td class="text-center">${item.stock}</td>
              `;
              tbody.appendChild(row);
            });
      
            const info = data.info;
            if (info) {
              document.getElementById('idSP').textContent = info.product_id;
              document.getElementById('tenNSP').textContent = info.name;
              document.getElementById('loaiSP').textContent = info.category;
              document.getElementById('motaSP').textContent = info.description;
              document.getElementById('gianhapSP').textContent = Number(info.price).toLocaleString('vi-VN');
              document.getElementById('giabanSP').textContent = Number(info.price_sale).toLocaleString('vi-VN');
              document.getElementById('pttgSP').textContent = Number(info.pttg).toLocaleString('vi-VN');
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
            fetch(`./ajax/chi_tiet_san_pham.php`, {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: `product_id=${idspGlobal}&page=${page}`
            })
            .then(res => res.text())
            .then(text => {
            //   console.log("Raw response:", text); // ← kiểm tra HTML lỗi gì
              try {
                const data = JSON.parse(text);
                renderChiTietSanPham(data); // vẫn dùng hàm cũ nếu đúng JSON
                const modalElement = document.getElementById('modalChiTietSP');
                const existingModal = bootstrap.Modal.getOrCreateInstance(modalElement);
                existingModal.show();
              } catch (e) {
                console.error("❌ JSON parse failed:", e);
              }
            });
          }
      
          fetchPage(1);
        }
      });
      
      document.getElementById('modalChiTietSP').addEventListener('hidden.bs.modal', function () {
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style = '';
      });
}
function hienthisua() {
    const formSua = document.getElementById('formSua');

    document.querySelectorAll(".btn-sua").forEach(btn => {
        btn.addEventListener("click", function () {
            const id = this.dataset.id;
            const ten = this.dataset.ten;
            const mota = this.dataset.mota;
            const gia = this.dataset.gia;
            const giaban = this.dataset.giaban;
            const loai = this.dataset.loaiid;
            const pttg = this.dataset.pttg;

            formSua.querySelector("input[name='id']").value = id;
            formSua.querySelector("input[name='ten']").value = ten;
            formSua.querySelector("textarea[name='mota']").value = mota;
            formSua.querySelector("select[name='loai']").value = loai;
            formSua.querySelector("input[name='gia']").value = parseFloat(gia).toLocaleString('vi-VN');
            formSua.querySelector("input[name='giaban']").value = parseFloat(giaban).toLocaleString('vi-VN');
            formSua.querySelector("input[name='pttg']").value = parseFloat(pttg);

            // Lưu giá trị gốc vào dataset (nếu cần)
            formSua.dataset.giaNhapCu = parseFloat(gia.replace(/\./g, "").replace(",", "."));
            formSua.dataset.giaBanCu = parseFloat(giaban.replace(/\./g, "").replace(",", "."));

            // ✅ Chỉ mở modal sau khi click
            const modalSuaSanPham = new bootstrap.Modal(document.getElementById('modalSuaSanPham'));
            modalSuaSanPham.show();
        });
    });
}


function xoasanpham()
{
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
                            if (!permissions.includes('delete')) {
                                const tBquyen = document.querySelector('.thongBaoQuyen');
                                tBquyen.style.display = 'block';
                                tBquyen.classList.add('show');
                                popup.style.display = "none";
                                document.querySelector(".overlay").style.display = "none";
    
                                setTimeout(() => tBquyen.classList.remove('show'), 2000);
                                return; 
                            }
                            fetch("./ajax/deleteSanPham.php", {
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
                                        adjustPageIfLastItem();
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
}

function locsanpham()
{
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
}

function themsanpham()
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

function suasanpham()
{
    formSua.addEventListener("submit", function (e) {
        e.preventDefault();
        const ten = document.getElementById("txtTenSua").value.trim();
        const mota = document.getElementById("txtMotaSua").value.trim();
        const cbLoai = document.getElementById("cbLoaiSua").value.trim();
        const pttg = parseFloat(document.getElementById("txtPttg").value.trim());
        const giaBanDau = parseFloat(document.getElementById("txtGiaSua").value.replace(/\./g, "").replace(",", "."));
        const giaBanNe = parseFloat(document.getElementById("txtGiaBanSua").value.replace(/\./g, "").replace(",", "."));
        const gia = parseFloat(document.getElementById("txtGiaSua").value.replace(/\./g, "").replace(",", "."));
        const giaban = parseFloat(document.getElementById("txtGiaBanSua").value.replace(/\./g, "").replace(",", "."));
        const tbLoi = document.querySelector(".thongbaoLoi");
        const loiTB = tbLoi.querySelector("p");
        let loi = "";

        if (!permissions.includes('update')) {
            const tBquyen = document.querySelector('.thongBaoQuyen');
            tBquyen.style.display = 'block';
            tBquyen.classList.add('show');
            setTimeout(() => tBquyen.classList.remove('show'), 2000);
            document.querySelector('.formSua').style.display = 'none';
            document.querySelector('.overlay').style.display = 'none';
            return; 
        }



        if(!ten)
        {
            loi = "Không được để trống tên sản phẩm";
            loiTB.textContent = loi;
            tbLoi.style.display = 'block';
            tbLoi.classList.add('show');
            setTimeout(() => tbLoi.classList.remove('show'), 2000);
            document.getElementById("txtTenSua").focus();

            return;
        }

        if(!mota)
        {
            loi = "Không được để trống mô tả phẩm";
            loiTB.textContent = loi;
            tbLoi.style.display = 'block';
            tbLoi.classList.add('show');
            setTimeout(() => tbLoi.classList.remove('show'), 2000);
            document.getElementById("txtMotaSua").focus();
            return;
        }

        if(!cbLoai)
        {
            loi = "Không được để trống loại sản phẩm";
            loiTB.textContent = loi;
            tbLoi.style.display = 'block';
            tbLoi.classList.add('show');
            setTimeout(() => tbLoi.classList.remove('show'), 2000);
            document.getElementById("cbLoaiSua").focus();
            return;
        }


        if(!giaBanDau)
        {
            loi = "Không được để trống giá nhập";
            loiTB.textContent = loi;
            tbLoi.style.display = 'block';
            tbLoi.classList.add('show');
            setTimeout(() => tbLoi.classList.remove('show'), 2000);
            document.getElementById("txtGiaSua").focus();
            return;
        }

        if(!pttg)
        {
            loi = "Không được để trống phần trăm tăng giá";
            loiTB.textContent = loi;
            tbLoi.style.display = 'block';
            tbLoi.classList.add('show');
            setTimeout(() => tbLoi.classList.remove('show'), 2000);
            document.getElementById("txtPttg").focus();
            return;
        }

        if(isNaN(pttg))
        {
            loi = "Phần trăm tăng giá phải là số dương";
            loiTB.textContent = loi;
            tbLoi.style.display = 'block';
            tbLoi.classList.add('show');
            setTimeout(() => tbLoi.classList.remove('show'), 2000);
            document.getElementById("txtPttg").focus();

            return;
        }

        if(pttg < 0)
        {
            loi = "Phần trăm tăng giá phải là số dương";
            loiTB.textContent = loi;
            tbLoi.style.display = 'block';
            tbLoi.classList.add('show');
            setTimeout(() => tbLoi.classList.remove('show'), 2000);
            document.getElementById("txtPttg").focus();

            return;
        }

        if(!giaBanNe)
        {
            loi = "Không được để trống giá bán";
            loiTB.textContent = loi;
            tbLoi.style.display = 'block';
            tbLoi.classList.add('show');
            setTimeout(() => tbLoi.classList.remove('show'), 2000);
            document.getElementById("txtGiaBanSua").focus();
            return; 
        }

        if(isNaN(giaBanDau)  || isNaN(giaBanNe))
        {
            loi = "Sai định dạng giá";
            loiTB.textContent = loi;
            tbLoi.style.display = 'block';
            tbLoi.classList.add('show');
            setTimeout(() => tbLoi.classList.remove('show'), 2000);
            return;  
        }

        if(giaBanDau <= 0 || giaBanNe <= 0)
        {
            loi = "GIá phải lớn hơn 0";
            loiTB.textContent = loi;
            tbLoi.style.display = 'block';
            tbLoi.classList.add('show');
            setTimeout(() => tbLoi.classList.remove('show'), 2000);
            return; 
        }
        // if (gia === null || giaban === null) return;
    
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
}

function sendCapNhatSanPham(gia, giaban) {
    const formData = new FormData(formSua);
    formData.set("gia", gia);
    formData.set("giaban", giaban);

    fetch("./ajax/updateSanPham.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {

            const tbUpdate = document.querySelector(".thongbaoUpdateThanhCong");
            tbUpdate.style.display = "block";
            tbUpdate.classList.add("show");
            setTimeout(() => tbUpdate.classList.remove('show'), 2000);
            const modalElement = document.getElementById('modalSuaSanPham');
const modalInstance = bootstrap.Modal.getInstance(modalElement);
if (modalInstance) {
    modalInstance.hide();
}

            adjustPageIfLastItem();
            fetchSanPham(currentPage);
        } else {
            alert(data.message || "Lỗi cập nhật");
        }
    });
}

function tinhGiaBanTuDong() {
    const giaNhapVal = document.getElementById("txtGiaSua").value.replace(/\./g, "").replace(",", ".");
    const pttgVal = document.getElementById("txtPttg").value.replace(",", ".");

    const giaNhap = parseFloat(giaNhapVal);
    const pttg = parseFloat(pttgVal);

    if (!isNaN(giaNhap) && !isNaN(pttg)) {
        const giaBan = giaNhap * (1 + pttg / 100);
        document.getElementById("txtGiaBanSua").value = Math.round(giaBan); // hoặc toFixed(0)
    }
}

function tudongtanggia()
{
        // Gắn sự kiện tự động tính khi nhập giá nhập hoặc phần trăm
        document.getElementById("txtGiaSua").addEventListener("input", tinhGiaBanTuDong);
        document.getElementById("txtPttg").addEventListener("input", tinhGiaBanTuDong);
}

    