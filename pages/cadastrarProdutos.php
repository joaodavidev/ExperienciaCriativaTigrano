<?php
include '../includes/db.php';

$produtoEncontradoID = null;
$produtosFiltrados = [];

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
    }else {
        $mensagem_erro = "Produto não encontrado.";
    }
    
} elseif (isset($_GET['preco_min']) && isset($_GET['preco_max']) && $_GET['preco_min'] !== '' && $_GET['preco_max'] !== '') {
    $min = (float) $_GET['preco_min'];
    $max = (float) $_GET['preco_max'];
    $sql_search = "SELECT * FROM produtos WHERE preco BETWEEN ? AND ?";
    $stmt_search = $conn->prepare($sql_search);
    $stmt_search->bind_param("dd", $min, $max);
    $stmt_search->execute();
    $produtosFiltrados = $stmt_search->get_result();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cadastro de Produtos</title>
  <link rel="stylesheet" href="../assets/css/cadastrarProdutos.css" />
</head>
<body>
  <header>
    <h1>TIGRANO</h1>
    <nav>
      <a href="../pages/paginaInicial.php"><button>Home</button></a>
      <a href="../pages/carrinho.php"><button>Carrinho</button></a>
      <button>Contato</button>
    </nav>
  </header>

  <div id="searchbar">
    <form action="index.php" method="POST">
      <input type="text" id="search" name="nome" placeholder="Pesquisar produto..." required />
      <button type="submit" id="search-button">Pesquisar</button>
    </form>
    <?php if (isset($mensagem_erro)) echo "<p class='erro'>$mensagem_erro</p>"; ?>
  </div>

  <div class="filtro-preco-form">
    <form action="index.php" method="GET">
      <label>Preço mínimo:
        <input type="number" name="preco_min" step="0.01" min="0">
      </label>
      <label>Preço máximo:
        <input type="number" name="preco_max" step="0.01" min="0">
      </label>
      <button type="submit">Filtrar</button>
    </form>
  </div>

  <h1>Cadastro de Produtos</h1>
  <form action="create.php" method="POST">
    <input type="text" name="nome" placeholder="Nome do Produto" required />
    <input type="number" name="preco" placeholder="Preço" step="0.01" required />
    <textarea name="descricao" placeholder="Descrição do Produto"></textarea>
    <button type="submit">Cadastrar</button>
  </form>

  <div id="produtos">
    <h2>Produtos Cadastrados</h2>
    <?php if (!empty($produtosFiltrados)): ?>
      <?php while($produto = $produtosFiltrados->fetch_assoc()): ?>
        <div class="produto" id="produto-<?php echo $produto['id']; ?>">
          <strong><?php echo htmlspecialchars($produto['nome']); ?></strong>
          <p>Preço: R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
          <p><?php echo htmlspecialchars($produto['descricao']); ?></p>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <?php include '../includes/readProduto.php'; ?>
    <?php endif; ?>
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