<?php
$connection = mysqli_connect("localhost", "root", "", "db_web_quanao");
if (!$connection) {
    echo 'Không thể kết nối đến database';
    exit;
}
mysqli_set_charset($connection, 'utf8');

require_once('product_filter_sort.php');
require_once('../layout/phantrang.php');

$limit = 8;
$page = isset($_GET['pageproduct']) ? (int)$_GET['pageproduct'] : 1;

$loc = locSanPham($connection);
$sapxep = str_replace("products.", "p.", sapXepSanPham());
$whereCondition = "v.is_deleted = 0 AND v.stock > 0";
$loc = $loc ? str_replace(["products.", "product_variants.", "WHERE"], ["p.", "v.", "WHERE $whereCondition AND"], $loc) : "WHERE $whereCondition";

$countSQL = "
SELECT COUNT(*) AS total FROM (
    SELECT p.product_id
    FROM products p
    JOIN (SELECT * FROM product_variants WHERE is_deleted = 0 AND stock > 0 GROUP BY product_id) v ON p.product_id = v.product_id
    $loc
    GROUP BY p.product_id
) AS t
";

$countResult = mysqli_query($connection, $countSQL);
if (!$countResult) {
    echo "\u274c Lỗi truy vấn đếm sản phẩm: " . mysqli_error($connection);
    exit;
}
$totalItems = mysqli_fetch_assoc($countResult)['total'];
if ($totalItems == 0) {
    echo 'REDIRECT_TO_HOME';
    exit;
}

$pagination = new Pagination($totalItems, $limit, $page);
$offset = $pagination->offset();

$productSQL = "
SELECT p.*, v.variant_id, v.image
FROM products AS p
JOIN (
    SELECT * FROM product_variants
    WHERE is_deleted = 0 AND stock > 0
    GROUP BY product_id
) AS v ON p.product_id = v.product_id
$loc
$sapxep
LIMIT $limit OFFSET $offset
";

$result = mysqli_query($connection, $productSQL);
if (!$result) {
    echo "\u274c Lỗi truy vấn sản phẩm: " . mysqli_error($connection);
    exit;
}

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['product_id'];
    $name = $row['name'];
    $gia = number_format($row['price_sale'], 0, ',', '.');
    $imagePath = 'assets/img/sanpham/' . $row['image'];
    $color_query = "
        SELECT DISTINCT v.color_id, v.image, c.name AS color_name
        FROM product_variants v
        JOIN colors c ON v.color_id = c.color_id
        WHERE v.product_id = $id AND v.is_deleted = 0 AND v.stock > 0
        ";
    $color_result = mysqli_query($connection, $color_query);

    $color_images_html = '';
    while ($color = mysqli_fetch_assoc($color_result)) {
        $color_images_html .= '<img src="../assets/img/sanpham/' . $color['image'] . '" 
        data-product-id="' . $id . '" 
        data-color-id="' . $color['color_id'] . '"
        data-image="../assets/img/sanpham/' . $color['image'] . '"
        title="' . htmlspecialchars($color['color_name'], ENT_QUOTES) . '"
        class="color-thumb"
        style="width:28px;height:28px;object-fit:cover;border-radius:3px;border:1px solid #ccc;margin-right:4px;cursor:pointer;">';
    } 
    echo '
    <div class="xacdinhZ col-md-3 col-6 mt-3 effect_hover p-md-2 p-1">
        <div class="border rounded-1 position-relative overflow-hidden product-item" 
             data-id="' . $id . '" style="background:#fff; cursor:pointer;">
            <!-- Hình sản phẩm -->
            <div class="position-relative">
                <img id="main-image-' . $id . '" src="../' . $imagePath . '" alt=""
                    class="img-fluid product-img w-100" 
                    style="transition:transform 0.4s ease, opacity 0.4s ease;">
                
                <!-- ✅ Size overlay đè lên ảnh -->
                <div class="size-group position-absolute start-0 end-0 d-flex justify-content-center gap-1 py-2 d-none"
                    style="bottom: 0; background: rgba(255, 255, 255, 0.9); z-index: 2;"></div>
            </div>
    
            <!-- Ảnh màu -->
            <div class="mt-2 px-2">
                <div class="color-group d-flex justify-content-start">' . $color_images_html . '</div>
            </div>
    
            <!-- Nội dung -->
            <div class="mt-2 p-2 pt-1">
                <p class="mb-0 fw-lighter">Nam</p>
                <p class="mb-0">' . $gia . ' VNĐ</p>
                <p class="mb-0 limit-text">' . $name . '</p>
                <button 
                    class="btn btn-dark btn-sm mt-2 w-100 add-to-cart-btn" 
                    data-product-id="' . $id . '"
                    data-product-name="' . htmlspecialchars($name, ENT_QUOTES) . '"
                    data-product-price="' . $row['price_sale'] . '" disabled>
                    <i class="fa fa-cart-plus me-1"></i>Thêm vào giỏ
                </button>
            </div>
        </div>
    </div>';
    
    
}

