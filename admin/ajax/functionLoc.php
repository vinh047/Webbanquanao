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

    if (isset($_POST['txtIDSP']) && $_POST['txtIDSP'] !== '') {
        $idsp = (int)$_POST['txtIDSP'];
        $where[] = "p.product_id = $idsp";
    }

    if (isset($_POST['txtTensp']) && $_POST['txtTensp'] !== '') {
        $tensp = addslashes($_POST['txtTensp']);
        $where[] = "p.name LIKE '%$tensp%'";
    }

    if (isset($_POST['cbTheLoai']) && $_POST['cbTheLoai'] !== '') {
        $cbtheloai = (int)$_POST['cbTheLoai'];
        $where[] = "p.category_id = $cbtheloai";
    }

    if (isset($_POST['txtGiaMin']) && $_POST['txtGiaMin'] !== '') {
        $tienMin = (int)$_POST['txtGiaMin'];
        $where[] = "p.price >= $tienMin";
    }

    if (isset($_POST['txtGiaMax']) && $_POST['txtGiaMax'] !== '') {
        $tienMax = (int)$_POST['txtGiaMax'];
        $where[] = "p.price <= $tienMax";
    }

    return (count($where) > 0) ? "WHERE " . implode(" AND ", $where) : "";
}


function locCTPN($connection)
{
    $where = [];

    if(!empty($_POST['txtIDctpn']))
    {
        $idctpn = (int)$_POST['txtIDctpn'];
        $where[] = "importreceipt_details.importreceipt_details_id = $idctpn";
    }

    if(!empty($_POST['txtIDpn']))
    {
        $idpn = (int)$_POST['txtIDpn'];
        $where[] = "importreceipt_details.importreceipt_id = $idpn";
    }

    if(!empty($_POST['txtIDsp']))
    {
        $idsp = (int)$_POST['txtIDsp'];
        $where[] = "importreceipt_details.product_id = $idsp";
    }

    if(!empty($_POST['txtIDbt']))
    {
        $idbt = (int)$_POST['txtIDbt'];
        $where[] = "importreceipt_details.variant_id = $idbt";
    }

    if(!empty($_POST['dateNhap']))
    {
        $ngaybd = $_POST['dateNhap'];
        $where[] = "DATE(importreceipt_details.created_at) >= '$ngaybd'";
    }

    if(!empty($_POST['dateKT']))
    {
        $ngaykt = $_POST['dateKT'];
        $where[] = "DATE(importreceipt_details.created_at) <= '$ngaykt'";
    }

    if (isset($_POST['txtTrangThai']) && $_POST['txtTrangThai'] !== '') {
        $status = (int)$_POST['txtTrangThai'];
        $where[] = "importreceipt_details.status = $status";
    }

    return (count($where) > 0) ? "WHERE " . implode(" AND ", $where) : "";

}

function locPhieuNhap($connection)
{
    $where = [];

    if (!empty($_POST['txtIDpn'])) {
        $idpn = (int)$_POST['txtIDpn'];
        $where[] = "im.ImportReceipt_id = $idpn";
    }

    if (!empty($_POST['txtIDncc'])) {
        $idncc = (int)$_POST['txtIDncc'];
        $where[] = "im.supplier_id = $idncc";
    }

    if (!empty($_POST['txtIDnv'])) {
        $idnv = (int)$_POST['txtIDnv'];
        $where[] = "im.user_id = $idnv";
    }

    if (!empty($_POST['dateNhap'])) {
        $ngaybd = trim($_POST['dateNhap']);
        $where[] = "DATE(im.created_at) >= '$ngaybd'";
    }
    

    if (!empty($_POST['dateKT'])) {
        $ngaykt = trim($_POST['dateKT']);
        $where[] = "DATE(im.created_at) <= '$ngaykt'";
    }
    

    if (isset($_POST['txtTrangThai']) && $_POST['txtTrangThai'] !== '') {
        $status = (int)$_POST['txtTrangThai'];
        $where[] = "im.status = $status";
    }

    return (count($where) > 0) ? "WHERE " . implode(" AND ", $where) : "WHERE 1";
}
?>
