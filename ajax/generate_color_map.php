<?php
require_once '../database/DBConnection.php';
header('Content-Type: application/javascript');

$db = DBConnect::getInstance();
$colors = $db->select("SELECT color_id, name, hex_code FROM colors WHERE is_deleted = 0");

echo "window.COLOR_MAP = {\n";
foreach ($colors as $color) {
    $id = (int)$color['color_id'];
    $name = addslashes($color['name']);
    $hex = addslashes($color['hex_code']);
    echo "  $id: { name: \"$name\", hex: \"$hex\" },\n";
}
echo "};";
