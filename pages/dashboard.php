<?php
session_start();
include '../includes/db.php';
include '../includes/verificar_login.php';

$emailVendedor = $_SESSION['usuario']['email'];

// Saldo total
$querySaldo = $conn->prepare("
    SELECT SUM(v.quantidade_vendas * p.preco) AS saldo_total
    FROM vendas v
    JOIN produtos p ON v.produto_id = p.id
    WHERE p.vendedor_email = ?
");

$querySaldo->bind_param("s", $emailVendedor);
$querySaldo->execute();
$resultSaldo = $querySaldo->get_result();
$saldo = $resultSaldo->fetch_assoc()['saldo_total'] ?? 0.00;

// Total de vendas no mês atual
$queryVendasMes = $conn->prepare("
    SELECT SUM(v.quantidade_vendas * p.preco) AS total_vendas
    FROM vendas v
    JOIN produtos p ON v.produto_id = p.id
    WHERE MONTH(v.data_vendas) = MONTH(CURRENT_DATE())
    AND YEAR(v.data_vendas) = YEAR(CURRENT_DATE())
    AND p.vendedor_email = ?
");
$queryVendasMes->bind_param("s", $emailVendedor);
$queryVendasMes->execute();
$resultVendasMes = $queryVendasMes->get_result();
$vendasMes = $resultVendasMes->fetch_assoc()['total_vendas'] ?? 0.00;

// Total de vendas (lifetime)
$queryTotalVendas = $conn->prepare("
    SELECT SUM(v.quantidade_vendas * p.preco) AS total_vendas_lifetime
    FROM vendas v
    JOIN produtos p ON v.produto_id = p.id
    WHERE p.vendedor_email = ?
");
$queryTotalVendas->bind_param("s", $emailVendedor);
$queryTotalVendas->execute();
$resultTotalVendas = $queryTotalVendas->get_result();
$vendasTotais = $resultTotalVendas->fetch_assoc()['total_vendas_lifetime'] ?? 0.00;

// Total de clientes
$queryClientes = $conn->prepare("
    SELECT COUNT(DISTINCT comprador_email) AS total_clientes
    FROM vendas v
    JOIN produtos p ON v.produto_id = p.id
    WHERE p.vendedor_email = ?
");
$queryClientes->bind_param("s", $emailVendedor);
$queryClientes->execute();
$result = $queryClientes->get_result();
$totalClientes = $result->fetch_assoc()['total_clientes'] ?? 0;

// Últimas compras
$queryUltimasCompras = $conn->prepare("
    SELECT 
        p.nome AS nome_produto,
        v.data_vendas AS data_pedido,
        u.nome AS nome_cliente,
        v.comprador_email AS email_cliente
    FROM vendas v
    JOIN produtos p ON v.produto_id = p.id
    JOIN usuarios u ON v.comprador_email = u.email
    WHERE p.vendedor_email = ?
    ORDER BY v.data_vendas DESC
    LIMIT 5
");

$queryUltimasCompras->bind_param("s", $emailVendedor);
$queryUltimasCompras->execute();
$resultUltimasCompras = $queryUltimasCompras->get_result();
$ultimasCompras = [];

while ($row = $resultUltimasCompras->fetch_assoc()) {
    $ultimasCompras[] = $row;
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Produtos - Tigrano</title>
  <link rel="stylesheet" href="../assets/css/dashboard.css"/>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
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
</nav>  <main class="main-content">    <div class="main-header">
      <h1>Dashboard</h1>
      <div class="header-actions">
        <div class="search-bar">
          <input type="text" name="nome" placeholder="Buscar Produtos"/>
          <button><i class='bx bx-search'></i></button>
        </div>
      </div>
    </div>
    
    <div class="info-cards">
      <div class="info-card purple">
        <i class='bx bx-wallet'></i>
        <div>
          <span>Saldo Disponível</span>
          <h3>R$ <?= number_format($saldo, 2, ',', '.') ?></h3>
        </div>
      </div>
      <div class="info-card blue">
        <i class='bx bx-cart'></i>
        <div>
          <span>Vendas Totais</span>
          <h3>R$ <?= number_format($vendasTotais, 2, ',', '.') ?></h3>
        </div>
      </div>
      <div class="info-card pink">
        <i class='bx bx-group'></i>
        <div>
          <span>Clientes</span>
          <h3><?= $totalClientes ?></h3>
        </div>
      </div>
    </div>

    <div class="recent-purchases">
      <h2>Compras Recentes</h2>      <div class="purchases-list">        <?php if (empty($ultimasCompras)): ?>
          <div class="no-purchases">
            <i class='bx bx-package'></i>
            <p>Nenhuma venda realizada ainda.</p>
            <small>Quando você vender seus produtos, as compras dos clientes aparecerão aqui.</small>
          </div>
        <?php else: ?>
          <?php foreach ($ultimasCompras as $compra): ?>
            <div class="purchase-item">
              <div class="purchase-info">
                <div class="client-avatar">
                  <i class='bx bx-user'></i>
                </div>
                <div class="purchase-details">
                  <h3><?= htmlspecialchars($compra['nome_cliente']) ?></h3>
                  <p>comprou <strong><?= htmlspecialchars($compra['nome_produto']) ?></strong></p>
                  <span class="purchase-date"><?= date('d/m/Y', strtotime($compra['data_pedido'])) ?></span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </main>  <script src="../assets/css/js/script.js"></script>
  <script>
    function toggleDebugInfo() {
      const debugInfo = document.getElementById('debug-info');
      if (debugInfo) {
        debugInfo.style.display = debugInfo.style.display === 'none' ? 'block' : 'none';
      }
    }
  </script>
</body>
</html>
