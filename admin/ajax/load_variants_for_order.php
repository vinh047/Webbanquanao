<?php
require_once '../../database/DBConnection.php';

$db = DBConnect::getInstance();

// Lấy product_id từ GET (hoặc POST nếu bạn dùng fetch POST)
$productId = $_GET['product_id'] ?? null;

if (!$productId) {
    echo json_encode(['success' => false, 'message' => 'Thiếu product_id']);
    exit;
}

// Lấy danh sách biến thể của sản phẩm
$variants = $db->select("
    SELECT v.variant_id, c.name AS color, s.name AS size, v.stock, v.image, c.color_id, s.color_id
    FROM product_variants v
    JOIN products p ON p.product_id = v.product_id
    JOIN colors c ON v.color_id = c.color_id
    JOIN sizes s ON v.size_id = s.size_id
    WHERE v.product_id = ? AND v.is_deleted = 0
", [$productId]);

// Tạo HTML bảng biến thể
ob_start();
foreach ($variants as $variant): ?>
    <tr>
        <td><?= $variant['variant_id'] ?></td>
         <td>
        <img
            src="../../assets/img/sanpham/<?= $variant['image'] ?>"
            alt="Ảnh"
            class="img-thumbnail"
            style="width: 60px; height: 60px; object-fit: cover;"
        >
    </td>
    <td><?= htmlspecialchars($variant['size']) ?></td>
    <td><?= htmlspecialchars($variant['color']) ?></td>
        <td><?= $variant['stock'] ?></td>
        <td>
            <button
                class="btn btn-success btn-choose-variant"
                data-variant-id="<?= $variant['variant_id'] ?>"
                data-color="<?= htmlspecialchars($variant['color_id']) ?>"
                data-size="<?= htmlspecialchars($variant['color_id']) ?>"
                data-stock="<?$variant['stock']?>">
                <i class="fa fa-check"></i> Chọn
            </button>
        </td>
    </tr>
<?php endforeach;

$variantHtml = ob_get_clean();

// Trả về JSON
echo json_encode([
    'success' => true,
    'variantHtml' => $variantHtml
]);
