const form = document.getElementById("formNhapSPbienThe");
form.addEventListener("submit", function(event) {
    const idsp = document.getElementById("txtMa").value.trim();
    const img = document.getElementById("fileAnh").value;
    const size = document.getElementById("cbSize").value.trim();
    const mau = document.getElementById("cbMau").value;
    const sl = document.getElementById("txtSl").value.trim();
    const thongbao = document.querySelector(".thongbaoLoi");
    const loi = thongbao.querySelector("p"); 
    event.preventDefault();
    
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
    form.submit();

});
document.getElementById("fileAnh").addEventListener("change", function () {
    const file = this.files[0];
    const imgPreview = document.querySelector("#hienthianh img");

    if (file) {
        const imgURL = URL.createObjectURL(file);
        imgPreview.src = imgURL;
        imgPreview.style.display = "block";
    } else {
        imgPreview.src = "";
        imgPreview.style.display = "none";
    }
});