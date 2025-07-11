
<?php
session_start();
require_once '../includes/db.php';

$filtro = $_GET['filtro'] ?? 'abertos';

switch ($filtro) {
  case 'abertos':
    $sql = "SELECT s.*, u.nome FROM suporte s JOIN usuarios u ON s.email_usuario = u.email WHERE s.resposta IS NULL OR TRIM(s.resposta) = '' ORDER BY data_envio DESC";
    break;
  case 'respondidos':
    $sql = "SELECT s.*, u.nome FROM suporte s JOIN usuarios u ON s.email_usuario = u.email WHERE s.resposta IS NOT NULL AND TRIM(s.resposta) <> '' ORDER BY data_envio DESC";
    break;
  default:
    $sql = "SELECT s.*, u.nome FROM suporte s JOIN usuarios u ON s.email_usuario = u.email ORDER BY data_envio DESC";
    break;
}

$tickets = $conn->query($sql);
$ticketsArray = [];
$tickets->data_seek(0);
while ($row = $tickets->fetch_assoc()) {
  $ticketsArray[] = $row;
}

// contadores
$totalTodos = $conn->query("SELECT COUNT(*) AS total FROM suporte")->fetch_assoc()['total'];
$totalAbertos = $conn->query("SELECT COUNT(*) AS total FROM suporte WHERE resposta IS NULL OR TRIM(resposta) = ''")->fetch_assoc()['total'];
$totalRespondidos = $conn->query("SELECT COUNT(*) AS total FROM suporte WHERE resposta IS NOT NULL AND TRIM(resposta) <> ''")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Tickets - Admin</title>
  <link rel="stylesheet" href="../assets/css/tickets.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<nav class="sidebar active">
  <div class="logo-menu">
    <h2 class="logo">Tigrano</h2>
    <i class='bx bx-menu toggle-btn'></i>
  </div>
<ul class="lista">
  <li class="lista-item"><a href="admin.php"><i class='bx bx-pie-chart-alt-2'></i><span class="nome-link" style="--i:1;">Admin</span></a></li>
  <li class="lista-item"><a href="usuarios.php"><i class='bx bxs-user-detail'></i><span class="nome-link" style="--i:2;">Usuários</span></a></li>
  <li class="lista-item"><a href="tickets.php"><i class='bx bx-support'></i><span class="nome-link" style="--i:3;">Tickets</span></a></li>
  <li class="espacador"></li>
  <li class="lista-item">
    <form id="formDeleteAdmin" action="../includes/deletecadastroAdmin.php" method="POST" style="display:inline;">
      <input type="hidden" name="excluir" value="1">
      <a href="#" onclick="confirmarExclusao(event)">
        <i class='bx bx-user-x'></i>
        <span class="nome-link" style="--i:4;">Excluir Admin</span>
      </a>
    </form>
  </li>
  <li class="lista-item"><a href="#" class="btn-toggle-tema"><i class='bx bx-moon'></i><span class="nome-link" style="--i:5;">Claro/Escuro</span></a></li>
  <li class="lista-item"><a href="../includes/logout.php"><i class='bx bx-log-out'></i><span class="nome-link" style="--i:6;">Sair</span></a></li>
</ul>
</nav>
<main class="main-content">
  <h1>Tickets de Suporte</h1>

  <div class="filtro-tickets">
    <a href="?filtro=abertos" class="<?= $filtro === 'abertos' ? 'ativo' : '' ?>">
      Abertos <span><?= $totalAbertos ?></span>
    </a>
    <a href="?filtro=todos" class="<?= $filtro === 'todos' ? 'ativo' : '' ?>">
      Todos <span><?= $totalTodos ?></span>
    </a>
    <a href="?filtro=respondidos" class="<?= $filtro === 'respondidos' ? 'ativo' : '' ?>">
      Respondidos <span><?= $totalRespondidos ?></span>
    </a>
  </div>
  <div class="lista-tickets">
    <?php foreach ($ticketsArray as $ticket): 
      $temResposta = !empty(trim($ticket['resposta'] ?? ''));
    ?>
      <div class="ticket-item <?= $temResposta ? 'respondido' : '' ?>" onclick="abrirModal(<?= $ticket['id'] ?>)">
        <div class="ticket-header">
          <h4><?= htmlspecialchars($ticket['nome']) ?></h4>
          <?php if ($temResposta): ?>
            <span class="status-respondido"><i class='bx bx-check'></i></span>
          <?php endif; ?>
        </div>
        <p><?= htmlspecialchars($ticket['assunto']) ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<div id="ticketModal" class="modal" style="display:none;">
  <div class="modal-content">
    <span class="fechar" onclick="fecharModal()">&times;</span>
    <h3 id="modalTitulo">Responder Ticket</h3>
    <form method="POST" action="../includes/responderTicket.php">
      <input type="hidden" name="ticket_id" id="ticket_id">
      <p><strong>De:</strong> <span id="modalNomeUsuario"></span></p>
      <p><strong>Assunto:</strong> <span id="modalAssunto"></span></p>
      <p><strong>Mensagem:</strong> <span id="modalDescricao"></span></p>
      <div id="respostaExistente" style="display:none;">
        <p class="resposta-atual"><strong>Resposta atual:</strong> <span id="respostaAtual"></span></p>
      </div>
      <div id="formResposta">
        <textarea name="resposta" id="modalResposta" rows="4" placeholder="Digite a resposta..."></textarea>
        <button type="submit">Salvar</button>
      </div>
    </form>
  </div>
</div>

<script>
const tickets = <?= json_encode($ticketsArray) ?>;

function abrirModal(id) {
  const ticket = tickets.find(t => t.id == id);
  if (!ticket) return;

  document.getElementById('ticket_id').value = ticket.id;
  document.getElementById('modalNomeUsuario').innerText = ticket.nome;
  document.getElementById('modalAssunto').innerText = ticket.assunto;
  document.getElementById('modalDescricao').innerText = ticket.descricao;
  
  const temResposta = ticket.resposta && ticket.resposta.trim() !== '';
  
  document.getElementById('modalTitulo').innerText = temResposta ? 'Editar Resposta' : 'Responder Ticket';
  
  if (temResposta) {
    document.getElementById('respostaExistente').style.display = 'block';
    document.getElementById('respostaAtual').innerText = ticket.resposta;
    document.getElementById('modalResposta').value = ticket.resposta;
  } else {
    document.getElementById('respostaExistente').style.display = 'none';
    document.getElementById('modalResposta').value = '';
  }
  
  document.getElementById('ticketModal').style.display = 'flex';
}

function fecharModal() {
  document.getElementById('ticketModal').style.display = 'none';
}
</script>

<script src="../assets/css/js/script.js"></script>
<script src="../assets/css/js/admin.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>
