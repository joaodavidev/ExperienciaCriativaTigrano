<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['usuario']['email'])) {
  header("Location: login.php");
  exit();
}

$email = $_SESSION['usuario']['email'];

// Verifica se o e-mail existe na tabela de administradores
$sql = "SELECT 1 FROM adm WHERE email = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Se não for admin, exibe erro e redireciona
if ($result->num_rows === 0) {
  echo "<script>
          alert('Acesso negado. Apenas administradores podem acessar esta página.');
          window.location.href = 'marketplace.php';
        </script>";
  exit();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<body>
  <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tigrano Marketplace</title>
  <link rel="stylesheet" href="caminho/para/suporteAdmin.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  </head>
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
          <h1>Suporte</h1>
        </div>
      </section>

      <section class="suporte-section">
        <h2>Tickets de Suporte</h2>
        <?php include '../includes/readSuporteAdmin.php'; ?>
      </section>
</body>
</html>