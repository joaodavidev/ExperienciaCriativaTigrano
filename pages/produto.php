<?php
session_start();
require_once '../includes/db.php';
include '../includes/verificar_login.php';
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
    <li class="lista-item"><a href="../pages/marketplace.php"><i class='bx bxs-shopping-bag-alt'></i><span class="nome-link" style="--i:1;">Marketplace</span></a></li>
    <li class="lista-item"><a href="../pages/dashboard.php"><i class='bx bxs-dashboard'></i><span class="nome-link" style="--i:2;">Dashboard</span></a></li>
    <li class="lista-item"><a href="../pages/produto.php"><i class='bx bxs-purchase-tag'></i><span class="nome-link" style="--i:3;">Produtos</span></a></li>
    <li class="lista-item"><a href="../pages/compras.php"><i class='bx bx-shopping-bag'></i><span class="nome-link" style="--i:4;">Compras</span></a></li>
    <li class="espacador"></li>
    <li class="lista-item"><a href="#" class="btn-toggle-tema"><i class='bx bx-moon'></i><span class="nome-link" style="--i:5;">Claro/Escuro</span></a></li>
    <li class="lista-item"><a href="../pages/suporteUsuario.php"><i class='bx bx-info-circle'></i><span class="nome-link" style="--i:6;">Ajuda</span></a></li>
    <li class="lista-item"><a href="../pages/configuracoes.php"><i class='bx bx-cog'></i><span class="nome-link" style="--i:7;">Configurações</span></a></li>
    <li class="lista-item"><a href="../pages/perfil.php"><i class='bx bx-user'></i><span class="nome-link" style="--i:8;">Perfil</span></a></li>
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
        <form action="../includes/createProduto.php" method="POST" class="form-produto" enctype="multipart/form-data">
            <input type="hidden" name="id">
            <input type="text" name="nome" placeholder="Nome do Produto" required />
            <input type="text" name="categoria" placeholder="Categoria" required />
            <input type="number" name="preco" placeholder="Preço" step="0.01" required />
            <textarea name="descricao" placeholder="Descrição do Produto" required></textarea>

            <div class="upload-arquivo">
                <label for="arquivo_produto">Arquivo do Produto (opcional):</label>
                <input type="file" name="arquivo_produto" id="arquivo_produto" accept=".pdf,.doc,.docx,.zip,.rar,.txt,.mp4,.mp3,.png,.jpg,.jpeg">
                <small>Formatos aceitos: PDF, DOC, ZIP, TXT, MP4, MP3, PNG, JPG (máx. 10MB)</small>
            </div>

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1): ?>
  <script>
    const tema = localStorage.getItem("tema") === "claro";
    Swal.fire({
      icon: 'success',
      title: 'Produto criado com sucesso!',
      confirmButtonText: 'OK',
      background: tema ? '#E6E4E4' : '#262626',
      color: tema ? '#121212' : '#ffffff',
      confirmButtonColor: '#1D4ED8'
    }).then(() => {
      const url = new URL(window.location.href);
      url.searchParams.delete('sucesso');
      window.history.replaceState({}, document.title, url.toString());
    });
  </script>
<?php endif; ?>

</body>
</html>