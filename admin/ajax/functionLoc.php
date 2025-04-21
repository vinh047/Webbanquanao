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

function locSanPham($connection)
{
    $where = [];
    if(!empty($_POST['txtIDSP']))
    {
        $idsp = (int)$_POST['txtIDSP'];
        $where[] = "p.product_id = $idsp";    }

    if(!empty($_POST['txtTensp']))
    {
        $tensp = addslashes($_POST['txtTensp']);
        $where[] = "p.name LIKE '%$tensp%'";        
        
    }

    if(!empty($_POST['cbTheLoai']))
    {
        $cbtheloai = $_POST['cbTheLoai'];
        $where[] = "p.category_id = $cbtheloai";
    }

    if(!empty($_POST['txtGiaMin']))
    {
        $tienMin = (int)$_POST['txtGiaMin'];
        $where[] = "p.price >= $tienMin";
    }

    if(!empty($_POST['txtGiaMax']))
    {
        $tienMax = (int)$_POST['txtGiaMax'];
        $where[] = "p.price <= $tienMax";
    }

    return (count($where) > 0) ? "WHERE " . implode(" AND ", $where) : "";
}
?>
