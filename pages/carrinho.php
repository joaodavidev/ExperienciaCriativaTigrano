<?php
session_start();
include '../includes/db.php';

// 🔐 LOGIN FAKE PARA TESTES (cliente)
if (!isset($_SESSION['cliente_email'])) {
    $_SESSION['cliente_email'] = 'cliente@email.com';
}
$email = $_SESSION['cliente_email'];

// ADICIONAR PRODUTO AO CARRINHO
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id']) && isset($_SESSION['usuario_email'])) {
    $produtoId = intval($_POST['id']);
    $email = $_SESSION['usuario_email'];

    // Verifica se o produto já está no carrinho
    $check = $conn->prepare("SELECT * FROM carrinho WHERE usuario_email = ? AND produto_id = ?");
    $check->bind_param("si", $email, $produtoId);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows === 0) {
        $sql = "INSERT INTO carrinho (usuario_email, produto_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $email, $produtoId);
        $stmt->execute();
    }
}

// BUSCA POR NOME
$produtoEncontradoID = null;
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nome'])) {
    $nome = trim($_POST['nome']);
    $sql_search = "SELECT * FROM produtos WHERE nome = ?";
    $stmt_search = $conn->prepare($sql_search);
    $stmt_search->bind_param("s", $nome);
    $stmt_search->execute();
    $result = $stmt_search->get_result();

    if ($result->num_rows > 0) {
        $produto = $result->fetch_assoc();
        $produtoEncontradoID = $produto['id'];
    } else {
        $mensagem_erro = "Produto não encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Carrinho</title>
  <link rel="stylesheet" href="../assets/css/carrinho.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
  <header>
       <nav class="sidebar active">
      <div class="logo-menu">
        <h2 class="logo">Tigrano</h2>
        <i class='bx bx-menu toggle-btn'></i>
      </div>
            <ul class="lista">
        <!-- Itens superiores -->
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
  </header>

  <div class="main-content">
    <div class="search-container">
      <form action="carrinho.php" method="POST">
          <div class="search-bar">
            <input type="text" name="nome" placeholder="Buscar produtos">
            <i class='bx bx-search'></i>
    </div>
        </form>
        <?php if (isset($mensagem_erro)) echo "<p class='erro'>$mensagem_erro</p>"; ?>
      </div>
  

    <div id="produtos">
      <h2>Produtos no Carrinho</h2>
      <?php
        $total = 0;

        if (!empty($_SESSION['carrinho'])) {
          foreach ($_SESSION['carrinho'] as $index => $item) {
            $total += $item['preco'];
            echo "<div class='produto' id='produto-{$item['id']}'>";
            echo "<strong>" . htmlspecialchars($item['nome']) . "</strong><br>";
            echo "R$ " . number_format($item['preco'], 2, ',', '.') . "<br>";
            echo "<p>" . htmlspecialchars($item['descricao']) . "</p>";
            echo "<form action='removerCarrinho.php' method='post' class='form-excluir'>";
            echo "<input type='hidden' name='index' value='$index'>";
            echo "<button type='submit' class='btn-excluir'>Excluir</button>";
            echo "</form>";
            echo "</div>";
          }

          // Total
          echo "<div class='total-carrinho'>";
          echo "<strong>Total do Carrinho: R$ " . number_format($total, 2, ',', '.') . "</strong>";
          echo "</div>";
        } else {
          echo "<p>Nenhum produto no carrinho.</p>";
        }
      ?>
    </div>



    <div class="comprar-container">
      <form action="pagamento.php" method="POST">
        <button type="submit" class="btn-comprar">Finalizar Compra</button>
      </form>
    </div>
  </div>
  <?php if ($produtoEncontradoID): ?>
  <?php endif; ?>
  <script src="../assets/css/js/script.js"></script>
  <script src="../assets/css/js/carrinho.js"></script>
</body>
</html>
