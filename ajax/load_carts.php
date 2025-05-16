<?php
require_once '../database/DBConnection.php';
$db = DBConnect::getInstance();

$user_id = $_POST['user_id'] ?? '';

$carts = $db->select('SELECT * FROM carts WHERE user_id = ?', [$user_id]);

ob_start();
foreach ($carts as $cart): ?>
    <div class="d-flex align-items-center mb-2 border-bottom pb-2">
        <img src="/assets/img/sanpham/${item.image}" style="width:50px;height:50px;object-fit:cover;" class="me-2 rounded">
        <div class="flex-grow-1">
            <p class="mb-0 small fw-bold">${item.name}</p>
            <p class="mb-0 text-muted small">
                <span class="me-1 d-inline-block rounded-circle"
                    style="width:12px; height:12px; background-color:${colorHex}; border:1px solid #aaa;">
                </span>
                ${colorName} - ${item.size || '(không size)'}
            </p>
            <div class="d-flex align-items-center mt-1">
                <span class="small me-2">SL:</span>
                <button class="btn btn-sm btn-outline-secondary px-2" onclick="window.changeMiniCartQty(${index}, -1)">
                    <i class="fa fa-minus"></i>
                </button>
                <span class="mx-2">${item.quantity}</span>
                <button class="btn btn-sm btn-outline-secondary px-2" onclick="window.changeMiniCartQty(${index}, 1)">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>
        <button class="btn btn-sm btn-outline-danger ms-2" onclick="window.removeMiniCartItem(${index})">
            <i class="fa fa-trash"></i>
        </button>
    </div>
<?php endforeach

?>