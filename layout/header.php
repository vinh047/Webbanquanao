<?php
session_start();
require_once __DIR__ . '/../database/DBConnection.php';


$db = DBConnect::getInstance();

$categories = $db->select('SELECT * FROM categories', []);

$menu = [
    [
        'label' => 'ÁO',
        'subs'  => [],
    ],
    [
        'label' => 'QUẦN',
        'subs'  => [],
    ],
    [
        'label' => 'PHỤ KIỆN',
        'subs'  => [],
    ],
];

// Xử lý phân loại category vào đúng nhóm
foreach ($categories as $cat) {
    $catName = mb_strtolower($cat['name']); // để không bị lỗi chữ hoa/chữ thường
    $item = [
        'id' => $cat['category_id'],
        'label' => $cat['name'],
    ];

    if (strpos($catName, 'áo') !== false) {
        $menu[0]['subs'][] = $item;
    } elseif (strpos($catName, 'quần') !== false) {
        $menu[1]['subs'][] = $item;
    } elseif (strpos($catName, 'phụ kiện') !== false) {
        $menu[2]['subs'][] = $item;
    }
}


if (isset($_SESSION['user_id']) && ($_SESSION['role_id'] == 1)) {
    $user = $db->selectOne('SELECT * FROM users WHERE user_id = ?', [$_SESSION['user_id']]);
}

?>





<header class="header">

    <button class="menu-toggle">
        <i class="fa-solid fa-bars"></i>
    </button>

    <div class="logo_header">
        <a href="/index.php">
            <img src="../assets/img/logo_favicon/logo.png" alt="SAGKUTO Logo">
        </a>
    </div>

    <nav class="menu_header">
        <button class="menu-close">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <ul class="mb-0">
            <?php foreach ($menu as $item): ?>
                <li>
                    <span class="menu-title">
                        <?= htmlspecialchars($item['label']) ?>
                        <?php if (!empty($item['subs'])): ?>
                            <i class="fa-solid fa-chevron-down"></i>
                        <?php endif; ?>
                    </span>

                    <?php if (!empty($item['subs'])): ?>
                        <ul class="submenu">
                            <?php foreach ($item['subs'] as $sub): ?>
                                <li>
                                    <a href="/index.php?page=sanpham&pageproduct=1&selectTheloai=<?= $sub['id'] ?>">
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
        <a href="User-form/Login_Form/Login_Form.php" class="icon account-link">
            <i class="fa-solid fa-user"></i>
            <span><?= isset($user) ? $user['username'] : 'Tài khoản' ?></span>
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