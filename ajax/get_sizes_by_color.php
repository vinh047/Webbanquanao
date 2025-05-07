<?php
$connection = mysqli_connect("localhost", "root", "", "db_web_quanao");
mysqli_set_charset($connection, 'utf8');

$data = json_decode(file_get_contents("php://input"), true);
$product_id = (int)($data['product_id'] ?? 0);
$color_id = (int)($data['color_id'] ?? 0);

$sql = "
SELECT DISTINCT v.variant_id, s.size_id, s.name
FROM product_variants v
JOIN sizes s ON v.size_id = s.size_id
WHERE v.product_id = $product_id AND v.color_id = $color_id
AND v.is_deleted = 0 AND v.stock > 0
ORDER BY s.size_id ASC
";

$result = mysqli_query($connection, $sql);

$html = '';
while ($row = mysqli_fetch_assoc($result)) {
      $$html .= '<div class="size-thumb border text-center" 
      data-size-id="'.$row['size_id'].'"
      data-size-name="'.htmlspecialchars($row['name'], ENT_QUOTES).'" 
      data-variant-id="'.$row['variant_id'].'"
      style="width:50px;height:35px;line-height:35px;font-size:14px;margin-right:4px;cursor:pointer;user-select:none;border-radius:3px;">
      '.$row['name'].'
  </div>';
  
      data-size-id="'.$row['size_id'].'"
      data-variant-id="'.$row['variant_id'].'"
      style="width:50px;height:35px;line-height:35px;font-size:14px;margin-right:4px;cursor:pointer;user-select:none;border-radius:3px;">
      '.$row['name'].'
    </div>';
}
echo $html;