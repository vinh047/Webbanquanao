<?php
$userId = $_SESSION['user_id'];
$addresses = $db->select(
    "SELECT * FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC, updated_at DESC",
    [$userId]
);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Địa chỉ của tôi</h2>
    <button class="btn btn-dark rounded-pill" data-bs-toggle="modal" data-bs-target="#modalAddAddress">
        Thêm địa chỉ mới
    </button>
</div>

<?php if (empty($addresses)): ?>
    <div class="alert alert-warning">Bạn chưa có địa chỉ nào.</div>
<?php else: ?>
    <?php foreach ($addresses as $address): ?>
        <div class="border-bottom py-3">
            <div class="fw-bold">
                <?= htmlspecialchars($address['address_detail']) ?>,
                <?= htmlspecialchars($address['ward']) ?>,
                <?= htmlspecialchars($address['district']) ?>,
                <?= htmlspecialchars($address['province']) ?>
                <?php if ($address['is_default']): ?>
                    <span class="badge bg-light text-dark border ms-2">★ Mặc định</span>
                <?php endif; ?>
            </div>
            <div class="text-muted small">
                Cập nhật: <?= date('d/m/Y H:i', strtotime($address['updated_at'])) ?>
            </div>
            <div class="mt-2">
                <a href="#" class="me-3 text-primary btn-edit-address"
                   data-address='<?= json_encode($address, JSON_HEX_APOS | JSON_UNESCAPED_UNICODE) ?>'
                   data-bs-toggle="modal" data-bs-target="#modalAddAddress">
                    Cập nhật
                </a>

                <a href="#" class="text-danger btn-delete-address" data-id="<?= $address['address_id'] ?>">Xoá</a>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script src="/assets/js/address_section.js" defer></script>