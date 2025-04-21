<?php

$menu = [
  [
    'label' => 'ÁO',
    'subs'  => [
      ['id'=>1, 'label'=>'Áo thun'],
      ['id'=>3, 'label'=>'Áo sơ mi'],
      ['id'=>4, 'label'=>'Áo polo'],
      ['id'=>5, 'label'=>'Áo khoác'],
    ],
  ],
  [
    'label' => 'QUẦN',
    'subs'  => [
      ['id'=>2, 'label'=>'Quần'],
      ['id'=>2, 'label'=>'Quần short'],
      ['id'=>6, 'label'=>'Quần lót'],
    ],
  ],
  [
    'label' => 'PHỤ KIỆN',
    'subs'  => [
      ['id'=>7, 'label'=>'Nón'],
      ['id'=>8, 'label'=>'Túi'],
      ['id'=>9, 'label'=>'Thắt lưng'],
    ],
  ],
  [
    'label' => 'SP ƯU ĐÃI',
    'subs'  => [],  //phần này chưa biết làm gì cho submenu :)
  ],
];
?>



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
    <button class="menu-close">
      <i class="fa-solid fa-xmark"></i>
    </button>

    <ul class="mb-0">
      <?php foreach($menu as $index => $item): 
        // Mỗi mục cha sẽ có pageproduct = $index + 1
        $parentId = $index + 1;
      ?>
        <li>
          <a href="index.php?page=sanpham&amp;pageproduct=<?= $parentId ?>">
            <?= htmlspecialchars($item['label']) ?>
            <?php if (!empty($item['subs'])): ?>
              <i class="fa-solid fa-chevron-down"></i>
            <?php endif; ?>
          </a>

          <?php if (!empty($item['subs'])): ?>
            <ul class="submenu">
              <?php foreach($item['subs'] as $sub): ?>
                <li>
                  <a href="index.php?page=sanpham&amp;pageproduct=<?= $parentId ?>&amp;selectTheloai=<?= $sub['id'] ?>">
                    <?= htmlspecialchars($sub['label']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
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