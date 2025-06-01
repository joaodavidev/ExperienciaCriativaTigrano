<?php
session_start();
include '../includes/db.php';

if (isset($_SESSION['usuario']['email'])) {
    $usuario_email = $_SESSION['usuario']['email'];

    if (isset($_SESSION['carrinho']) && !empty($_SESSION['carrinho'])) {        foreach ($_SESSION['carrinho'] as $produto) {
            $produto_id = $produto['id'];
            $quantidade = 1;
            
            $stmtFornecedor = $conn->prepare("SELECT vendedor_email FROM produtos WHERE id = ?");
            $stmtFornecedor->bind_param("i", $produto_id);
            $stmtFornecedor->execute();
            $stmtFornecedor->bind_result($vendedor_email);
            $stmtFornecedor->fetch();
            $stmtFornecedor->close();
            
            if ($vendedor_email) {
                // Inserir na tabela vendas
                $stmtVenda = $conn->prepare("INSERT INTO vendas (fornecedor_email, comprador_email, produto_id, quantidade_vendas, data_vendas) VALUES (?, ?, ?, ?, NOW())");
                $stmtVenda->bind_param("ssii", $vendedor_email, $usuario_email, $produto_id, $quantidade);
                $stmtVenda->execute();
                $stmtVenda->close();

                // Inserir no histórico de compras
                $stmtHistorico = $conn->prepare("INSERT INTO historico_compras (comprador_email, produto_id, data_compras) VALUES (?, ?, NOW())");
                $stmtHistorico->bind_param("si", $usuario_email, $produto_id);
                $stmtHistorico->execute();
                $stmtHistorico->close();
            }
        }

        $conn->query("DELETE FROM carrinho WHERE usuario_email = '$usuario_email'");
        unset($_SESSION['carrinho']);
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pagamento Aprovado</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .swal2-custom-popup {
      border-radius: 15px;
      padding: 2em;
      background-color: #1e1e1e;
      color: white;
    }
    .swal2-custom-title {
      font-size: 1.8rem;
      font-weight: bold;
      font-family: 'Poppins', sans-serif;
    }
    .swal2-html-container {
      font-family: 'Poppins', sans-serif;
    }
    .swal2-custom-button {
      padding: 0.6em 1.5em;
      font-size: 1rem;
      font-weight: 600;
      background-color: #4CAF50 !important;
      color: white !important;
      border-radius: 8px;
    }
  </style>
</head>
<body>

<script>
  Swal.fire({
    icon: 'success',
    title: 'Pagamento Aprovado!',
    text: 'Seu pagamento foi confirmado com sucesso.',
    confirmButtonText: 'Voltar à loja',
    customClass: {
      popup: 'swal2-custom-popup',
      title: 'swal2-custom-title',
      confirmButton: 'swal2-custom-button'
    }
  }).then(() => {
    window.location.href = 'marketplace.php';
  });
</script>

</body>
</html>