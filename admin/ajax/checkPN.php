<?php
        require_once(__DIR__ . '/../../database/DBConnection.php');
        header('Content-Type: application/json');
        file_put_contents("debug_checkpn.txt", json_encode($_GET));

if (isset($_GET['pn_id'])) {
    $id = (int)$_GET['pn_id'];

    $pdo = DBConnect::getInstance()->getConnection();
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM importreceipt WHERE importreceipt_id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    echo json_encode(['exists' => $row['total'] > 0]);
} else {
    echo json_encode(['exists' => false]);
}
?>
