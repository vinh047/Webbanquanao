<?php
require_once '../../database/DBConnection.php';
$db = DBConnect::getInstance();

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($order_id <= 0) {
    echo "<div class='text-danger'>Mã đơn hàng không hợp lệ.</div>";
    exit;
}

// Lấy thông tin chi tiết đơn hàng dựa trên variant_id
$details = $db->select("
    SELECT 
        p.name AS product_name,
        od.quantity,
        od.price,
        s.name AS size_name,
        c.name AS color_name,
        pv.image
    FROM order_details od
    JOIN product_variants pv ON pv.variant_id = od.variant_id
    JOIN products p ON p.product_id = pv.product_id
    LEFT JOIN sizes s ON s.size_id = pv.size_id
    LEFT JOIN colors c ON c.color_id = pv.color_id
    WHERE od.order_id = ?
", [$order_id]);

if (!$details) {
    echo "<div class='text-muted'>Không có sản phẩm trong đơn hàng này.</div>";
    exit;
}
?>

<table class="table table-bordered align-middle">
    <thead class="table-light">
        <tr>
            <th>Ảnh</th>
            <th>Sản phẩm</th>
            <th>Size</th>
            <th>Màu</th>
            <th>Số lượng</th>
            <th>Đơn giá</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($details as $item): ?>
        <tr>
            <td>
                <?php if (!empty($item['image'])): ?>
                    <img src="../../assets/img/sanpham/<?= htmlspecialchars($item['image']) ?>" width="60" height="60" style="object-fit:cover;border-radius:5px;">
                <?php else: ?>
                    <span class="text-muted">Không ảnh</span>
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td><?= htmlspecialchars($item['size_name'] ?? '(không có)') ?></td>
            <td><?= htmlspecialchars($item['color_name'] ?? '(không có)') ?></td>
            <td><?= (int)$item['quantity'] ?></td>
            <td><?= number_format($item['price']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
