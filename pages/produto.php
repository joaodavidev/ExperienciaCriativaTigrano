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
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tigrano Marketplace</title>
  <link rel="stylesheet" href="../assets/css/produto.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
  <nav class="sidebar active">
      <div class="logo-menu">
        <h2 class="logo">Tigrano</h2>
        <i class='bx bx-menu toggle-btn'></i>
      </div>
      <ul class="lista">
        <li class="lista-item active">
            <a href="../pages/paginaInicial.php">
                <i class='bx bxs-shopping-bag-alt' ></i>
                <span class="nome-link" style="--i:1;">Marketplace</span>
            </a>
        </li>
        <li class="lista-item">
            <a href="#">
                <i class='bx bxs-dashboard' ></i>
                <span class="nome-link" style="--i:2;">Dashboard</span>
            </a>
        </li>
        <li class="lista-item">
            <a href="../pages/produtos.php">
                <i class='bx bxs-purchase-tag' ></i>
                <span class="nome-link" style="--i:3;">Produtos</span>
            </a>
        </li>
        <li class="lista-item">
            <a href="#">
                <i class='bx bx-shopping-bag' ></i>
                <span class="nome-link" style="--i:4;">Compras</span>
            </a>
              </li>
        <li class="lista-item grupo-inferior">
            <a href="#" class="btn-toggle-tema">
              <i class='bx bx-moon'></i>
              <span class="nome-link" style="--i:5;">Claro/Escuro</span>
            </a>
          </li>
        <li class="lista-item">
          <a href="#">
            <i class='bx bx-cog'></i>
            <span class="nome-link" style="--i:6;">Configurações</span>
          </a>
        </li>
        <li class="lista-item">
          <a href="#">
            <i class='bx bx-log-out'></i>
            <span class="nome-link" style="--i:7;">LogOut</span>
          </a>
        </li>
      </ul>
    </nav>
    <main class="main-content">
    <div class="main-header">
        <h1>Produtos</h1>
        <button id="btnNovoProduto"><i class="bx bx-plus"></i> Novo Produto</button>
    </div>

    <div class="search-bar">
        <input type="text" name="nome" placeholder="Buscar produtos">
        <i class='bx bx-search'></i>
    </div>
    <div id="modalProduto" class="modal-overlay" style="display: none;">
      <div class="modal-content">
        <h2>Novo Produto</h2>
        <form action="../includes/createProduto.php" method="POST" class="form-produto">
            <input type="hidden" name="id">
            <input type="text" name="nome" placeholder="Nome do Produto" required />
            <input type="text" name="categoria" placeholder="Categoria" required />
            <input type="number" name="preco" placeholder="Preço" step="0.01" required />
            <textarea name="descricao" placeholder="Descrição do Produto" required></textarea>

        <select name="status" required>
            <option value="Ativo">Ativo</option>
            <option value="Inativo">Inativo</option>
        </select>
        <div class="botoes">
            <button type="submit">Salvar</button>
            <button type="button" id="fecharModal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
    <section class="produtos-listagem">
    <div class="tabela-produtos">
      <div class="tabela-cabecalho">
        <span>PRODUTO</span>
        <span>CATEGORIA</span>
        <span>PREÇO</span>
        <span>STATUS</span>
        <span>AÇÕES</span>
      </div>

      <?php include '../includes/readProduto.php'; ?>
    </div>
    </section>
</main>
    <script src="../assets/css/js/script.js"></script>
    <script src="../assets/css/js/produtos.js"></script>
</body>
</html>