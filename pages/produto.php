<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['email'])) {
  header("Location: ../pages/login.php");
  exit();
}

$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Produtos</title>
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
        <!-- Itens superiores -->
        <li class="lista-item">
          <a href="../pages/marketplace.php">
            <i class='bx bxs-shopping-bag-alt'></i>
            <span class="nome-link" style="--i:1;">Marketplace</span>
          </a>
        </li>
        <li class="lista-item">
          <a href="../pages/dashboard.php">
            <i class='bx bxs-dashboard'></i>
            <span class="nome-link" style="--i:2;">Dashboard</span>
          </a>
        </li>
        <li class="lista-item">
          <a href="../pages/produto.php">
            <i class='bx bxs-purchase-tag'></i>
            <span class="nome-link" style="--i:3;">Produtos</span>
          </a>
        </li>
        <li class="lista-item">
          <a href="../pages/compras.php">
            <i class='bx bx-shopping-bag'></i>
            <span class="nome-link" style="--i:4;">Compras</span>
          </a>
        </li>

        <!-- Separador flexível -->
        <li class="espacador"></li>

        <!-- Itens inferiores -->
        <li class="lista-item">
          <a href="#" class="btn-toggle-tema">
            <i class='bx bx-moon'></i>
            <span class="nome-link" style="--i:5;">Claro/Escuro</span>
          </a>
        </li>
        <li class="lista-item">
          <a href="../pages/configuracoes.php">
            <i class='bx bx-cog'></i>
            <span class="nome-link" style="--i:6;">Configurações</span>
          </a>
        </li>
        <li class="lista-item">
          <a href="perfil.php">
            <i class='bx bx-user' ></i>
            <span class="nome-link" style="--i:7;">Perfil</span>
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