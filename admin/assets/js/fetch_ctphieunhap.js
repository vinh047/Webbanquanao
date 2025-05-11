let productList = [];
let productCount = 0;
let productPendingToAdd = null; 
const formLoc = document.getElementById("formLoc");
const permissionsElement = document.getElementById('permissions');
let permissions = [];
let currentPage = 1;
document.addEventListener('DOMContentLoaded', function () {
    phanQuyen();
    adjustPageIfLastItem();

    const params = new URLSearchParams(window.location.search);
    const pageFromURL = parseInt(params.get('pageadmin')) || 1;
    currentPage = pageFromURL;

    // ✅ Đổ lại filter vào form trước khi load dữ liệu
    for (let [key, value] of params.entries()) {
        const el = document.querySelector(`[name="${key}"]`);
        if (el) {
            el.value = value;
            if ($(el).hasClass('select2')) {
                $(el).val(value).trigger('change');
            }
        }
    }

    // ✅ Gọi sau khi đã đổ filter
    loadctPhieuNhap(currentPage);

    // ✅ Gán các sự kiện lọc
    locctPhieuNhap();
});

function phanQuyen()
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
function adjustPageIfLastItem() {
    const btnCount = document.querySelectorAll(".btn-sua").length;
    if (btnCount === 1 && currentPage > 1) {
        currentPage -= 1;
    }
}
function loadctPhieuNhap(page = 1) {
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
            phanTrang();
            xacNhanCho();
            xemChiTiet();
        });

        
}
function updateUrlWithPage(page) {
    const url = new URL(window.location.href);
    url.searchParams.set('pageadmin', page); // cập nhật hoặc thêm mới
    window.history.pushState({}, '', url);    // thay đổi URL trên trình duyệt
}
function phanTrang()
{
    document.querySelectorAll(".page-link-custom").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            currentPage = parseInt(this.dataset.page);
            updateUrlWithPage(currentPage);
            loadctPhieuNhap(currentPage);
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
                    loadctPhieuNhap(page);
                }
            }
        });
    }
}
function xacNhanCho()
{
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
                            loadctPhieuNhap(currentPage); // ✅ reload bảng
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
}
function xemChiTiet()
{
    document.querySelectorAll('.btn-xemchitiet').forEach(button => {
        button.addEventListener('click', async function () {
          const id = this.dataset.idct;
          try {
            const res = await fetch(`./ajax/infoCTPN.php?id=${id}`);
            const text = await res.text();
            // console.log("Kết quả trả về:", text);
      
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
      

            if (!permissions.includes('read')) {
            const tBquyen = document.querySelector('.thongBaoQuyen');
            tBquyen.style.display = 'block';
            tBquyen.classList.add('show');
            setTimeout(() => tBquyen.classList.remove('show'), 2000);
            return; 
        }
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
}
function locctPhieuNhap() {
    formLoc.addEventListener("submit", function (e) {
        e.preventDefault();
        currentPage = 1;

        // 1. Thu thập các filter từ form
        const formData = new FormData(formLoc);
        const filters = [];

        for (let [key, value] of formData.entries()) {
            if (value) {
                filters.push(`${encodeURIComponent(key)}=${encodeURIComponent(value)}`);
            }
        }

        // 2. Tạo URL mới
        const queryParts = [
            'page=ctphieunhap',
            ...filters,
            `pageadmin=${currentPage}`
        ];
        const newUrl = `${location.pathname}?${queryParts.join('&')}`;
        window.history.pushState({}, '', newUrl); // Cập nhật URL mà không reload

        // 3. Gọi lại load
        loadctPhieuNhap(currentPage);
    });

    // Toggle form lọc
    document.getElementById('filter-icon').addEventListener('click', function () {
        const filterBox = document.querySelector('.filter-loc');
        filterBox.classList.toggle('d-none');
    });

    document.getElementById('tatFormLoc').addEventListener('click', function () {
        const filterBox = document.querySelector('.filter-loc');
        filterBox.classList.toggle('d-none');
    });
}
