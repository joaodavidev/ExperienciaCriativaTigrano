<?php
session_start();
require_once '../includes/db.php';
include '../includes/verificar_login.php';

$email = $_SESSION['usuario']['email'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Configurações</title>
  <link rel="stylesheet" href="../assets/css/perfil.css">
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
      <h1>Configurações da Conta</h1>
    </div>
  </section>

  <section class="perfil-container">
    <?php if (isset($_GET['erro'])) echo "<p class='mensagem'>" . htmlspecialchars($_GET['erro']) . "</p>"; ?>

   <form id="formDeletarConta" action="../includes/deleteCadastro.php" method="POST" class="logout-form">
  <input type="hidden" name="deletar_conta" value="1">
  <button type="button" onclick="confirmarExclusao()">Deletar minha conta</button>
  </form>
  </section>
</main>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmarExclusao() {
  const temaClaro = localStorage.getItem("tema") === "claro";

  Swal.fire({
    title: "Tem certeza?",
    text: "Essa ação irá deletar permanentemente sua conta.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sim, deletar",
    cancelButtonText: "Cancelar",
    background: temaClaro ? "#E6E4E4" : "#262626",
    color: temaClaro ? "#121212" : "#ffffff",
    confirmButtonColor: "#b91c1c",
    cancelButtonColor: "#4b5563"
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById('formDeletarConta').submit();
    }
  });
}
</script>
<script src="../assets/css/js/script.js"></script>
</body>
</html>
