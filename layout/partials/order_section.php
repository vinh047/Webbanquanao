<?php
// layout/partials/order_section.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../database/DBConnection.php';

$pdo     = DBConnect::getInstance()->getConnection();
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo "<p>Vui lòng đăng nhập để xem đơn hàng.</p>";
    return;
}

// --- 1) XỬ LÝ HỦY ĐƠN ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order_id'])) {
    $cancelId = intval($_POST['cancel_order_id']);
    $stmtCancel = $pdo->prepare("
    UPDATE orders
    SET status = 'Đã huỷ'
    WHERE order_id = ? 
      AND user_id = ? 
      AND status = 'Chờ xác nhận'
  ");
    $stmtCancel->execute([$cancelId, $user_id]);
    // Sau khi xong, tiếp tục render lại danh sách (no redirect)
}

// --- 2) Phân trang ---
$perPage    = 5;
$page_order = max(1, intval($_GET['page_order'] ?? 1));
$offset     = ($page_order - 1) * $perPage;

// Tổng số đơn
$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
$stmtCount->execute([$user_id]);
$totalOrders = (int)$stmtCount->fetchColumn();
$totalPages  = (int)ceil($totalOrders / $perPage);

// Lấy đơn trang hiện tại
$stmt = $pdo->prepare("
  SELECT o.order_id,o.created_at,o.status,o.total_price,pm.name AS payment_method
  FROM orders o
  LEFT JOIN payment_method pm 
    ON o.payment_method_id = pm.payment_method_id
  WHERE o.user_id = :uid
  ORDER BY o.created_at DESC
  LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':uid',    $user_id, PDO::PARAM_INT);
$stmt->bindValue(':limit',  $perPage,  PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,   PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h3 class="mb-4">Đơn hàng của bạn</h3>

<?php if (empty($orders)): ?>
    <p>Bạn chưa có đơn hàng nào.</p>
<?php else: ?>
    <table class="table table-bordered align-middle text-center">
        <thead class="table-light">
            <tr>
                <th>Ngày</th>
                <th>Trạng thái</th>
                <th>Thanh toán</th>
                <th>Tổng tiền</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
                    <td><?= htmlspecialchars($o['status']) ?></td>
                    <td><?= htmlspecialchars($o['payment_method']) ?></td>
                    <td><?= number_format($o['total_price'], 0, ',', '.') ?>₫</td>
                    <td>
                        <!-- Mở modal chi tiết -->
                        <button
                            class="btn btn-sm btn-info text-white"
                            data-bs-toggle="modal"
                            data-bs-target="#orderDetailModal"
                            data-order-id="<?= $o['order_id'] ?>">
                            <i class="fa-solid fa-eye"></i> Chi tiết
                        </button>

                        <!-- Hủy đơn (POST về chính file này) -->
                        <?php if ($o['status'] === 'Chờ xác nhận'): ?>
                            <form
                                method="POST"
                                action=""
                                class="d-inline"
                                onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn này?');">
                                <input type="hidden" name="cancel_order_id" value="<?= $o['order_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger ms-1">
                                    <i class="fa-solid fa-trash"></i> Hủy đơn
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- phân trang giống trước -->
    <?php if ($totalPages > 1): ?>
        <div class="d-flex justify-content-center align-items-center my-4">
            <a href="?page=donhang&page_order=1" class="btn btn-outline-secondary<?= $page_order === 1 ? ' disabled' : '' ?>">&laquo;</a>
            <a href="?page=donhang&page_order=<?= max(1, $page_order - 1) ?>" class="btn btn-outline-secondary ms-2<?= $page_order === 1 ? ' disabled' : '' ?>">&lsaquo;</a>
            <span class="border rounded mx-2 px-3 py-2"><?= $page_order ?> / <?= $totalPages ?></span>
            <a href="?page=donhang&page_order=<?= min($totalPages, $page_order + 1) ?>" class="btn btn-outline-secondary me-2<?= $page_order === $totalPages ? ' disabled' : '' ?>">&rsaquo;</a>
            <a href="?page=donhang&page_order=<?= $totalPages ?>" class="btn btn-outline-secondary<?= $page_order === $totalPages ? ' disabled' : '' ?>">&raquo;</a>
        </div>
    <?php endif; ?>

    <!-- Ẩn sẵn content chi tiết để modal dùng -->
    <?php foreach ($orders as $o): ?>
        <?php
        $stmt2 = $pdo->prepare("
        SELECT od.quantity, od.price, od.total_price,
               p.name AS product_name,
               c.name AS color_name,
               s.name AS size_name
        FROM order_details od
        JOIN products p ON od.product_id = p.product_id
        LEFT JOIN product_variants pv ON od.variant_id = pv.variant_id
        LEFT JOIN colors c ON pv.color_id = c.color_id
        LEFT JOIN sizes s ON pv.size_id = s.size_id
        WHERE od.order_id = ?
      ");
        $stmt2->execute([$o['order_id']]);
        $items = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <div id="detail-content-<?= $o['order_id'] ?>" class="d-none">
            <table class="table table-sm mb-0 text-center">
                <thead>
                    <tr class="table-secondary">
                        <th>Sản phẩm</th>
                        <th>Màu</th>
                        <th>Size</th>
                        <th>SL</th>
                        <th>Đơn giá</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $it): ?>
                        <tr>
                            <td><?= htmlspecialchars($it['product_name']) ?></td>
                            <td><?= htmlspecialchars($it['color_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($it['size_name']  ?? '-') ?></td>
                            <td><?= $it['quantity'] ?></td>
                            <td><?= number_format($it['price'], 0, ',', '.') ?>₫</td>
                            <td><?= number_format($it['total_price'], 0, ',', '.') ?>₫</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>

    <!-- Modal chi tiết đơn -->
    <div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailModalLabel">Chi tiết đơn hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('orderDetailModal')
            .addEventListener('show.bs.modal', function(e) {
                let oid = e.relatedTarget.getAttribute('data-order-id');
                let content = document.getElementById('detail-content-' + oid).innerHTML;
                this.querySelector('.modal-body').innerHTML = content;
            });
    </script>
<?php endif; ?>