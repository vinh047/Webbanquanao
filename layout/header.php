<header class="header">

  <button class="menu-toggle">
    <i class="fa-solid fa-bars"></i>
  </button>

  <div class="logo_header">
  <a href="index.php">
    <img src="../assets/img/logo_favicon/logo.png" alt="SAGKUTO Logo">
  </a>
  </div>
  
  <nav class="menu_header">
    <!-- Nút đóng menu -->
    <button class="menu-close">
      <i class="fa-solid fa-xmark"></i>
    </button>


    <ul class="mb-0">
      <li>
        <a href="index.php?page=sanpham&phanloai=ao">ÁO <i class="fa-solid fa-chevron-down"></i></a>
        <ul class="submenu">
          <li><a href="index.php?page=sanpham&phanloai=aopolo">Áo Polo</a></li>
          <li><a href="index.php?page=sanpham&phanloai=aosomi">Áo sơ mi</a></li>
          <li><a href="index.php?page=sanpham&phanloai=aokhoac">Áo khoác</a></li>
        </ul>
      </li>
      <li>
        <a href="index.php?page=sanpham&phanloai=quan">QUẦN <i class="fa-solid fa-chevron-down"></i></a>
        <ul class="submenu">
          <li><a href="index.php?page=sanpham&phanloai=quanjean">Quần jean</a></li>
          <li><a href="index.php?page=sanpham&phanloai=quanshort">Quần short</a></li>
        </ul>
      </li>
      <li>
        <a href="index.php?page=sanpham&phanloai=phukien">PHỤ KIỆN <i class="fa-solid fa-chevron-down"></i></a>
        <ul class="submenu">
          <li><a href="index.php?page=sanpham&phanloai=non">Nón</a></li>
          <li><a href="index.php?page=sanpham&phanloai=tui">Túi</a></li>
        </ul>
      </li>
      <li><a href="index.php?page=sanpham&phanloai=uudai">SP ƯU ĐÃI</a></li>
    </ul>
  </nav>

  <div class="icon-group">
    <a href="search.html" class="icon"><i class="fa-solid fa-magnifying-glass"></i></a>
    <a href="User-form\Login_Form\Login_Form.php" class="icon account-link">
      <i class="fa-solid fa-user"></i>
    <span>Tài khoản</span>
    </a>
  </div>

</header>

<script>
  // Nút hamburger (mở menu)
  document.querySelector('.menu-toggle').addEventListener('click', function() {
    document.querySelector('.menu_header').classList.toggle('active');
  });

  // Nút x (đóng menu)
  document.querySelector('.menu-close').addEventListener('click', function() {
    document.querySelector('.menu_header').classList.remove('active');
  });


</script>