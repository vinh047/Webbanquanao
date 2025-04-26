<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../../database/DBConnection.php');
$pdo = DBConnect::getInstance()->getConnection();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_ctpn = $_POST['txtMaCTPNsua'] ?? null;
    $id_pn = $_POST['txtMaPNsua'] ?? null;
    $id_sp = $_POST['txtMaSPsua'] ?? null;
    $quantity_new = intval($_POST['txtSlsuaTon'] ?? 0);
    $variant_id_input = $_POST['txtMaBTsua'] ?? null;

    if (!$id_ctpn || !$id_pn || !$id_sp || !$quantity_new || !$variant_id_input) {
        echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đầu vào']);
        exit;
    }

    // 1. Lấy dữ liệu cũ
    $stmt = $pdo->prepare("SELECT variant_id, quantity, importreceipt_id FROM importreceipt_details WHERE importreceipt_details_id = ?");
    $stmt->execute([$id_ctpn]);
    $old = $stmt->fetch();

    if (!$old) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy chi tiết phiếu nhập']);
        exit;
    }

    $variant_id_old = $old['variant_id'];
    $quantity_old = intval($old['quantity']);
    $old_pn_id = $old['importreceipt_id'];

    // Chuẩn bị biến để xử lý
    $final_variant_id = $variant_id_input;
    $variant_to_hide = null;

    // 2. Lấy lại giá sản phẩm
    $stmtPrice = $pdo->prepare("SELECT price FROM products WHERE product_id = ?");
    $stmtPrice->execute([$id_sp]);
    $unit_price = $stmtPrice->fetchColumn();
    if (!$unit_price) $unit_price = 0;
    $total_price = $unit_price * $quantity_new;

    // 3. Xử lý cập nhật tồn kho
$stmtOldVariant = $pdo->prepare("SELECT image, color_id, size_id FROM product_variants WHERE variant_id = ?");
$stmtOldVariant->execute([$variant_id_old]);
$oldVariant = $stmtOldVariant->fetch();

$final_variant_id = $variant_id_input;
$variant_to_hide = null;
$existingVariant = null;

if ($oldVariant) {
    // Trường hợp người dùng đổi sản phẩm nhưng vẫn giữ variant_id cũ
    if ($variant_id_old == $variant_id_input) {
        // Tự động kiểm tra xem biến thể đó đã tồn tại ở sản phẩm mới chưa
        $stmtFindVariant = $pdo->prepare("SELECT variant_id FROM product_variants 
            WHERE product_id = ? AND image = ? AND color_id = ? AND size_id = ? AND is_deleted = 0");
        $stmtFindVariant->execute([$id_sp, $oldVariant['image'], $oldVariant['color_id'], $oldVariant['size_id']]);
        $existingVariant = $stmtFindVariant->fetchColumn();

        if ($existingVariant && $existingVariant != $variant_id_old) {
            // Có biến thể trùng ở sản phẩm mới
            $final_variant_id = $existingVariant;
            $variant_to_hide = $variant_id_old;

            // Gộp tồn kho
            $pdo->prepare("UPDATE product_variants SET stock = stock + ? WHERE variant_id = ?")
                ->execute([$quantity_new, $existingVariant]);
            $pdo->prepare("UPDATE product_variants SET stock = stock - ? WHERE variant_id = ?")
                ->execute([$quantity_old, $variant_id_old]);
        } else {
            // Không gộp, chỉ đổi product_id nếu cần
            $delta = $quantity_new - $quantity_old;
            $pdo->prepare("UPDATE product_variants SET stock = stock + ? WHERE variant_id = ?")
                ->execute([$delta, $variant_id_old]);

            $stmtCheck = $pdo->prepare("SELECT product_id FROM product_variants WHERE variant_id = ?");
            $stmtCheck->execute([$variant_id_old]);
            $currentProduct = $stmtCheck->fetchColumn();

            if ($currentProduct != $id_sp) {
                $pdo->prepare("UPDATE product_variants SET product_id = ? WHERE variant_id = ?")
                    ->execute([$id_sp, $variant_id_old]);
            }
        }
    } else {
        // Trường hợp đổi sang một biến thể mới khác hẳn
        $pdo->prepare("UPDATE product_variants SET stock = stock - ? WHERE variant_id = ?")
            ->execute([$quantity_old, $variant_id_old]);
        $pdo->prepare("UPDATE product_variants SET stock = stock + ? WHERE variant_id = ?")
            ->execute([$quantity_new, $variant_id_input]);

        $pdo->prepare("UPDATE product_variants SET product_id = ? WHERE variant_id = ?")
            ->execute([$id_sp, $variant_id_input]);
    }
}


    // 4. Cập nhật chi tiết phiếu nhập
    $stmtUpdate = $pdo->prepare("
        UPDATE importreceipt_details 
        SET importreceipt_id = ?, product_id = ?, variant_id = ?, quantity = ?, unit_price = ?, total_price = ?
        WHERE importreceipt_details_id = ?
    ");
    $stmtUpdate->execute([
        $id_pn,
        $id_sp,
        $final_variant_id,
        $quantity_new,
        $unit_price,
        $total_price,
        $id_ctpn
    ]);

    // 5. Đánh dấu biến thể cũ là đã xoá nếu không còn dùng
    if ($variant_to_hide) {
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM importreceipt_details WHERE variant_id = ?");
        $stmtCheck->execute([$variant_to_hide]);
        $stillUsed = $stmtCheck->fetchColumn();

        $stmtCheck2 = $pdo->prepare("SELECT COUNT(*) FROM order_details WHERE variant_id = ?");
        $stmtCheck2->execute([$variant_to_hide]);
        $stillUsed += $stmtCheck2->fetchColumn();

        if ($stillUsed == 0) {
            $pdo->prepare("UPDATE product_variants SET is_deleted = 1, stock = 0 WHERE variant_id = ?")
                ->execute([$variant_to_hide]);
        }
    }

    // 6. Cập nhật tổng tiền phiếu nhập hiện tại
    $stmtUpdateTotal = $pdo->prepare("
        UPDATE importreceipt 
        SET total_price = (
            SELECT SUM(total_price)
            FROM importreceipt_details
            WHERE importreceipt_id = ?
        )
        WHERE importreceipt_id = ?
    ");
    $stmtUpdateTotal->execute([$id_pn, $id_pn]);

    // 7. Nếu mã phiếu nhập thay đổi, cập nhật lại phiếu cũ
    if ($id_pn != $old_pn_id) {
        $stmtUpdateTotalOld = $pdo->prepare("
            UPDATE importreceipt 
            SET total_price = (
                SELECT SUM(total_price)
                FROM importreceipt_details
                WHERE importreceipt_id = ?
            )
            WHERE importreceipt_id = ?
        ");
        $stmtUpdateTotalOld->execute([$old_pn_id, $old_pn_id]);
    }

    echo json_encode(['success' => true]);
}
echo json_encode([
    'success' => true,
    'variant_id_old' => $variant_id_old,
    'variant_id_input' => $variant_id_input,
    'variant_id_thuc_su_gop' => $existingVariant ?? null,
    'is_gop' => ($existingVariant ?? null) && $existingVariant != $variant_id_old,
    'final_variant_id' => $final_variant_id,
    'chinh_sua_product_id' => $id_sp
]);
exit;


?>
