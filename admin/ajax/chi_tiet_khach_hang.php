<?php
require_once '../../database/DBConnection.php';
$db = DBConnect::getInstance();
$user_id = (int)($_GET['user_id'] ?? 0);

// 1. Đơn hàng
$orders = $db->select("
    SELECT order_id, created_at, total_price, status
    FROM orders
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT 5
", [$user_id]);

ob_start();
if ($orders) {
    echo "<ul class='list-group'>";
    foreach ($orders as $order) {
        echo "<li class='list-group-item'>";
        echo "Mã: <strong>{$order['order_id']}</strong> | ";
        echo "Ngày: {$order['created_at']} | ";
        echo "Tổng: <strong>" . number_format($order['total_price']) . "đ</strong> | ";
        echo "Trạng thái: {$order['status']}";
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "Không có đơn hàng.";
}
$ordersHtml = ob_get_clean();

// 2. Lịch sử mua hàng (sản phẩm)
$history = $db->select("
    SELECT p.name, od.quantity, od.price, o.created_at
    FROM order_details od
    JOIN orders o ON o.order_id = od.order_id
    JOIN product_variants pv ON pv.variant_id = od.variant_id
    JOIN products p ON p.product_id = pv.product_id
    WHERE o.user_id = ? AND o.status = 'Giao thành công'
    ORDER BY o.created_at DESC
    LIMIT 10
", [$user_id]);

ob_start();
if ($history) {
    echo "<table class='table table-bordered'>";
    echo "<thead><tr><th>Tên SP</th><th>SL</th><th>Giá</th><th>Ngày mua</th></tr></thead><tbody>";
    foreach ($history as $item) {
        echo "<tr>
                <td>{$item['name']}</td>
                <td>{$item['quantity']}</td>
                <td>" . number_format($item['price']) . "đ</td>
                <td>{$item['created_at']}</td>
              </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "Chưa có lịch sử mua hàng.";
}
$historyHtml = ob_get_clean();

// 3. Địa chỉ
$addresses = $db->select("
    SELECT address_detail, ward, district, province, is_default
    FROM user_addresses
    WHERE user_id = ?
", [$user_id]);

ob_start();
if ($addresses) {
    echo "<ul class='list-group'>";
    foreach ($addresses as $addr) {
        echo "<li class='list-group-item'>";
        echo "{$addr['address_detail']}, {$addr['ward']}, {$addr['district']}, {$addr['province']}";
        if ($addr['is_default']) echo " <span class='badge bg-success'>Mặc định</span>";
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "Không có địa chỉ nào.";
}
$addressesHtml = ob_get_clean();

// Trả JSON
header('Content-Type: application/json');
echo json_encode([
    'ordersHtml'    => $ordersHtml,
    'historyHtml'   => $historyHtml,
    'addressesHtml' => $addressesHtml
]);
