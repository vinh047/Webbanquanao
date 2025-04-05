<?php
echo '
<header class="header">
  <div class="logo_header">
    <a href="index.php">
      <img src="./assets/img/logo_favicon/logo.png" alt="SAGKUTO Logo">
    </a>
  </div>

  <nav class="menu_header">
    <ul class="main-menu mb-0">

      <li class="menu-item">
        <input type="checkbox" id="menu-ao" hidden>
        <label for="menu-ao" class="menu-label">
          ÁO <i class="fa-solid fa-chevron-down"></i>
        </label>
        <ul class="submenu">
          <li><a href="index.php?page=sanpham&phanloai=aopolo">Áo Polo</a></li>
          <li><a href="index.php?page=sanpham&phanloai=aosomi">Áo sơ mi</a></li>
          <li><a href="index.php?page=sanpham&phanloai=aokhoac">Áo khoác</a></li>
        </ul>
      </li>

      <li class="menu-item">
        <input type="checkbox" id="menu-quan" hidden>
        <label for="menu-quan" class="menu-label">
          QUẦN <i class="fa-solid fa-chevron-down"></i>
        </label>
        <ul class="submenu">
          <li><a href="index.php?page=sanpham&phanloai=quanjean">Quần jean</a></li>
          <li><a href="index.php?page=sanpham&phanloai=quanshort">Quần short</a></li>
        </ul>
      </li>

      <li class="menu-item">
        <input type="checkbox" id="menu-phukien" hidden>
        <label for="menu-phukien" class="menu-label">
          PHỤ KIỆN <i class="fa-solid fa-chevron-down"></i>
        </label>
        <ul class="submenu">
          <li><a href="index.php?page=sanpham&phanloai=non">Nón</a></li>
          <li><a href="index.php?page=sanpham&phanloai=tui">Túi</a></li>
        </ul>
      </li>

      <li>
        <a href="index.php?page=sanpham&phanloai=uudai">SP ƯU ĐÃI</a>
      </li>
    </ul>
  </nav>

  <div class="icons_header">
    <i class="fa-solid fa-magnifying-glass"></i>
    <i class="fa-solid fa-user"></i>
  </div>
</header>
';
?>
