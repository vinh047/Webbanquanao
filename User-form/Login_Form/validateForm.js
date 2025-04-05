function validateForm() {
    const form = document.getElementById("mainformmainform");
    let isValid = true;
    let messages = [];
  
    // Regex chuẩn
    const usernameRegex = /^[a-zA-Z0-9_]{4,20}$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    const passwordRegex = /^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*]).{6,}$/;
    const phoneRegex = /^0\d{9}$/;
  
    if (trangthai === "dangky") {
      const username = form.querySelector('[name="username"]')?.value.trim();
      const email = form.querySelector('[name="email"]')?.value.trim();
      const password = form.querySelector('[name="pswd"]')?.value.trim();
      const sdt = form.querySelector('[name="sdt"]')?.value.trim();
      const diachi = form.querySelector('[name="diachi"]')?.value.trim();
  
      if (!username || !email || !password || !sdt || !diachi) {
        messages.push("Vui lòng nhập đầy đủ tất cả các trường.");
      } else {
        if (!usernameRegex.test(username)) messages.push("Username phải từ 4-20 ký tự, không dấu.");
        if (!emailRegex.test(email)) messages.push("Email không hợp lệ.");
        if (!passwordRegex.test(password)) messages.push("Mật khẩu ít nhất 6 ký tự, 1 chữ hoa, 1 số, 1 ký tự đặc biệt.");
        if (!phoneRegex.test(sdt)) messages.push("Số điện thoại không hợp lệ (bắt đầu bằng 0, đủ 10 số).");
      }
  
    } else if (trangthai === "dangnhap") {
      const email = form.querySelector('[name="email"]')?.value.trim();
      const password = form.querySelector('[name="pswd"]')?.value.trim();
  
      if (!email && !password) {
        messages.push("Vui lòng nhập email và mật khẩu.");
      }

      else {
        if(!email){
            messages.push("Vui long nhap email");

        }
        else if (! password){
            messages.push("Vui long nhap mat khau");
        }

        else if (!emailRegex.test(email)) messages.push("Email không hợp lệ.");
      }
    }
  
    // Nếu có lỗi -> hiện alert + không submit
    if (messages.length > 0) {
      alert(messages.join("\n"));
      isValid = false;
    }
  
    return isValid;
  }
  