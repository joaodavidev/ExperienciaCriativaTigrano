<?php
session_start();
include '../includes/db.php';
include '../includes/verificar_login.php';

$mensagem_erro = null;
$produtos = [];

// Configuração padrão de preço
$precoMin = isset($_GET['preco_min']) ? floatval($_GET['preco_min']) : 0;
$precoMax = isset($_GET['preco_max']) ? floatval($_GET['preco_max']) : 1000;

// Obter o preço mínimo e máximo disponíveis no banco
$sql_precos = "SELECT MIN(preco) as min_preco, MAX(preco) as max_preco FROM produtos WHERE status = 'Ativo'";
$result_precos = $conn->query($sql_precos);
$precos = $result_precos->fetch_assoc();

$precoMinDB = 0;
$precoMaxDB = isset($precos['max_preco']) ? floatval($precos['max_preco']) : 1000;

// Se não foi definido via GET, usa os valores do banco
if (!isset($_GET['preco_min'])) $precoMin = $precoMinDB;
if (!isset($_GET['preco_max'])) $precoMax = $precoMaxDB;

// verifica a busca por nome no método GET
if (isset($_GET['nome']) && !empty(trim($_GET['nome']))) {    $nome = trim($_GET['nome']);    $sql = "SELECT p.id, p.nome AS nome_produto, p.categoria, p.preco, p.descricao, u.nome AS nome_vendedor,
            AVG(CAST(av.estrelas AS DECIMAL(2,1))) as media_avaliacao,
            COUNT(av.id) as total_avaliacoes
            FROM produtos p
            JOIN usuarios u ON p.vendedor_email = u.email
            LEFT JOIN avaliacao av ON p.id = av.produto_id
            WHERE p.nome = ? AND p.status = 'Ativo' 
            AND p.preco BETWEEN ? AND ?
            GROUP BY p.id, p.nome, p.categoria, p.preco, p.descricao, u.nome";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdd", $nome, $precoMin, $precoMax);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $produtos[] = $row;
        }
    } else {
        $mensagem_erro = "Produto não encontrado!";
    }

    $stmt->close();
} else {    $sql = "SELECT p.id, p.nome AS nome_produto, p.categoria, p.preco, p.descricao, u.nome AS nome_vendedor,
            AVG(CAST(av.estrelas AS DECIMAL(2,1))) as media_avaliacao,
            COUNT(av.id) as total_avaliacoes
            FROM produtos p
            LEFT JOIN usuarios u ON p.vendedor_email = u.email
            LEFT JOIN avaliacao av ON p.id = av.produto_id
            WHERE p.status = 'Ativo' AND p.preco BETWEEN ? AND ?
            GROUP BY p.id, p.nome, p.categoria, p.preco, p.descricao, u.nome";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dd", $precoMin, $precoMax);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $produtos[] = $row;
        }
    } else {
        $mensagem_erro = "Nenhum produto ativo encontrado.";
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tigrano Marketplace</title>
  <link rel="stylesheet" href="../assets/css/marketplace.css">
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
    <div class="content-wrapper">
      <header>
        <section class="marketplace-header">          <div class="marketplace-title">
            <div>
              <h1>Marketplace</h1>
              <p>Encontre aqui os melhores infoprodutos.</p>
            </div>
            <a href="../pages/carrinho.php" class="cart-icon">
              <i class='bx bx-cart'></i>
            </a>
          </div>
        </section>
        
        <div class="search-bar">
          <form method="GET" action="marketplace.php">
            <div class="search-input">
              <input type="text" name="nome" placeholder="Buscar produto..." value="<?= isset($_GET['nome']) ? htmlspecialchars($_GET['nome']) : '' ?>">
              <button type="submit"><i class='bx bx-search'></i></button>
            </div>
            
            <div class="price-filter">
              <div class="price-inputs">                <div class="input-group">
                  <span class="price-label">Min:</span>
                  <div class="input-wrapper">
                    <span class="currency-symbol">R$</span>
                    <input type="number" id="input-preco-min" name="preco_min" min="0" max="<?= $precoMaxDB ?>" value="<?= number_format($precoMin, 2, '.', '') ?>" step="0.01">
                  </div>
                </div>
                <div class="input-group">
                  <span class="price-label">Max:</span>
                  <div class="input-wrapper">
                    <span class="currency-symbol">R$</span>
                    <input type="number" id="input-preco-max" name="preco_max" min="0" max="<?= $precoMaxDB ?>" value="<?= number_format($precoMax, 2, '.', '') ?>" step="0.01">
                  </div>
                </div>
              </div>              <div class="filter-buttons">
                <button type="submit" class="btn-filtrar">
                  <i class='bx bx-filter-alt'></i>
                  Filtrar
                </button>
                <a href="marketplace.php" class="btn-limpar">
                  <i class='bx bx-reset'></i>
                  Limpar
                </a>
              </div>
            </div>
          </form>
        </div>
      </header>

         <section class="product-grid">
      <?php if ($mensagem_erro): ?>
        <p><?php echo $mensagem_erro; ?></p>
      <?php elseif (!empty($produtos)): ?>        <?php foreach ($produtos as $row): //usa a lista produtos como rows para jogar eles no site?>
          <div class="product-card">
            <h2><?php echo htmlspecialchars($row['nome_produto']); ?></h2>
            <p class="categoria"><?php echo htmlspecialchars($row['categoria']); ?></p>
            <p class="descricao"><?php echo htmlspecialchars($row['descricao']); ?></p>
            
            <!-- Avaliações -->
            <div class="product-rating">
              <?php if ($row['total_avaliacoes'] > 0): ?>
                <div class="stars">
                  <?php 
                    $media = round($row['media_avaliacao'], 1);
                    for ($i = 1; $i <= 5; $i++): 
                  ?>
                    <i class='bx bx<?= $i <= $media ? 's' : '' ?>-star'></i>
                  <?php endfor; ?>
                </div>
                <span class="rating-text"><?= number_format($media, 1, ',', '.') ?> (<?= $row['total_avaliacoes'] ?> avaliação<?= $row['total_avaliacoes'] > 1 ? 'ões' : '' ?>)</span>
              <?php else: ?>
                <div class="no-rating">
                  <span class="rating-text">Sem avaliações</span>
                </div>
              <?php endif; ?>
            </div>
            
            <p class="preco">R$ <?php echo number_format($row['preco'], 2, ',', '.'); ?></p>
            <p class="vendedor">Vendedor: <?php echo htmlspecialchars($row['nome_vendedor']); ?></p>
            <form action="carrinho.php" method="POST" class="form-selecionar">
              <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
              <button type="submit" title="Adicionar ao carrinho" class="btn-carrinho">
                <i class='bx bx-cart-add'></i>
              </button>
            </form>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Nenhum produto ativo encontrado.</p>
      <?php endif; ?>
    </section>
  </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function formatCurrency(value) {
    return 'R$ ' + value.toFixed(2).replace('.', ',');
  }
    function handleInputChange(inputId, type) {
    const input = document.getElementById(inputId);
    const min = parseFloat(input.min);
    const max = parseFloat(input.max);
    let value = parseFloat(input.value);
    
    if (isNaN(value)) {
      value = (type === 'min') ? min : max;
    }
    
    if (value < min) value = min;
    if (value > max) value = max;
    
    input.value = value.toFixed(2);
  }  function limparFiltrosPreco() {
    console.log('Função limparFiltrosPreco chamada');
    
    const inputPrecoMin = document.getElementById('input-preco-min');
    const inputPrecoMax = document.getElementById('input-preco-max');
    
    // Restaura os valores para o mínimo (zero) e máximo
    inputPrecoMin.value = '0.00';
    inputPrecoMax.value = parseFloat(inputPrecoMax.max).toFixed(2);
    
    // Limpa o campo de busca também
    const inputBusca = document.querySelector('input[name="nome"]');
    if (inputBusca) {
      inputBusca.value = '';
    }
    
    console.log('Valores redefinidos, enviando formulário...');
    
    // Submete automaticamente o formulário para atualizar os resultados
    inputPrecoMin.form.submit();
  }
    // Inicializa os displays e adiciona eventos
  document.addEventListener('DOMContentLoaded', function() {
    // Adiciona eventos para quando o usuário terminar de digitar
    const inputPrecoMin = document.getElementById('input-preco-min');
    const inputPrecoMax = document.getElementById('input-preco-max');
    
    inputPrecoMin.addEventListener('blur', function() {
      handleInputChange('input-preco-min', 'min');
    });
    
    inputPrecoMax.addEventListener('blur', function() {
      handleInputChange('input-preco-max', 'max');
    });
    
    // Adiciona eventos para lidar com as teclas Enter
    inputPrecoMin.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        handleInputChange('input-preco-min', 'min');
        e.preventDefault(); // Previne a submissão do formulário
      }
    });
    
    inputPrecoMax.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        handleInputChange('input-preco-max', 'max');
        e.preventDefault(); // Previne a submissão do formulário
      }
    });
  });
</script>

<!-- Mensagem de sucesso produto adicionado ao carrinho -->
<?php if (isset($_GET['adicionado']) && $_GET['adicionado'] == 1): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const tema = localStorage.getItem("tema") === "claro";
      
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Produto adicionado ao carrinho!',
        showConfirmButton: false,
        timer: 2000,
        background: tema ? "#E6E4E4" : "#262626",
        color: tema ? "#121212" : "#ffffff"
      });
      
      const url = new URL(window.location.href);
      url.searchParams.delete('adicionado');
      window.history.replaceState({}, document.title, url.toString());
    });
  </script>
<?php endif; ?>

<script src="../assets/css/js/script.js"></script>
</body>
</html>