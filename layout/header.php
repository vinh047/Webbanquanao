<?php
session_start();
require_once __DIR__ . '/../database/DBConnection.php';

// Kết nối DB
$db = DBConnect::getInstance();

// Lấy danh sách categories
$categories = $db->select('SELECT * FROM categories', []);

// Build menu chính
$menu = [
    ['label' => 'ÁO',        'subs' => []],
    ['label' => 'QUẦN',      'subs' => []],
    ['label' => 'PHỤ KIỆN',  'subs' => []],
];
foreach ($categories as $cat) {
    $nameLower = mb_strtolower($cat['name'], 'UTF-8');
    $item = [
        'id'    => $cat['category_id'],
        'label' => $cat['name'],
    ];
    if (strpos($nameLower, 'áo') !== false) {
        $menu[0]['subs'][] = $item;
    } elseif (strpos($nameLower, 'quần') !== false) {
        $menu[1]['subs'][] = $item;
    } elseif (strpos($nameLower, 'phụ kiện') !== false) {
        $menu[2]['subs'][] = $item;
    }
}

// Khởi tạo $user luôn tồn tại
$user = null;
if (!empty($_SESSION['user_id'])) {
    // Lấy thông tin user (bạn có thể thay đổi SELECT cho phù hợp)
    $user = $db->selectOne(
        'SELECT user_id, username FROM users WHERE user_id = ?',
        [$_SESSION['user_id']]
    );
}
?>
<header class="header">

    <button class="menu-toggle">
        <i class="fa-solid fa-bars"></i>
    </button>

    <div class="logo_header">
        <a href="/index.php">
            <!-- Dùng đường dẫn tuyệt đối -->
            <img src="/assets/img/logo_favicon/logo.png" alt="SAGKUTO Logo">
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
                        <?= htmlspecialchars($item['label'], ENT_QUOTES) ?>
                        <?php if (!empty($item['subs'])): ?>
                            <i class="fa-solid fa-chevron-down"></i>
                        <?php endif; ?>
                    </span>
                    <?php if (!empty($item['subs'])): ?>
                        <ul class="submenu">
                            <?php foreach ($item['subs'] as $sub): ?>
                                <li>
                                    <a href="/index.php?page=sanpham&pageproduct=1&selectTheloai=<?= $sub['id'] ?>">
                                        <?= htmlspecialchars($sub['label'], ENT_QUOTES) ?>
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
        <a href="/search.html" class="icon"><i class="fa-solid fa-magnifying-glass"></i></a>
        <a href="<?= $user
            ? '/tai-khoan.php'
            : '/User-form/Login_Form/Login_Form.php'
        ?>" class="icon account-link">
            <i class="fa-solid fa-user"></i>
            <span>
                <?= htmlspecialchars($user['username'] ?? 'Tài khoản', ENT_QUOTES) ?>
            </span>
        </a>
    </div>

</header>

<script>
    document.querySelector('.menu-toggle').addEventListener('click', () => {
        document.querySelector('.menu_header').classList.toggle('active');
    });
    document.querySelector('.menu-close').addEventListener('click', () => {
        document.querySelector('.menu_header').classList.remove('active');
    });
</script>
