<?php
session_start();
include '../includes/db.php';
include '../includes/verificar_login.php';

$emailVendedor = $_SESSION['usuario']['email'];

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

// Total de vendas no mês atual (apenas do vendedor)
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

// TOTAL DE VENDAS (apenas do vendedor)
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

// Verificar se a tabela vendas existe e contar os clientes
$checkVendas = $conn->query("SHOW TABLES LIKE 'vendas'");
$tabelaVendasExiste = $checkVendas->num_rows > 0;

// Total de clientes baseado nas vendas
if ($tabelaVendasExiste) {
    $queryClientes = $conn->prepare("
        SELECT COUNT(DISTINCT cliente_email) AS total_clientes
        FROM vendas v
        JOIN produtos p ON v.produto_id = p.id
        WHERE p.vendedor_email = ?
    ");
    if (!$queryClientes) {
        // Tentativa alternativa se a coluna cliente_email não existir
        $queryClientes = $conn->prepare("
            SELECT 
                COUNT(DISTINCT CASE WHEN quantidade_vendas > 0 THEN produto_id ELSE null END) AS total_clientes
            FROM vendas v
            JOIN produtos p ON v.produto_id = p.id
            WHERE p.vendedor_email = ?
        ");
    }
} else {
    // Consulta alternativa usando pedidos
    $queryClientes = $conn->prepare("
        SELECT COUNT(DISTINCT ped.comprador_email) AS total_clientes
        FROM produtos p
        JOIN produtos_pedido pped ON p.id = pped.produto_id
        JOIN pedidos ped ON pped.pedido_id = ped.id
        WHERE p.vendedor_email = ?
    ");
}

if(!$queryClientes) {
    // Se tudo falhar, use 0 como valor padrão
    $totalClientes = 0;
} else {
    $queryClientes->bind_param("s", $emailVendedor);
    $queryClientes->execute();
    $result = $queryClientes->get_result();
    $totalClientes = $result->fetch_assoc()['total_clientes'] ?? 0;
}

// Verificamos se existe a tabela vendas
$checkVendas = $conn->query("SHOW TABLES LIKE 'vendas'");
$tabelaVendasExiste = $checkVendas->num_rows > 0;

// Buscar as últimas compras 
$ultimasCompras = [];

if ($tabelaVendasExiste) {
        // Verificar qual campo de email existe na tabela vendas
    $queryCheckEmail = $conn->query("SHOW COLUMNS FROM vendas WHERE Field = 'cliente_email'");
    if ($queryCheckEmail->num_rows > 0) {
        $colunaEmail = 'v.cliente_email';
    } else {
        $queryCheckAlternativo = $conn->query("SHOW COLUMNS FROM vendas WHERE Field = 'email_cliente'");
        if ($queryCheckAlternativo->num_rows > 0) {
            $colunaEmail = 'v.email_cliente';
        } else {
            $colunaEmail = 'NULL';  // caso não exista nenhuma das colunas
        }
    }
    
    // Tentar a partir da tabela vendas usando a coluna de email do cliente correta
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

    
    if(!$queryUltimasCompras) {
        // Estratégia alternativa - consulta mais simples
        $queryUltimasCompras = $conn->prepare("
            SELECT 
                p.nome AS nome_produto,
                v.data_vendas AS data_pedido,
                'Cliente' AS nome_cliente
            FROM vendas v
            JOIN produtos p ON v.produto_id = p.id
            WHERE p.vendedor_email = ?
            ORDER BY v.data_vendas DESC
            LIMIT 5
        ");
    }
    
    if($queryUltimasCompras) {
        $queryUltimasCompras->bind_param("s", $emailVendedor);
        $queryUltimasCompras->execute();
        $resultUltimasCompras = $queryUltimasCompras->get_result();
        
        while ($row = $resultUltimasCompras->fetch_assoc()) {
            // Se temos o email do cliente mas não o nome, vamos buscar o nome
            if (isset($row['email_cliente']) && $row['nome_cliente'] == 'Cliente') {
                $emailCliente = $row['email_cliente'];
                $queryNomeCliente = $conn->prepare("SELECT nome FROM usuarios WHERE email = ? LIMIT 1");
                $queryNomeCliente->bind_param("s", $emailCliente);
                $queryNomeCliente->execute();
                $resultNome = $queryNomeCliente->get_result();
                if ($resultNome && $nomeRow = $resultNome->fetch_assoc()) {
                    $row['nome_cliente'] = $nomeRow['nome'];
                }
            }
            $ultimasCompras[] = $row;
        }
    }
}

// Se não encontrou na tabela vendas, tenta na tabela pedidos
if (empty($ultimasCompras)) {
    $queryUltimasCompras = $conn->prepare("
        SELECT 
            COALESCE(
                u.nome,
                (SELECT nome FROM usuarios WHERE email = ped.comprador_email LIMIT 1),
                'Cliente'
            ) AS nome_cliente, 
            p.nome AS nome_produto, 
            ped.data_pedido,
            ped.comprador_email AS email_cliente
        FROM produtos p
        JOIN produtos_pedido pped ON p.id = pped.produto_id
        JOIN pedidos ped ON pped.pedido_id = ped.id
        LEFT JOIN usuarios u ON ped.comprador_email = u.email
        WHERE p.vendedor_email = ?
        ORDER BY ped.data_pedido DESC
        LIMIT 5
    ");
    
    if(!$queryUltimasCompras) {
        // Consulta simplificada como último recurso
        $queryUltimasCompras = $conn->prepare("
            SELECT 
                'Cliente' AS nome_cliente, 
                p.nome AS nome_produto, 
                ped.data_pedido
            FROM produtos p
            JOIN produtos_pedido pped ON p.id = pped.produto_id
            JOIN pedidos ped ON pped.pedido_id = ped.id
            WHERE p.vendedor_email = ?
            ORDER BY ped.data_pedido DESC
            LIMIT 5
        ");
    }
    
    if($queryUltimasCompras) {
        $queryUltimasCompras->bind_param("s", $emailVendedor);
        $queryUltimasCompras->execute();
        $resultUltimasCompras = $queryUltimasCompras->get_result();
        
        while ($row = $resultUltimasCompras->fetch_assoc()) {
            // Se temos o email do cliente mas não o nome, vamos buscar o nome
            if (isset($row['email_cliente']) && $row['nome_cliente'] == 'Cliente') {
                $emailCliente = $row['email_cliente'];
                $queryNomeCliente = $conn->prepare("SELECT nome FROM usuarios WHERE email = ? LIMIT 1");
                $queryNomeCliente->bind_param("s", $emailCliente);
                $queryNomeCliente->execute();
                $resultNome = $queryNomeCliente->get_result();
                if ($resultNome && $nomeRow = $resultNome->fetch_assoc()) {
                    $row['nome_cliente'] = $nomeRow['nome'];
                }
            }
            $ultimasCompras[] = $row;
        }
    }
}

// Debug: mostrar quantas compras foram encontradas
// echo "<!-- Debug: " . count($ultimasCompras) . " compras encontradas -->";

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
