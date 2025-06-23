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
  <style>
    .config-section {
      margin-top: 30px;
      padding: 20px;
      border-radius: 12px;
      background: var(--card-bg, #1e1e1e);
      border: 1px solid var(--border-color, #333);
    }
    
    .aviso-exclusao {
      display: flex;
      align-items: flex-start;
      gap: 15px;
      padding: 15px;
      background: rgba(59, 130, 246, 0.1);
      border-left: 4px solid #3b82f6;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    
    .aviso-exclusao i {
      color: #3b82f6;
      font-size: 20px;
      margin-top: 2px;
    }
    
    .aviso-exclusao p {
      margin: 0;
      line-height: 1.5;
    }
    
    .config-section h2 {
      margin-bottom: 20px;
      color: var(--text-primary, #ffffff);
    }
    
    /* Modo claro */
    body.light-mode .config-section {
      --card-bg: #ffffff;
      --border-color: #e5e7eb;
      --text-primary: #1f2937;
    }
    
    body.light-mode .aviso-exclusao {
      background: rgba(59, 130, 246, 0.05);
    }
  </style>
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

    <div class="config-section">
      <h2>Exclusão de Conta</h2>
      <div class="aviso-exclusao">
        <i class='bx bx-info-circle'></i>
        <p><strong>Importante:</strong> Se você já vendeu produtos, sua conta será apenas <strong>desativada</strong> (não deletada) para preservar o acesso dos compradores aos produtos adquiridos.</p>
      </div>
      
      <form id="formDeletarConta" action="../includes/deleteCadastro.php" method="POST" class="logout-form">
        <input type="hidden" name="deletar_conta" value="1">
        <button type="button" onclick="confirmarExclusao()">Deletar minha conta</button>
      </form>
    </div>
  </section>
</main>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmarExclusao() {
  const temaClaro = localStorage.getItem("tema") === "claro";

  Swal.fire({
    title: "Deletar conta?",
    html: "<div style='text-align: left;'>" +
          "<p style='margin-bottom: 10px;'><strong>Atenção:</strong></p>" +
          "<p style='margin-bottom: 10px;'>• Se você <strong>nunca vendeu</strong> produtos: sua conta será <strong>deletada permanentemente</strong>.</p>" +
          "<p style='margin-bottom: 10px;'>• Se você <strong>já vendeu</strong> produtos: sua conta será apenas <strong>desativada</strong> para preservar o acesso dos compradores aos produtos adquiridos.</p>" +
          "<p style='margin-top: 15px; font-size: 14px; color: #666;'>Deseja continuar?</p>" +
          "</div>",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sim, continuar",
    cancelButtonText: "Cancelar",
    background: temaClaro ? "#E6E4E4" : "#262626",
    color: temaClaro ? "#121212" : "#ffffff",
    confirmButtonColor: "#b91c1c",
    cancelButtonColor: "#4b5563",
    width: '500px'
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
