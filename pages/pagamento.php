<?php
session_start();
include '..includes/db.php';

// olha se tem algo no carrinho
if (empty($_SESSION['carrinho'])) {
    header("Location: carrinho.php");
    exit();
}

// calcula o total do carrinho
$total = 0;
foreach ($_SESSION['carrinho'] as $item) {
    $total += $item['preco'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['finalizar_compra'])) {
    // aq da pra integrar com sistema de pagamento, tipo processar pagamento e limpar carrinho apos a compra


    // simula pagamento concluido
    $_SESSION['carrinho'] = []; // limpa o carrinho

    // redireciona pra uma pagina de confirmação ficticia..
    header("Location: pagamento_confirmado.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Pagamento</title>
  <link rel="stylesheet" href="../assets/css/pagamento.css">
</head>
<body>
  <header>
    <h1>TIGRANO</h1>
    <nav>
      <a href="../pages/cadastrarProdutos.php"><button>Cadastrar</button></a>
      <a href="../pages/carrinho.php"><button>Carrinho</button></a>
    </nav>
  </header>

  <div id="resumo-compra">
    <h2>Resumo da Compra</h2>
    <?php
    if (!empty($_SESSION['carrinho'])) {
        foreach ($_SESSION['carrinho'] as $index => $item) {
            echo "<div class='produto'>";
            echo "<strong>" . htmlspecialchars($item['nome']) . "</strong><br>";
            echo "R$ " . number_format($item['preco'], 2, ',', '.') . "<br>";
            echo "<p>" . htmlspecialchars($item['descricao']) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>Nenhum produto no carrinho.</p>";
    }
    ?>

    <div class="total">
      <strong>Total: R$ <?php echo number_format($total, 2, ',', '.'); ?></strong>
    </div>

    <form action="pagamento.php" method="POST" class="form-pagamento">
      <button type="submit" name="finalizar_compra" class="btn-comprar">Finalizar Pagamento</button>
    </form>
  </div>
</body>
</html>
