<?php
session_start();
require_once '../includes/db.php';

$totalUsuarios = $conn->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()['total'];
$totalTickets = $conn->query("SELECT COUNT(*) AS total FROM suporte")->fetch_assoc()['total'];
$ticketsRespondidos = $conn->query("SELECT COUNT(*) AS total FROM suporte WHERE resposta IS NOT NULL AND TRIM(resposta) <> ''")->fetch_assoc()['total'];
$ticketsPendentes = $conn->query("SELECT COUNT(*) AS total FROM suporte WHERE resposta IS NULL OR TRIM(resposta) = ''")->fetch_assoc()['total'];

// Processamento dos filtros
$filtroDataInicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$filtroDataFim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';

// Configuração da paginação
$itemsPorPagina = 10;
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaAtual - 1) * $itemsPorPagina;

// Base da consulta SQL para contagem
$sqlContagem = "
    SELECT COUNT(*) as total
    FROM vendas v
    WHERE 1=1
";

// Adicionar condições de filtro para contagem
if (!empty($filtroDataInicio)) {
    $sqlContagem .= " AND v.data_vendas >= '$filtroDataInicio'";
}

if (!empty($filtroDataFim)) {
    $sqlContagem .= " AND v.data_vendas <= '$filtroDataFim'";
}

$resultadoContagem = $conn->query($sqlContagem);
$totalRegistros = $resultadoContagem->fetch_assoc()['total'];
$totalPaginas = ceil($totalRegistros / $itemsPorPagina);

// Base da consulta SQL
$sqlPagamentos = "
    SELECT 
        v.id, 
        v.data_vendas, 
        CONCAT(u1.nome, ' (', u1.email, ')') AS comprador,
        CONCAT(u2.nome, ' (', u2.email, ')') AS vendedor,
        p.nome AS produto,
        p.preco AS valor
    FROM vendas v
    LEFT JOIN usuarios u1 ON v.comprador_email = u1.email
    LEFT JOIN usuarios u2 ON v.fornecedor_email = u2.email
    LEFT JOIN produtos p ON v.produto_id = p.id
    WHERE 1=1
";

// Adicionar condições de filtro
if (!empty($filtroDataInicio)) {
    $sqlPagamentos .= " AND v.data_vendas >= '$filtroDataInicio'";
}

if (!empty($filtroDataFim)) {
    $sqlPagamentos .= " AND v.data_vendas <= '$filtroDataFim'";
}

// Ordenação e limite para paginação
$sqlPagamentos .= " ORDER BY v.data_vendas DESC LIMIT $offset, $itemsPorPagina";

$pagamentos = $conn->query($sqlPagamentos);
$totalPagamentos = $conn->query("SELECT COUNT(*) AS total FROM vendas")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Painel do Administrador</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
  <h1>Bem-vindo(a) ao Painel do Administrador</h1>
  <div class="cards">
    <div class="card">
      <h3>Usuários cadastrados</h3>
      <p><?= $totalUsuarios ?></p>
    </div>
    <div class="card">
      <h3>Tickets recebidos</h3>
      <p><?= $totalTickets ?></p>
    </div>
    <div class="card">
      <h3>Tickets respondidos</h3>
      <p><?= $ticketsRespondidos ?></p>
    </div>
    <div class="card">
      <h3>Tickets pendentes</h3>
      <p><?= $ticketsPendentes ?></p>
    </div>
  </div>

  <div class="pagamentos-section">    <div class="section-header">
      <h2>Monitoramento de Pagamentos</h2>
      <div class="status-badges">
        <span class="status-badge total"><?= $totalPagamentos ?> Total</span>
      </div>
    </div>
      <div class="pagamentos-filtros">
      <form action="" method="GET">
        <div class="filtro-item">
          <label for="data_inicio">Data Início:</label>
          <input type="date" name="data_inicio" id="data_inicio" value="<?= $filtroDataInicio ?>">
        </div>
        <div class="filtro-item">
          <label for="data_fim">Data Fim:</label>
          <input type="date" name="data_fim" id="data_fim" value="<?= $filtroDataFim ?>">
        </div>
        <div class="filtro-item">
          <button type="submit" class="btn-filtrar">Filtrar</button>
        </div>
      </form>
    </div>

    <div class="pagamentos-container">      <table class="pagamentos-table">
        <thead>          <tr>
            <th>ID</th>
            <th>Data</th>
            <th>Comprador</th>
            <th>Produto</th>
            <th>Valor</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($pagamentos && $pagamentos->num_rows > 0): ?>
            <?php while($row = $pagamentos->fetch_assoc()): ?>
              <tr>
                <td>#<?= $row['id'] ?></td>
                <td><?= date('d/m/Y', strtotime($row['data_vendas'])) ?></td>
                <td><?= htmlspecialchars($row['comprador']) ?></td>
                <td><?= htmlspecialchars($row['produto']) ?></td>
                <td>R$ <?= number_format($row['valor'], 2, ',', '.') ?></td>
              </tr>
            <?php endwhile; ?>          <?php else: ?>
            <tr>
              <td colspan="5" class="empty-table">Nenhum pagamento encontrado.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

      <?php if ($totalPaginas > 1): ?>
      <div class="paginacao">
        <?php if ($paginaAtual > 1): ?>
          <a href="?pagina=1<?= !empty($filtroDataInicio) ? '&data_inicio='.$filtroDataInicio : '' ?><?= !empty($filtroDataFim) ? '&data_fim='.$filtroDataFim : '' ?>" class="pagina-link">Primeira</a>
          <a href="?pagina=<?= $paginaAtual - 1 ?><?= !empty($filtroDataInicio) ? '&data_inicio='.$filtroDataInicio : '' ?><?= !empty($filtroDataFim) ? '&data_fim='.$filtroDataFim : '' ?>" class="pagina-link">Anterior</a>
        <?php endif; ?>
        
        <?php
        $inicioRange = max(1, $paginaAtual - 2);
        $fimRange = min($totalPaginas, $paginaAtual + 2);
        
        for ($i = $inicioRange; $i <= $fimRange; $i++): ?>
          <a href="?pagina=<?= $i ?><?= !empty($filtroDataInicio) ? '&data_inicio='.$filtroDataInicio : '' ?><?= !empty($filtroDataFim) ? '&data_fim='.$filtroDataFim : '' ?>" class="pagina-link <?= $i == $paginaAtual ? 'ativa' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        
        <?php if ($paginaAtual < $totalPaginas): ?>
          <a href="?pagina=<?= $paginaAtual + 1 ?><?= !empty($filtroDataInicio) ? '&data_inicio='.$filtroDataInicio : '' ?><?= !empty($filtroDataFim) ? '&data_fim='.$filtroDataFim : '' ?>" class="pagina-link">Próxima</a>
          <a href="?pagina=<?= $totalPaginas ?><?= !empty($filtroDataInicio) ? '&data_inicio='.$filtroDataInicio : '' ?><?= !empty($filtroDataFim) ? '&data_fim='.$filtroDataFim : '' ?>" class="pagina-link">Última</a>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</main>
<script src="../assets/css/js/admin.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../assets/css/js/script.js"></script>
</body>
</html>
