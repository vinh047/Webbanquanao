<?php
require_once '../database/DBConnection.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$items = $input['items'] ?? [];

$db = DBConnect::getInstance()->getConnection();
$errors = [];

foreach ($items as $item) {
    $variant_id = (int)$item['variant_id'];
    $requiredQty = (int)$item['quantity'];

    $stmt = $db->prepare("SELECT stock FROM product_variants WHERE variant_id = ?");
    $stmt->execute([$variant_id]);
    $stock = (int)($stmt->fetchColumn());

    if ($requiredQty > $stock) {
        $errors[] = [
            'variant_id' => $variant_id,
            'product_name' => $item['product_name'],
            'size' => $item['size'],
            'color' => $item['color'],
            'stock' => $stock
        ];
    }
}

echo json_encode([
    'success' => true,
    'errors' => $errors
]);
