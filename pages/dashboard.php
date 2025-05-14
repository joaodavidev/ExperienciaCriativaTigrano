<?php
include '../includes/db.php';

$emailVendedor = 'vendedor@email.com';


// Saldo total
$querySaldo = $conn->prepare("
    SELECT SUM(v.quantidade_vendas * p.preco) AS saldo_total
    FROM vendas v
    JOIN produtos p ON v.produto_id = p.id
    WHERE p.vendedor_email = ?
");
if(!$querySaldo) { 
  die('o erro é: '.$conn->error);
}


$querySaldo->bind_param("s", $emailVendedor);
$querySaldo->execute();
$resultSaldo = $querySaldo->get_result();
$saldo = $resultSaldo->fetch_assoc()['saldo_total'] ?? 0.00;

// Total de vendas no mês atual
$queryVendas = $conn->query("
    SELECT SUM(v.quantidade_vendas * p.preco) AS total_vendas
    FROM vendas v
    JOIN produtos p ON v.produto_id = p.id
    WHERE MONTH(v.data_vendas) = MONTH(CURRENT_DATE())
      AND YEAR(v.data_vendas) = YEAR(CURRENT_DATE())
");
$vendasMes = $queryVendas->fetch_assoc()['total_vendas'] ?? 0.00;

// TOTAL DE VENDAS
$queryTotalVendas = $conn->query("
    SELECT SUM(v.quantidade_vendas * p.preco) AS total_vendas_lifetime
    FROM vendas v
    JOIN produtos p ON v.produto_id = p.id
");
$vendasTotais = $queryTotalVendas->fetch_assoc()['total_vendas_lifetime'] ?? 0.00;

// Total de clientes  
$queryClientes = $conn->prepare("
    SELECT COUNT(DISTINCT pped.pedido_id) AS total_clientes
    FROM produtos p
    JOIN produtos_pedido pped ON p.id = pped.produto_id
    JOIN pedidos ped ON pped.pedido_id = ped.id
    WHERE p.vendedor_email = ?
");
$queryClientes->bind_param("s", $emailVendedor);
$queryClientes->execute();
$result = $queryClientes->get_result();
$totalClientes = $result->fetch_assoc()['total_clientes'] ?? 0;

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

        <!-- Separador flexível -->
        <li class="espacador"></li>

        <!-- Itens inferiores -->
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
          <a href="#">
            <i class='bx bx-log-out'></i>
            <span class="nome-link" style="--i:7;">LogOut</span>
          </a>
        </li>
      </ul>
  </nav>

  <main class="main-content">
    <div class="info-cards">
  <div class="info-card purple">
    <i class='bx bx-wallet'></i>
    <div>
      <span>Saldo Disponível</span>
      <h3>R$ <?= number_format($saldo, 2, ',', '.') ?></h3>
    </div>
  </div>
  <div class="info-card green">
    <i class='bx bx-line-chart'></i>
    <div>
      <span>Vendas este mês</span>
      <h3>R$ <?= number_format($vendasMes, 2, ',', '.') ?></h3>
    </div>
  </div>
  <div class="info-card blue">
    <i class='bx bx-cart'></i>
    <div>
      <span>Vendas totais</span>
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

    <div class="main-header">
      <h1>Dashboard</h1>
    </div>

    <div class="search-bar">
        <input type="text" name="nome" placeholder="Buscar Produtos"/>
      <button><i class='bx bx-search'></i></button>
    </div>
  </main>
  <script src="../assets/css/js/script.js"></script>
</body>
</html>
