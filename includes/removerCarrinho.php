<?php
session_start();
include '../includes/db.php';

// Verifica se o produto_id foi enviado e se o usuário está logado corretamente
if (isset($_POST['produto_id']) && isset($_SESSION['usuario']['email'])) {
    $usuario_email = $_SESSION['usuario']['email'];
    $produtoId = intval($_POST['produto_id']);

    // SQL para remover o produto do carrinho do usuário
    $sql = "DELETE FROM carrinho WHERE usuario_email = ? AND produto_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Erro ao preparar a query: " . $conn->error);
    }

    $stmt->bind_param("si", $usuario_email, $produtoId);

    if ($stmt->execute()) {
        // Você pode exibir uma mensagem para depuração (remova em produção)
        // echo "Produto removido com sucesso.";
    } else {
        die("Erro ao executar a exclusão: " . $stmt->error);
    }

    $stmt->close();
} else {
    // Caso os dados não estejam completos
    die("Erro: produto_id ausente ou usuário não autenticado.");
}

// Redireciona de volta para o carrinho
header("Location: ../pages/carrinho.php");
exit;
?>
