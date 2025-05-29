<?php
include '../includes/db.php';

$produtoEncontradoID = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nome'])) {
    $nome = trim($_POST['nome']);

    $sql_search = "SELECT * FROM produtos WHERE nome = ?";
    $stmt_search = $conn->prepare($sql_search);
    $stmt_search->bind_param("s", $nome);
    $stmt_search->execute();
    $result = $stmt_search->get_result();

    if ($result->num_rows > 0) {
        $produto = $result->fetch_assoc();
        $produtoEncontradoID = $produto['id'];
    }else {
        $mensagem_erro = "Produto não encontrado.";
    }
}



  $sql = "SELECT p.id, p.nome AS nome_produto, p.categoria, p.preco, p.descricao, u.nome AS nome_vendedor 
        FROM produtos p
        JOIN usuarios u ON p.vendedor_email = u.email
        WHERE p.status = 'Ativo'";

  $result = $conn->query($sql);
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
      <section class="marketplace-header">
        <div class="marketplace-title">
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
          <form method="POST">
            <input type="text" name="nome" placeholder="Buscar produto...">
            <button type="submit"><i class='bx bx-search'></i></button>
           </form>
        </div>
</div>
  
    </header>

    <section class="product-grid">
      <?php if ($result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <div class="product-card">
          <h2><?php echo htmlspecialchars($row['nome_produto']); ?></h2>
          <p class="categoria"><?php echo htmlspecialchars($row['categoria']); ?></p>
          <p class="descricao"><?php echo htmlspecialchars($row['descricao']); ?></p>
          <p class="preco">R$ <?php echo number_format($row['preco'], 2, ',', '.'); ?></p>
          <p class="vendedor">Vendedor: <?php echo htmlspecialchars($row['nome_vendedor']); ?></p>
          <form action="carrinho.php" method="POST" class="form-selecionar">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            <button type="submit" title="Adicionar ao carrinho" class="btn-carrinho">
              <i class='bx bx-cart-add'></i>
            </button>
          </form>
        </div>
      <?php endwhile; ?>
      <?php else: ?>
        <p>Nenhum produto ativo encontrado.</p>
    <?php endif; ?>
    </section>
  </div>
</main>



      <script src="../assets/css/js/script.js"></script>
</body>
</html>