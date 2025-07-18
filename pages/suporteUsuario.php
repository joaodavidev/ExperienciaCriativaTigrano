<?php
session_start();
require_once '../includes/db.php';
include '../includes/verificar_login.php';

$email = $_SESSION['usuario']['email'];

$total     = $conn->query("SELECT COUNT(*) AS total FROM suporte WHERE email_usuario = '$email'")->fetch_assoc()['total'];
$abertos   = $conn->query("SELECT COUNT(*) AS total FROM suporte WHERE email_usuario = '$email' AND (resposta IS NULL OR TRIM(resposta) = '')")->fetch_assoc()['total'];
$respondidos = $conn->query("SELECT COUNT(*) AS total FROM suporte WHERE email_usuario = '$email' AND TRIM(resposta) <> ''")->fetch_assoc()['total'];


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
    </div>    <div class="filtro-tickets">
      <a href="?filtro=todos" class="<?= (!isset($_GET['filtro']) || $_GET['filtro'] === 'todos') ? 'ativo' : '' ?>">Todos <span><?= $total ?></span></a>
      <a href="?filtro=abertos" class="<?= ($_GET['filtro'] ?? '') === 'abertos' ? 'ativo' : '' ?>">Abertos <span><?= $abertos ?></span></a>
      <a href="?filtro=respondidos" class="<?= ($_GET['filtro'] ?? '') === 'respondidos' ? 'ativo' : '' ?>">Respondidos <span><?= $respondidos ?></span></a>
      <button id="botaoAbrirTicket">Novo Ticket</button>
    </div>

    <div class="suporte-lista">
      <?php include '../includes/readSuporteUsuario.php'; ?>
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
      <p id="modalRespostaWrapper"><strong>Resposta:</strong><br> <span id="modalResposta"></span></p>
      <div class="modal-ticket-actions" style="display: flex; justify-content: flex-end; align-items: center; gap: 10px; margin-top: 10px;">
        <i class='bx bx-edit' id="btnEditarTicket" style="font-size: 1.5em; cursor:pointer;"></i>
        <form id="formExcluirTicket" action="../includes/deleteTicket.php" method="POST" style="display:inline;">
          <input type="hidden" name="id" id="modalTicketId">
          <button type="submit" style="background:none;border:none;padding:0;margin:0;cursor:pointer;" onclick="event.stopPropagation();">
            <i class='bx bx-trash' style="font-size: 1.5em; color:#e74c3c;"></i>
          </button>
        </form>
      </div>
    </div> 
  </div>

  <div id="modalNovoTicket" class="modal">
  <div class="modal-content">
    <span id="fecharModalNovoTicket" class="fechar">&times;</span>
    <h2>Novo Ticket de Suporte</h2>
    <form action="../includes/createSuporteRequest.php" method="POST">
      <div class="form-group">
        <input type="text" id="assunto" name="assunto" placeholder="Digite o assunto" required>
        <input type="text" id="descricao" name="descricao" placeholder="Descreva o problema" required>
        <input type="hidden" name="data_envio" value="<?= date('Y-m-d H:i:s') ?>">
        <button type="submit">Solicitar suporte</button>
      </div>
    </form>
  </div>
</div>

</main>

<script src="../assets/css/js/script.js"></script>
<script src="../assets/css/js/suporteUsuario.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const botaoAbrirTicket = document.getElementById('botaoAbrirTicket');
    const modalNovoTicket = document.getElementById('modalNovoTicket');
    const fecharModalNovoTicket = document.getElementById('fecharModalNovoTicket');
    
    botaoAbrirTicket.addEventListener('click', function() {
      modalNovoTicket.style.display = 'block';
    });
    
    fecharModalNovoTicket.addEventListener('click', function() {
      modalNovoTicket.style.display = 'none';
    });
    
    window.addEventListener('click', function(event) {
      if (event.target === modalNovoTicket) {
        modalNovoTicket.style.display = 'none';
      }
    });
  });
</script>

<!-- Mensagem de sucesso para ticket excluído -->
<?php if (isset($_GET['excluido']) && $_GET['excluido'] == 1): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const tema = localStorage.getItem("tema") === "claro";
      
      Swal.fire({
        icon: 'success',
        title: 'Ticket excluído com sucesso!',
        confirmButtonText: 'OK',
        background: tema ? '#E6E4E4' : '#262626',
        color: tema ? '#121212' : '#ffffff',
        confirmButtonColor: '#1D4ED8'
      }).then(() => {
        const url = new URL(window.location.href);
        url.searchParams.delete('excluido');
        window.history.replaceState({}, document.title, url.toString());
      });
    });
  </script>
<?php endif; ?>

<!-- Tratamento de erros de suporte -->
<?php if (isset($_GET['erro'])): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const erro = '<?php echo $_GET["erro"]; ?>';
      const tema = localStorage.getItem("tema") === "claro";
      
      let titulo = 'Erro';
      let mensagem = 'Ocorreu um erro inesperado. Tente novamente.';
      
      switch(erro) {
        case 'campos_obrigatorios':
          titulo = 'Campos obrigatórios';
          mensagem = 'Preencha todos os campos: assunto e descrição são obrigatórios.';
          break;
        case 'erro_prepare':
          titulo = 'Erro interno';
          mensagem = 'Erro ao preparar a consulta. Tente novamente mais tarde.';
          break;
        case 'erro_inserir':
          titulo = 'Erro ao enviar';
          mensagem = 'Erro ao salvar sua solicitação. Tente novamente.';
          break;
        case 'ticket_nao_encontrado':
          titulo = 'Ticket não encontrado';
          mensagem = 'O ticket solicitado não foi encontrado ou você não tem permissão para acessá-lo.';
          break;
        case 'erro_atualizar':
          titulo = 'Erro ao atualizar';
          mensagem = 'Erro ao atualizar o ticket. Tente novamente.';
          break;
        case 'erro_excluir':
          titulo = 'Erro ao excluir';
          mensagem = 'Erro ao excluir o ticket. Tente novamente.';
          break;
      }
      
      Swal.fire({
        icon: 'error',
        title: titulo,
        text: mensagem,
        confirmButtonText: 'OK',
        background: tema ? '#E6E4E4' : '#262626',
        color: tema ? '#121212' : '#ffffff',
        confirmButtonColor: '#DC2626'
      }).then(() => {
        const url = new URL(window.location.href);
        url.searchParams.delete('erro');
        window.history.replaceState({}, document.title, url.toString());
      });
    });
  </script>
<?php endif; ?>

</body>
</html>