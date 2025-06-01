<?php
session_start();
require_once '../includes/db.php';
include '../includes/verificar_login.php';

// Obter email do usuário logado
$email_usuario = $_SESSION['usuario']['email'];

try {
    // Buscar compras do usuário
    $stmt = $conn->prepare("SELECT 
            ped.id as pedido_id,
            ped.data_pedido as data_compra,
            ped.status,
            p.nome,
            p.descricao,
            p.preco,
            COALESCE(p.imagem, '') as imagem,
            COALESCE(pp.quantidade, 1) as quantidade
        FROM pedidos ped
        INNER JOIN produtos_pedido pp ON ped.id = pp.pedido_id
        INNER JOIN produtos p ON pp.produto_id = p.id
        WHERE ped.comprador_email = ?
        ORDER BY ped.data_pedido DESC");

    if (!$stmt) {
        throw new Exception("Erro ao preparar statement: " . $conn->error);
    }

    $stmt->bind_param("s", $email_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $compras = $result->fetch_all(MYSQLI_ASSOC);

    // Calcular estatísticas
    $totalCompras = count($compras);
    $valorTotal = 0;

    foreach ($compras as $compra) {
        $valorTotal += ($compra['preco'] * $compra['quantidade']);
    }

    $stmt->close();

} catch (Exception $e) {
    $compras = [];
    $totalCompras = 0;
    $valorTotal = 0;
    error_log("Erro ao buscar compras: " . $e->getMessage());
}

// Função para formatar status em português
function formatarStatus($status) {
    $statusMap = [
        'pendente' => 'Pendente',
        'pago' => 'Pago',
        'enviado' => 'Enviado',
        'cancelado' => 'Cancelado'
    ];
    return $statusMap[$status] ?? ucfirst($status);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Compras - Tigrano</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/compras.css">
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
        <li class="lista-item ativar"><a href="../pages/compras.php"><i class='bx bx-shopping-bag'></i><span class="nome-link" style="--i:4;">Compras</span></a></li>
        <li class="espacador"></li>
        <li class="lista-item"><a href="#" class="btn-toggle-tema"><i class='bx bx-moon'></i><span class="nome-link" style="--i:5;">Claro/Escuro</span></a></li>
        <li class="lista-item"><a href="../pages/suporteUsuario.php"><i class='bx bx-info-circle'></i><span class="nome-link" style="--i:6;">Ajuda</span></a></li>
        <li class="lista-item"><a href="../pages/configuracoes.php"><i class='bx bx-cog'></i><span class="nome-link" style="--i:7;">Configurações</span></a></li>
        <li class="lista-item"><a href="../pages/perfil.php"><i class='bx bx-user'></i><span class="nome-link" style="--i:8;">Perfil</span></a></li>
    </ul>
</nav>

<main class="main-content">
    <div class="content-wrapper">
        <header class="page-header">
            <div class="header-content">
                <div class="title-section">
                    <h1><i class='bx bx-shopping-bag'></i> Minhas Compras</h1>
                    <p>Histórico de todas as suas compras realizadas</p>
                </div>
            </div>
        </header>

        <!-- Estatísticas das Compras -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class='bx bx-package'></i>
                </div>
                <div class="stat-info">
                    <h3><?= $totalCompras ?></h3>
                    <p>Total de Compras</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class='bx bx-money'></i>
                </div>
                <div class="stat-info">
                    <h3>R$ <?= number_format($valorTotal, 2, ',', '.') ?></h3>
                    <p>Valor Total Gasto</p>
                </div>
            </div>
        </div>

        <!-- Lista de Compras -->
        <div class="compras-section">
            <div class="section-header">
                <h2>Histórico de Compras</h2>
                <span class="total-items"><?= $totalCompras ?> item(s)</span>
            </div>

            <?php if (empty($compras)): ?>
                <div class="empty-state">
                    <i class='bx bx-package'></i>
                    <h3>Nenhuma compra encontrada</h3>
                    <p>Você ainda não realizou nenhuma compra. Explore nosso marketplace!</p>
                    <a href="marketplace.php" class="btn-primary">
                        <i class='bx bx-store'></i>
                        Ir ao Marketplace
                    </a>
                </div>
            <?php else: ?>
                <div class="compras-grid">
                    <?php foreach ($compras as $compra): ?>
                        <div class="compra-card">
                            <div class="produto-imagem">
                                <?php if (!empty($compra['imagem'])): ?>
                                    <img src="../uploads/<?= htmlspecialchars($compra['imagem']) ?>" 
                                         alt="<?= htmlspecialchars($compra['nome']) ?>"
                                         onerror="this.parentElement.innerHTML='<div class=\'imagem-placeholder\'><i class=\'bx bx-image\'></i></div>'">
                                <?php else: ?>
                                    <div class="imagem-placeholder">
                                        <i class='bx bx-image'></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="produto-info">
                                <div class="produto-header">
                                    <h3 class="produto-nome"><?= htmlspecialchars($compra['nome']) ?></h3>
                                    <div class="preco-quantidade">
                                        <span class="produto-preco">R$ <?= number_format($compra['preco'], 2, ',', '.') ?></span>
                                        <?php if ($compra['quantidade'] > 1): ?>
                                            <span class="quantidade">x<?= $compra['quantidade'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <p class="produto-descricao">
                                    <?= htmlspecialchars(strlen($compra['descricao']) > 100 ? substr($compra['descricao'], 0, 100) . '...' : $compra['descricao']) ?>
                                </p>

                                <div class="compra-detalhes">
                                    <div class="data-compra">
                                        <i class='bx bx-calendar'></i>
                                        <span><?= date('d/m/Y H:i', strtotime($compra['data_compra'])) ?></span>
                                    </div>

                                    <div class="pedido-id">
                                        <i class='bx bx-receipt'></i>
                                        <span>Pedido #<?= str_pad($compra['pedido_id'], 6, '0', STR_PAD_LEFT) ?></span>
                                    </div>
                                </div>

                                <div class="status-quantidade-container">
                                    <span class="status-badge status-<?= strtolower($compra['status']) ?>">
                                        <?= formatarStatus($compra['status']) ?>
                                    </span>
                                    
                                    <?php if ($compra['quantidade'] > 1): ?>
                                        <div class="valor-total">
                                            <small>Total: R$ <?= number_format($compra['preco'] * $compra['quantidade'], 2, ',', '.') ?></small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script src="../assets/js/script.js"></script>
</body>
</html>