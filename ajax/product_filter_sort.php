<?php

function sapXepSanPham()
{
    $sort = $_GET['sapxep'] ?? '';
    if ($sort === 'tangdan') {
        return 'ORDER BY price ASC';
    } else {
        return 'ORDER BY price DESC';
    }
}

function locSanPham($connection)
{
    $where = [];

    if (!empty($_GET['tensp'])) {
        $tensp = mysqli_real_escape_string($connection, $_GET['tensp']);
        $where[] = "products.name LIKE '%$tensp%'";
    }

    if (!empty($_GET['selectTheloai'])) {
        $catID = (int)$_GET['selectTheloai'];
        $where[] = "products.category_id = $catID";
    }

    if (!empty($_GET['giamin'])) {
        $giamin = (int)$_GET['giamin'];
        $where[] = "products.price >= $giamin";
    }

    if (!empty($_GET['giamax'])) {
        $giamax = (int)$_GET['giamax'];
        $where[] = "products.price <= $giamax";
    }

    if (!empty($_GET['colors'])) {
        $colors_raw = $_GET['colors'];
        if (!is_array($colors_raw)) {
            $colors_raw = explode(',', $colors_raw);
        }
        $color_id = array_map('intval', $colors_raw);
        $color_str = implode(",", $color_id);
        $where[] = "product_variants.color_id IN ($color_str)";
    }

    if (!empty($_GET['sizes'])) {
        $sizes_raw = $_GET['sizes'];
        if (!is_array($sizes_raw)) {
            $sizes_raw = explode(',', $sizes_raw);
        }
        $size_ids = array_map('intval', $sizes_raw);
        $size_str = implode(",", $size_ids);
        $where[] = "product_variants.size_id IN ($size_str)";
    }
    

    return (count($where) > 0) ? "WHERE " . implode(" AND ", $where) : "";
}

?>
