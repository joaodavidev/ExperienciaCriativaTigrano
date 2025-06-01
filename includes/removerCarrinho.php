<?php
session_start();
include '../includes/db.php';
include '../includes/verificar_login.php'; // ✅ proteção de sessão

if (isset($_POST['produto_id'])) {
    $usuario_email = $_SESSION['usuario']['email'];
    $produtoId = intval($_POST['produto_id']);

    $sql = "DELETE FROM carrinho WHERE usuario_email = ? AND produto_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Erro ao preparar a query: " . $conn->error);
    }

    $stmt->bind_param("si", $usuario_email, $produtoId);

    if (!$stmt->execute()) {
        die("Erro ao executar a exclusão: " . $stmt->error);
    }

    $stmt->close();
} else {
    die("Erro: produto_id ausente.");
}

header("Location: ../pages/carrinho.php");
exit;
?>
