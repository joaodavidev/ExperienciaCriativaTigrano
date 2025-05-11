<?php
session_start();
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $produtoId = intval($_POST['id']);
    $sql = "SELECT * FROM produtos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $produtoId);
    $stmt->execute();
    $result = $stmt->get_result();
    $produto = $result->fetch_assoc();

    if ($produto) {
        $_SESSION['carrinho'][] = $produto;
    }
}

$produtoEncontradoID = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nome'])) {
    $nome = trim($_POST['nome']);
    $sql_search = "SELECT * FROM produtos WHERE nome = ?";
    $stmt_search = $conn->prepare($sql_search);
    $stmt_search->bind_param("s", $nome);
    $stmt_search->execute();
    $result = $stmt_search->get_result();

    if ($result->num_rows > 0) {
        $produto = $result->fetch_assoc();
        $produtoEncontradoID = $produto['id'];
    } else {
        $mensagem_erro = "Produto não encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Carrinho</title>
  <link rel="stylesheet" href="../assets/css/carrinho.css">
</head>
<body>
  <header>
    <h1>a</h1>
    <nav>
      <a href="../pages/cadastrarProdutos.php"><button>Cadastrar</button></a>
    </nav>
  </header>

  <div class="search-container">
    <form action="carrinho.php" method="POST">
      <input type="text" id="search" name="nome" placeholder="Pesquisar produto..." required />
      <button type="submit" id="search-button">Pesquisar</button>
    </form>
    <?php if (isset($mensagem_erro)) echo "<p class='erro'>$mensagem_erro</p>"; ?>
  </div>

  <div id="produtos">
    <h2>Produtos no Carrinho</h2>
    <?php
      if (!empty($_SESSION['carrinho'])) {
        foreach ($_SESSION['carrinho'] as $index => $item) {
          echo "<div class='produto' id='produto-{$item['id']}'>";
          echo "<strong>" . htmlspecialchars($item['nome']) . "</strong><br>";
          echo "R$ " . number_format($item['preco'], 2, ',', '.') . "<br>";
          echo "<p>" . htmlspecialchars($item['descricao']) . "</p>";
          echo "<form action='removerCarrinho.php' method='post' class='form-excluir'>";
          echo "<input type='hidden' name='index' value='$index'>";
          echo "<button type='submit' class='btn-excluir'>Excluir</button>";
          echo "</form>";
          echo "</div>";
        }
      } else {
        echo "<p>Nenhum produto no carrinho.</p>";
      }
    ?>
  </div>

  <div class="comprar-container">
    <form action="pagamento.php" method="POST">
      <button type="submit" class="btn-comprar">Finalizar Compra</button>
    </form>
  </div>

  <?php if ($produtoEncontradoID): ?>
    <script>
      window.onload = function() {
        const el = document.getElementById('produto-<?php echo $produtoEncontradoID; ?>');
        if (el) {
          el.classList.add('encontrado');
          el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      };
    </script>
  <?php endif; ?>
</body>
</html>