$paddingTest = ($pagination->totalPages == 1) ? 'py-3' : 'py-0';
echo '<div class="' . $paddingTest . '"></div>';

if ($pagination->totalPages > 1) {
    $pagination->render(['page' => 'sanpham']);
}
?>

<script>
(function() {
    document.body.addEventListener("mouseover", function (e) {
        if (e.target.classList.contains("color-thumb")) {
            const img = e.target;
            const productId = img.getAttribute("data-product-id");
            const newSrc = img.getAttribute("data-image");
            const mainImg = document.querySelector("#main-image-" + productId);
            if (mainImg) {
                mainImg.src = newSrc;
            }
        }
    });

    document.body.addEventListener("mouseleave", function (e) {
        if (e.target.classList.contains("color-thumb")) {
            const img = e.target;
            const productId = img.getAttribute("data-product-id");
            const mainImg = document.querySelector("#main-image-" + productId);
            if (mainImg) {
                const selectedThumb = img.closest('.border.rounded-1')?.querySelector(".color-thumb.selected[data-product-id='" + productId + "']");
                if (selectedThumb) {
                    mainImg.src = selectedThumb.getAttribute("data-image");
                }
            }
        }
    });

    document.body.addEventListener("click", function (e) {
        // 📌 Khi click màu
        if (e.target.classList.contains("color-thumb")) {
            const img = e.target;
            const productId = img.getAttribute("data-product-id");
            const colorId = img.getAttribute("data-color-id");
            const newSrc = img.getAttribute("data-image");
            const container = img.closest('.border.rounded-1');

            // Đổi ảnh chính
            const mainImg = container.querySelector("#main-image-" + productId);
            if (mainImg) {
                mainImg.src = newSrc;
            }

            // Gỡ chọn cũ
            container.querySelectorAll(".color-thumb[data-product-id='" + productId + "']").forEach(el => {
                el.classList.remove("selected");
            });
            img.classList.add("selected");

            // 🔥 GỌI AJAX để lấy size theo color_id
            fetch('ajax/get_sizes_by_color.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, color_id: colorId })
            })
            .then(res => res.text())
            .then(html => {
                const sizeGroup = container.querySelector('.size-group');
                sizeGroup.innerHTML = html;
                sizeGroup.classList.remove('d-none');
            })
            .catch(err => console.error("Lỗi khi lấy size:", err));

            // Ẩn color-group nếu muốn
            const colorGroup = container.querySelector('.color-group');
            if (colorGroup) colorGroup.classList.add('d-none');
        }

        // 📌 Khi click size
        if (e.target.classList.contains('size-thumb')) {
            const container = e.target.closest('.border.rounded-1');
            if (container) {
                container.querySelectorAll('.size-thumb').forEach(el => el.classList.remove('selected'));
            }
            e.target.classList.add('selected');
        }

        // 📌 Khi thêm vào giỏ
        document.body.addEventListener('click', handleAddToCartClick);

    });
})();
</script>
