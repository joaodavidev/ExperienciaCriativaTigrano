<?php
session_start();
include '../includes/db.php';

if (isset($_POST['produto_id']) && isset($_SESSION['usuario_email'])) {
    $email = $_SESSION['usuario_email'];
    $produtoId = intval($_POST['produto_id']);

    $sql = "DELETE FROM carrinho WHERE usuario_email = ? AND produto_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $email, $produtoId);
    $stmt->execute();
}

header("Location: ../pages/carrinho.php");
exit;
