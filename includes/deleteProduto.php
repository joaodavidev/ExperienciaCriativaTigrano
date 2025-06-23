<?php
session_start();
include 'db.php';
include 'verificar_login.php';
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $vendedorEmail = $_SESSION['usuario']['email'];

    // Segurança extra: só deleta se o produto pertence ao vendedor logado
    $sql = "DELETE FROM produtos WHERE id = ? AND vendedor_email = ?";    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        header("Location: ../pages/produto.php?erro=erro_prepare");
        exit();
    }

    $stmt->bind_param("is", $id, $vendedorEmail);

    if ($stmt->execute()) {
        header("Location: ../pages/produto.php?removido=1");
        exit;
    } else {
        header("Location: ../pages/produto.php?erro=erro_deletar");
        exit();
    }
} else {
    header("Location: ../pages/produto.php?erro=requisicao_invalida");
    exit();
}
?>
