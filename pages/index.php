<?php
include 'db.php';

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
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cadastro de Produtos</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <header>
    <h1>TIGRANO</h1>
    <nav>
      <button>Home</button>
      <button>Produtos</button>
      <button>Carrinho</button>
      <button>Contato</button>
    </nav>
  </header>

  <div id="searchbar">
    <form action="index.php" method="POST">
      <input type="text" id="search" name="nome" placeholder="Pesquisar produto..." required />
      <button type="submit" id="search-button">Pesquisar</button>
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
    <?php include 'read.php'; ?>
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
