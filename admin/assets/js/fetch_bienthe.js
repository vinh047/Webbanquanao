document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formNhapSPbienThe");
    const thongbao = document.querySelector(".thongbaoLoi");
    const loi = thongbao.querySelector("p");
    const tbThanhCong = document.querySelector(".thongbaoThanhCong");
    const tc = tbThanhCong.querySelector("p");

    function fetchBienThe(page = 1) {
        fetch(`../ajax/quanlyBienThe_ajax.php?pageproduct=${page}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById("product-list").innerHTML = data.products;
                document.getElementById("pagination").innerHTML = data.pagination;

                document.querySelectorAll(".page-link-custom").forEach(btn => {
                    btn.addEventListener("click", function (e) {
                        e.preventDefault();
                        fetchBienThe(this.dataset.page);
                    });
                });
            })
            .catch(err => console.error("Lỗi khi fetch biến thể:", err));
    }

    fetchBienThe(); // load ban đầu

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


        fetch(`../ajax/checkID.php?product_id=${idsp}`)
            .then(res => res.json())
            .then(data => {
                if (!data.exists) {
                    loi.textContent = "Mã sản phẩm không tồn tại!";
                    return showError();
                }

                // ✅ Nếu hợp lệ và tồn tại, tiếp tục thêm
                const formData = new FormData(form);
                fetch('../ajax/insertBienThe.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchBienThe();
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

    
});
function showError() {
    thongbao.style.display = 'block';
    thongbao.classList.add('show');
    setTimeout(() => thongbao.classList.remove('show'), 2000);
}