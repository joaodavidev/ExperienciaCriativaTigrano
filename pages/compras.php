<?php
session_start();
require_once '../includes/db.php';
include '../includes/verificar_login.php';

// Obter email do usuário logado
$email_usuario = $_SESSION['usuario']['email'];

try {    // Buscar compras do usuário da tabela vendas (onde as compras reais são registradas)
    $stmt = $conn->prepare("SELECT 
            v.id as venda_id,
            v.data_vendas as data_compra,
            v.quantidade_vendas as quantidade,
            p.id as produto_id,
            p.nome,
            p.descricao,
            p.preco,
            p.categoria,
            p.arquivo_produto,
            u_vendedor.nome as vendedor_nome,
            v.fornecedor_email,
            av.id as avaliacao_id,
            av.estrelas,
            av.comentario
        FROM vendas v
        INNER JOIN produtos p ON v.produto_id = p.id
        LEFT JOIN usuarios u_vendedor ON v.fornecedor_email = u_vendedor.email
        LEFT JOIN avaliacao av ON (av.produto_id = p.id AND av.usuario_email = v.comprador_email)
        WHERE v.comprador_email = ?
        ORDER BY v.data_vendas DESC");

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

// Função para formatar data
function formatarData($data) {
    return date('d/m/Y', strtotime($data));
}

// Função para obter ícone da categoria
function obterIconeCategoria($categoria) {
    $icones = [
        'Eletrônicos' => 'bx-chip',
        'Roupas' => 'bx-closet',
        'Livros' => 'bx-book',
        'Calçados' => 'bx-walk',
        'Casa' => 'bx-home',
        'Esporte' => 'bx-football'
    ];
    return $icones[$categoria] ?? 'bx-package';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <title>Minhas Compras - Tigrano</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/compras.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <div class="compras-grid">                    <?php foreach ($compras as $compra): ?>
                        <div class="compra-card">
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
                                        <span><?= formatarData($compra['data_compra']) ?></span>
                                    </div>

                                    <div class="vendedor-info">
                                        <i class='bx bx-store'></i>
                                        <span>Vendido por: <?= htmlspecialchars($compra['vendedor_nome'] ?? 'Tigrano') ?></span>
                                    </div>
                                </div>                                <div class="status-quantidade-container">
                                    <span class="status-badge status-finalizado">
                                        <i class='bx bx-check-circle'></i>
                                        Compra Finalizada
                                    </span>
                                    
                                    <?php if ($compra['quantidade'] > 1): ?>
                                        <div class="valor-total">
                                            <small>Total: R$ <?= number_format($compra['preco'] * $compra['quantidade'], 2, ',', '.') ?></small>
                                        </div>
                                    <?php endif; ?>
                                </div>                                <!-- Botão de download se arquivo disponível -->
                                <?php if (!empty($compra['arquivo_produto'])): ?>
                                    <div class="download-section">
                                        <a href="../includes/download_arquivo.php?arquivo=<?= urlencode($compra['arquivo_produto']) ?>" 
                                           class="btn-download" 
                                           title="Baixar arquivo do produto">
                                            <i class='bx bx-download'></i>
                                            Baixar Arquivo
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <!-- Seção de Avaliação -->
                                <div class="avaliacao-section">
                                    <?php if ($compra['avaliacao_id']): ?>
                                        <!-- Avaliação Existente -->
                                        <div class="avaliacao-existente">
                                            <h4><i class='bx bx-star'></i> Sua Avaliação</h4>
                                            <div class="estrelas-display">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class='bx bx<?= $i <= $compra['estrelas'] ? 's' : '' ?>-star'></i>
                                                <?php endfor; ?>
                                                <span class="nota-texto">(<?= $compra['estrelas'] ?>/5)</span>
                                            </div>
                                            <?php if (!empty($compra['comentario'])): ?>
                                                <p class="comentario-texto"><?= htmlspecialchars($compra['comentario']) ?></p>
                                            <?php endif; ?>
                                            <button class="btn-editar-avaliacao" onclick="toggleAvaliacaoForm(<?= $compra['produto_id'] ?>)">
                                                <i class='bx bx-edit'></i> Editar Avaliação
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <!-- Botão para Avaliar -->
                                        <button class="btn-avaliar" onclick="toggleAvaliacaoForm(<?= $compra['produto_id'] ?>)">
                                            <i class='bx bx-star'></i>
                                            Avaliar Produto
                                        </button>
                                    <?php endif; ?>

                                    <!-- Formulário de Avaliação (oculto por padrão) -->
                                    <div id="form-avaliacao-<?= $compra['produto_id'] ?>" class="form-avaliacao" style="display: none;">
                                        <form action="../includes/createAvaliacao.php" method="POST">
                                            <input type="hidden" name="produto_id" value="<?= $compra['produto_id'] ?>">
                                            
                                            <div class="estrelas-input">
                                                <label>Sua nota:</label>
                                                <div class="estrelas-selecao">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <input type="radio" name="estrelas" value="<?= $i ?>" id="star-<?= $compra['produto_id'] ?>-<?= $i ?>" 
                                                               <?= ($compra['avaliacao_id'] && $compra['estrelas'] == $i) ? 'checked' : '' ?> required>
                                                        <label for="star-<?= $compra['produto_id'] ?>-<?= $i ?>" class="star-label">
                                                            <i class='bx bx-star'></i>
                                                        </label>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>

                                            <div class="comentario-input">
                                                <label for="comentario-<?= $compra['produto_id'] ?>">Comentário (opcional):</label>
                                                <textarea name="comentario" id="comentario-<?= $compra['produto_id'] ?>" 
                                                          placeholder="Conte sua experiência com o produto..."><?= $compra['avaliacao_id'] ? htmlspecialchars($compra['comentario']) : '' ?></textarea>
                                            </div>

                                            <div class="form-actions">
                                                <button type="submit" class="btn-salvar-avaliacao">
                                                    <i class='bx bx-check'></i>
                                                    <?= $compra['avaliacao_id'] ? 'Atualizar' : 'Avaliar' ?>
                                                </button>
                                                <button type="button" class="btn-cancelar" onclick="toggleAvaliacaoForm(<?= $compra['produto_id'] ?>)">
                                                    <i class='bx bx-x'></i>
                                                    Cancelar
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>    </div>
</main>

<script src="../assets/css/js/script.js"></script>
<script>
function toggleAvaliacaoForm(produtoId) {
    const form = document.getElementById('form-avaliacao-' + produtoId);
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}

// Efeito visual nas estrelas
document.addEventListener('DOMContentLoaded', function() {
    const starLabels = document.querySelectorAll('.star-label');
    
    starLabels.forEach(label => {
        label.addEventListener('mouseover', function() {
            const starValue = this.previousElementSibling.value;
            const produtoId = this.previousElementSibling.id.split('-')[1];
            highlightStars(produtoId, starValue);
        });
    });
    
    document.querySelectorAll('.estrelas-selecao').forEach(container => {
        container.addEventListener('mouseleave', function() {
            const produtoId = this.closest('.form-avaliacao').id.split('-')[2];
            const checkedStar = this.querySelector('input[type="radio"]:checked');
            const value = checkedStar ? checkedStar.value : 0;
            highlightStars(produtoId, value);
        });
    });
});

function highlightStars(produtoId, rating) {
    const stars = document.querySelectorAll(`#form-avaliacao-${produtoId} .star-label i`);
    stars.forEach((star, index) => {
        if (index < rating) {
            star.className = 'bx bxs-star';
        } else {
            star.className = 'bx bx-star';
        }
    });
}

// SweetAlert para mensagens de sucesso
<?php if (isset($_GET['avaliacao_criada'])): ?>
    Swal.fire({
        icon: 'success',
        title: 'Avaliação enviada!',
        text: 'Sua avaliação foi salva com sucesso.',
        timer: 2000,
        showConfirmButton: false
    });
    // Remove o parâmetro da URL
    const url = new URL(window.location);
    url.searchParams.delete('avaliacao_criada');
    window.history.replaceState({}, document.title, url);
<?php endif; ?>

<?php if (isset($_GET['avaliacao_atualizada'])): ?>
    Swal.fire({
        icon: 'success',
        title: 'Avaliação atualizada!',
        text: 'Sua avaliação foi atualizada com sucesso.',
        timer: 2000,
        showConfirmButton: false
    });
    // Remove o parâmetro da URL
    const url = new URL(window.location);
    url.searchParams.delete('avaliacao_atualizada');
    window.history.replaceState({}, document.title, url);
<?php endif; ?>
</script>
</body>
</html>