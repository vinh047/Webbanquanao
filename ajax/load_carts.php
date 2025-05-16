<?php
require_once '../database/DBConnection.php';
$db = DBConnect::getInstance();

$user_id = $_POST['user_id'] ?? '';

$carts = $db->select('SELECT cd.* FROM cart_details cd JOIN cart c ON c.cart_id = cd.cart_id WHERE c.user_id = ?', [$user_id]);

ob_start();
foreach ($carts as $cart):
    $details = $db->selectOne('
    SELECT p.name as product_name, pv.image as image, c.name as color_name, c.hex_code, s.name as size_name
    FROM products p
    JOIN product_variants pv ON pv.product_id = p.product_id
    JOIN colors c ON c.color_id = pv.color_id
    JOIN sizes s ON s.size_id = pv.size_id
    WHERE p.product_id = ? AND pv.variant_id = ?
', [$cart['product_id'], $cart['variant_id']]);
?>
    <div class="cart-item-wrap d-flex align-items-center mb-2 border-bottom pb-2"
        data-variant-id="<?= $cart['variant_id'] ?>"
        data-product-id="<?= $cart['product_id'] ?>">
        <img src="/assets/img/sanpham/<?= $details['image'] ?>" style="width:50px;height:50px;object-fit:cover;" class="me-2 rounded">
        <div class="flex-grow-1">
            <p class="mb-0 small fw-bold"><?= $details['product_name'] ?></p>
            <p class="mb-0 text-muted small">
                <span class="me-1 d-inline-block rounded-circle"
                    style="width:12px; height:12px; background-color:<?= $details['hex_code'] ?>; border:1px solid #aaa;">
                </span>
                <?= $details['color_name'] ?> - <?= $details['size_name'] ?>
            </p>
            <div class="d-flex align-items-center mt-1">
                <span class="small me-2">SL:</span>
                <button class="btn btn-sm btn-outline-secondary px-2 btn-minus">
                    <i class="fa fa-minus"></i>
                </button>
                <span class="qty mx-2"><?= $cart['quantity'] ?></span>
                <button class="btn btn-sm btn-outline-secondary px-2 btn-plus">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>
        <button class="btn btn-sm btn-outline-danger ms-2 btn-delete-cart-item">
            <i class="fa fa-trash"></i>
        </button>
    </div>
<?php endforeach;

$cartItemHtml = ob_get_clean();

header('Content-Type: application/json');
echo json_encode(['success' => true, 'cartItemHtml' => $cartItemHtml]);

?>