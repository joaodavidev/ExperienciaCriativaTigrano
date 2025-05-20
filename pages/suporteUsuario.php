<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['usuario']['email'])) {
  header("Location: login.php");
  exit();
}

$email = $_SESSION['usuario']['email'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<body>
    <nav class="sidebar active">
  <div class="logo-menu">
    <h2 class="logo">Tigrano</h2>
    <i class='bx bx-menu toggle-btn'></i>
  </div>
  <ul class="lista">
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
    <li class="espacador"></li>
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
      <a href="suporteUsuario.php">
        <i class='bx bx-cog'></i>
        <span class="nome-link">Suporte</span>
      </a>
    </li>
    <li class="lista-item">
      <a href="suporteAdmin.php">
        <i class='bx bx-cog'></i>
        <span class="nome-link">SuporteAdmin</span>
      </a></li>
    <li class="lista-item">
      <a href="perfil.php">
        <i class='bx bx-user'></i>
        <span class="nome-link" style="--i:7;">Perfil</span>
      </a>
    </li>
  </ul>
</nav>
    <main class="main-content">
      <section class="marketplace-header">
    <div class="marketplace-title">
      <h1>Requisitar suporte</h1>
    </div>
    <div class="suporte-lista">
      <h3>Suas solicitações de suporte:</h3>
      <?php include '../includes/readSuporteUsuario.php'; ?>
    </div>
      </section>
      <section class="marketplace-content">
        <div class="container">
          <form action="../includes/createSuporteRequest.php" method="POST">
            <div class="form-group">
              <label for="assunto">Assunto:</label>
              <input type="text" id="assunto" name="assunto" required>
              <label for="descricao">Descrição:</label>
              <input type="text" id="descricao" name="descricao" required>
              <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
              <input type="hidden" name="data_envio" value="<?php echo date('Y-m-d H:i:s'); ?>">
              <button type="submit">Solicitar suporte</button>
            </div>
          </form>
        </div>
      </section>
</body>
</html>