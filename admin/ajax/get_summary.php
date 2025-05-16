<?php
include_once '../../database/DBConnection.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = DBConnect::getInstance();

$tongSanPham = $db->selectOne("SELECT COUNT(*) AS total FROM products")['total'] ?? 0;
$tongNhanVien = $db->selectOne("SELECT COUNT(*) AS total FROM users WHERE role_id IN (2,4)")['total'] ?? 0;
$tongKhachHang = $db->selectOne("SELECT COUNT(*) AS total FROM users WHERE role_id = 1")['total'] ?? 0;
$tongNhaCungCap = $db->selectOne("SELECT COUNT(*) AS total FROM supplier")['total'] ?? 0;
$tongTonKho = $db->selectOne("SELECT SUM(stock) AS total FROM product_variants")['total'] ?? 0;

header('Content-Type: application/json');
echo json_encode([
    'tongSanPham' => $tongSanPham,
    'tongNhanVien' => $tongNhanVien,
    'tongKhachHang' => $tongKhachHang,
    'tongNhaCungCap' => $tongNhaCungCap,
    'tongTonKho' => $tongTonKho,
]);
exit;
