<?php
session_start();
require_once '../includes/db.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tigrano Marketplace</title>
  <link rel="stylesheet" href="../assets/css/suporteUsuario.css">
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
  <section class="marketplace-header">
    <div class="marketplace-title">
      <h1>Requisitar suporte</h1>
    </div>

    <h3>Tickets em aberto</h3>
    <div class="suporte-lista">
      <?php include '../includes/readSuporteUsuario_abertos.php'; ?>
    </div>

    <h3>Tickets respondidos</h3>
    <div class="suporte-lista">
      <?php include '../includes/readSuporteUsuario_respondidos.php'; ?>
    </div>
  </section>

  <section class="marketplace-content">
    <div class="container">
      <form action="../includes/createSuporteRequest.php" method="POST">
        <div class="form-group">
          <h2>Formulário de suporte</h2>
          <input type="text" id="assunto" name="assunto" placeholder="Digite o assunto" required>
          <input type="text" id="descricao" name="descricao" placeholder="Descreva o problema" required>
          <input type="hidden" name="data_envio" value="<?php echo date('Y-m-d H:i:s'); ?>">
          <button type="submit">Solicitar suporte</button>
        </div>
      </form>
    </div>
  </section>

  <div id="modalSuporte" class="modal">
    <div class="modal-content">
      <span id="fecharModalSuporte" class="fechar">&times;</span>
      <h2>Detalhes do Ticket</h2>
      <p><strong>Assunto:</strong><br> <span id="modalAssunto"></span></p>
      <p><strong>Mensagem:</strong><br> <span id="modalMensagem"></span></p>
      <p><strong>Data:</strong><br> <span id="modalData"></span></p>
      <p><strong>Status:</strong><br> <span id="modalStatus"></span></p>
    </div>
  </div>
</main>

<script src="../assets/css/js/script.js"></script>
<script src="../assets/css/js/suporteUsuario.js"></script>
</body>
</html>