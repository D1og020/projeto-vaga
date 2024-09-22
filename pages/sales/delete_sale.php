<?php
include '../../config/database.php';

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM vendas WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    header("Location: list_sales.php");
}
?>