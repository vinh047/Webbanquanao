<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    // Lấy thông tin user và vai trò
    $user = $db->selectOne(
        'SELECT user_id, name, role_id FROM users WHERE user_id = ?',
        [$_SESSION['user_id']]
    );

    // Nếu role_id không phải là 1 (admin), huỷ session
    if (!$user || !isset($user['role_id']) || $user['role_id'] != 1) {
        session_unset();     // Xoá toàn bộ biến session
        session_destroy();   // Huỷ phiên đăng nhập
        $user = null;        // Đặt lại user để tránh dùng sai
    }
}

?>
<link rel="stylesheet" href="../assets/css/basic_search.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=shopping_cart" />
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
		<!-- Search icon -->
		<a href="#" class="icon" id="openSearch">
			<i class="fa-solid fa-magnifying-glass"></i>
		</a>

		<div id="searchOverlay" class="search-overlay">
			<div class="search-box">
				<button class="close-btn" id="closeSearch">&times;</button>
				<input type="text" id="searchInput" placeholder="Bạn tìm kiếm gì hôm nay...">
				<div id="searchResultBox"></div>
			</div>
		</div>

		<?php if ($user): ?>
			<!-- Khi click icon user sẽ redirect thẳng tới info_user.php -->
			<a href="/index.php?page=taikhoan" class="icon account-link">
				<i class="fa-solid fa-user"></i>
				<span><?= htmlspecialchars($user['name'], ENT_QUOTES) ?></span>
			</a>
		<?php else: ?>
			<a href="/User-form/Login_Form/Login_Form.php" class="icon account-link">
				<i class="fa-solid fa-user"></i>
				<span>Đăng nhập</span>
			</a>
		<?php endif; ?>


		<!-- Cart toggle -->
		<div class="position-relative" style="margin-top: 10px;">
			<a href="javascript:void(0);" id="toggle-cart" title="Giỏ hàng" class="text-dark">
				<span class="material-symbols-outlined" style="font-size: 27px;">
					shopping_cart
				</span>
				<span id="cart-count-badge"
					class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
					style="font-size: 12px;">
					0
				</span>
			</a>
		</div>

		<!-- Mini cart sidebar -->
		<div id="mini-cart"
 			class="d-none bg-white shadow p-3 rounded position-fixed end-0 top-0 flex-column"
  			style="width: 300px; height: 100vh; z-index: 9999;">

			<h6 class="mb-3">
				Sản phẩm trong giỏ (<span id="cart-item-count">0</span>)
			</h6>
			<div id="mini-cart-items" class="flex-grow-1 overflow-auto"></div>
			<div id="mini-cart-footer" class="mt-3">
				<a href="/index.php?page=giohang" class="btn btn-dark w-100 mb-2">
					Xem giỏ hàng
				</a>
				<button id="close-mini-cart" class="btn btn-outline-secondary w-100">
					Đóng
				</button>
			</div>
		</div>

		<!-- Add-to-cart notification -->
		<div id="noticeAddToCart"
			class="notice-add-to-cart position-fixed top-50 start-50 translate-middle
                d-flex flex-column justify-content-center align-items-center p-4
                rounded w-auto opacity-0"
			style="background-color: rgba(0, 0, 0, 0.8);
                transition: opacity 0.5s ease;
                z-index: 1050;
                pointer-events: none;">
			<i id="noticeIcon" class="fa-solid fa-circle-check fa-3x mb-3" style="color: #fff;"></i>
			<span id="noticeText" class="text-white text-center fw-bold" style="font-size: 18px;">
				Đã thêm vào giỏ hàng
			</span>
		</div>
	</div>




</header>

<!-- Các script quản lý giỏ hàng và menu -->
<script>
	const user_id = <?= isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null ?>
</script>
<script src="/ajax/generate_color_map.php"></script>
<script src="/assets/js/cart.js"         defer></script>
<script src="/assets/js/header.js"       defer></script>
<script src="../assets/js/basic_search_overlay.js"></script>
<script src="../assets/js/basic_search_ui.js"></script>
<script src="../assets/js/basic_search_logic.js"></script>
<?php if (!empty($_SESSION['user_id'])): ?>
<script>
console.log("🟡 Kiểm tra syncCartAfterLogin trong header.php");

window.addEventListener('load', () => {
  console.log("🟢 window loaded, checking syncCartAfterLogin...");
  if (typeof syncCartAfterLogin === 'function' && sessionStorage.getItem('cart_merge_prompted') === '0') {
    console.log("✅ Gọi syncCartAfterLogin()");
    syncCartAfterLogin();
  } else {
    console.log("❌ Không gọi được syncCartAfterLogin hoặc đã hỏi rồi.");
  }
});
</script>
<?php endif; ?>

<?php if (isset($_GET['loggedout']) && $_GET['loggedout'] == '1'): ?>
<script>
  sessionStorage.setItem('cart_merge_prompted', '0');
</script>
<?php endif; ?>
