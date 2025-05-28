<?php
session_start();
require_once '../includes/db.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['usuario']['email'];

// Consultas preparadas para contar tickets
$total = $abertos = $respondidos = 0;

// Consulta para total de tickets
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM suporte WHERE email_usuario = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $total = $row['total'];
}
$stmt->close();

// Consulta para tickets abertos
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM suporte WHERE email_usuario = ? AND (resposta IS NULL OR TRIM(resposta) = '')");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $abertos = $row['total'];
}
$stmt->close();

// Consulta para tickets respondidos
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM suporte WHERE email_usuario = ? AND TRIM(resposta) <> ''");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $respondidos = $row['total'];
}
$stmt->close();

// Consulta para listar tickets
$sql = "SELECT * FROM suporte WHERE email_usuario = ? ORDER BY data_envio DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$tickets = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Suporte - Tigrano Marketplace</title>
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

<main class="main-content">  <section class="marketplace-header">
    <div class="marketplace-title">
      <h1>Requisitar suporte</h1>
    </div>

    <div class="filtro-tickets">
      <a href="?filtro=todos" class="<?= (!isset($_GET['filtro']) || $_GET['filtro'] === 'todos') ? 'ativo' : '' ?>">Todos <span><?= $total ?></span></a>
      <a href="?filtro=abertos" class="<?= ($_GET['filtro'] ?? '') === 'abertos' ? 'ativo' : '' ?>">Abertos <span><?= $abertos ?></span></a>
      <a href="?filtro=respondidos" class="<?= ($_GET['filtro'] ?? '') === 'respondidos' ? 'ativo' : '' ?>">Respondidos <span><?= $respondidos ?></span></a>
      <button id="botaoAbrirTicket" class="ativo">Abrir Ticket</button>
    </div>

    <div class="suporte-lista">
      <?php include '../includes/readSuporteUsuario.php'; ?>
    </div>
  </section>

  <!-- Modal para detalhes do ticket -->
  <div id="modalSuporte" class="modal">
    <div class="modal-content">
      <span id="fecharModalSuporte" class="fechar">&times;</span>
      <h2>Detalhes do Ticket</h2>
      <div class="modal-detalhes">
        <p><strong>Assunto:</strong><br> <span id="modalAssunto"></span></p>
        <p><strong>Mensagem:</strong><br> <span id="modalMensagem"></span></p>
        <p><strong>Data:</strong><br> <span id="modalData"></span></p>
        <p><strong>Status:</strong><br> <span id="modalStatus" class="status-badge"></span></p>
        <p id="modalRespostaWrapper"><strong>Resposta:</strong><br> <span id="modalResposta"></span></p>
      </div>
    </div> 
  </div>

  <!-- Modal para novo ticket -->
  <div id="modalNovoTicket" class="modal">
    <div class="modal-content">
      <span id="fecharModalNovoTicket" class="fechar">&times;</span>
      <h2>Novo Ticket de Suporte</h2>
      <form action="../includes/createSuporteRequest.php" method="POST" class="form-suporte">
        <div class="form-group">
          <label for="assunto">Assunto</label>
          <input type="text" id="assunto" name="assunto" placeholder="Digite o assunto" required>
          
          <label for="descricao">Descrição do problema</label>
          <textarea id="descricao" name="descricao" placeholder="Descreva detalhadamente o problema" required rows="5"></textarea>
          
          <input type="hidden" name="data_envio" value="<?= date('Y-m-d H:i:s') ?>">
          <button type="submit" class="btn-submit">Solicitar suporte</button>
        </div>
      </form>
    </div>
  </div>

</main>

<script src="../assets/css/js/script.js"></script>
<script src="../assets/css/js/suporteUsuario.js"></script>
</body>
</html>