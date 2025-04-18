<?php
function locBienThe($connection)
{
    $where = [];

    if (!empty($_POST['txtIDBT'])) {
        $idbt = (int)$_POST['txtIDBT'];
        $where[] = "product_variants.variant_id = $idbt";
    }

    if (!empty($_POST['txtIDSP'])) {
        $idsp = (int)$_POST['txtIDSP'];
        $where[] = "product_variants.product_id = $idsp";
    }

    if (!empty($_POST['cbSizeLoc'])) {
        $size_id = (int)$_POST['cbSizeLoc'];
        $where[] = "product_variants.size_id = $size_id";
    }

    if (!empty($_POST['cbMauLoc'])) {
        $color_id = (int)$_POST['cbMauLoc'];
        $where[] = "product_variants.color_id = $color_id";
    }

    if (!empty($_POST['txtSoLuong'])) {
        $soluong = (int)$_POST['txtSoLuong'];
        $where[] = "product_variants.stock >= $soluong";
    }

    return (count($where) > 0) ? "WHERE " . implode(" AND ", $where) : "";
}
?>
