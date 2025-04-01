<?php


function sapXepSanPham()
{
    $sort = $_GET['sapxep'] ?? '';
    if($sort === 'tangdan')
    {
        return 'ORDER BY price ASC';
    }else
    {
        return 'ORDER BY price DESC';
    }
}

function locSanPham($connection)
{
    $where = [];

    if(!empty($_GET['selectTheloai']))
    {
        $theloai = mysqli_real_escape_string($connection, $_GET['selectTheloai']);
        $categoryQuery = mysqli_query($connection,"SELECT category_id FROM categories WHERE name LIKE '%$theloai%'");
        if($row = mysqli_fetch_assoc($categoryQuery))
        {
            $catID = $row['category_id'];
            $where[] = "products.category_id = $catID";
        }
    }

    if(!empty($_GET['giamin']))
    {
        $giamin = (int)$_GET['giamin'];
        $where[] = "products.price >= $giamin";
    }

    if(!empty($_GET['giamax']))
    {
        $giamax = (int)$_GET['giamax'];
        $where[] = "products.price <= $giamax";
    }

    if(!empty($_GET['colors']))
    {
        $color_id = array_map('intval',$_GET['colors']);
        $color_str = implode(",",$color_id);
        $where[] = "product_variants.color_id IN ($color_str)";
    }

    if(!empty($_GET['sizes']))
    {
        $size = array_map(function($s) use ($connection)
        {
            return "'" . mysqli_real_escape_string($connection,$s) . "'";
        }, $_GET['sizes']);
        $size_str = implode(",",$size);
        $where[] = "product_variants.size IN ($size_str)";
    }

    return (count($where) > 0) ? "WHERE " . implode(" AND ", $where) : "";
}
?>