<?php
session_start();
include '../includes/db.php';

// Adicionar produto ao carrinho
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    if (!isset($_SESSION['usuario']['email'])) {
        die("Usuário não autenticado.");
    }

    $usuario_email = $_SESSION['usuario']['email'];
    $produto_id = $_POST['id'];

    // Evita duplicidade
    $check = $conn->prepare("SELECT * FROM carrinho WHERE usuario_email = ? AND produto_id = ?");
    $check->bind_param("si", $usuario_email, $produto_id);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows === 0) {
        $sql = "INSERT INTO carrinho (usuario_email, produto_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $usuario_email, $produto_id);
        $stmt->execute();
        $stmt->close();
    }

    $check->close();
    header("Location: marketplace.php");
    exit();
}

// Remover produto do carrinho
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['remover_id'])) {
    if (!isset($_SESSION['usuario']['email'])) {
        die("Usuário não autenticado.");
    }

    $usuario_email = $_SESSION['usuario']['email'];
    $produto_id = $_POST['remover_id'];

    $delete = $conn->prepare("DELETE FROM carrinho WHERE usuario_email = ? AND produto_id = ? LIMIT 1");
    $delete->bind_param("si", $usuario_email, $produto_id);
    $delete->execute();
    $delete->close();

    header("Location: carrinho.php");
    exit();
}

// Buscar produtos do carrinho
$produtosCarrinho = [];
if (isset($_SESSION['usuario']['email'])) {
    $usuario_email = $_SESSION['usuario']['email'];
    $sql = "SELECT p.id, p.nome, p.preco, p.descricao 
            FROM carrinho c
            JOIN produtos p ON c.produto_id = p.id
            WHERE c.usuario_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $produtosCarrinho = $result->fetch_all(MYSQLI_ASSOC);
    }
    $stmt->close();
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
        <li class="lista-item"><a href="marketplace.php"><i class='bx bxs-shopping-bag-alt'></i><span class="nome-link">Marketplace</span></a></li>
        <li class="lista-item"><a href="dashboard.php"><i class='bx bxs-dashboard'></i><span class="nome-link">Dashboard</span></a></li>
        <li class="lista-item"><a href="produto.php"><i class='bx bxs-purchase-tag'></i><span class="nome-link">Produtos</span></a></li>
        <li class="lista-item"><a href="compras.php"><i class='bx bx-shopping-bag'></i><span class="nome-link">Compras</span></a></li>
        <li class="espacador"></li>
        <li class="lista-item"><a href="#"><i class='bx bx-moon'></i><span class="nome-link">Claro/Escuro</span></a></li>
        <li class="lista-item"><a href="configuracoes.php"><i class='bx bx-cog'></i><span class="nome-link">Configurações</span></a></li>
        <li class="lista-item"><a href="#"><i class='bx bx-log-out'></i><span class="nome-link">LogOut</span></a></li>
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
    </div>

    <div id="produtos">
      <h2>Produtos no Carrinho</h2>
      <?php
        $total = 0;

        if (count($produtosCarrinho) > 0) {
            foreach ($produtosCarrinho as $produto) {
                $total += $produto['preco'];
                echo "<div class='produto' id='produto-{$produto['id']}'>";
                echo "<strong>" . htmlspecialchars($produto['nome']) . "</strong><br>";
                echo "R$ " . number_format($produto['preco'], 2, ',', '.') . "<br>";
                echo "<p>" . htmlspecialchars($produto['descricao']) . "</p>";
                echo "<form action='carrinho.php' method='post' class='form-excluir'>";
                echo "<input type='hidden' name='remover_id' value='{$produto['id']}'>";
                echo "<button type='submit' class='btn-excluir'>Excluir</button>";
                echo "</form>";
                echo "</div>";
            }

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

  <script src="../assets/css/js/script.js"></script>
  <script src="../assets/css/js/carrinho.js"></script>
</body>
</html>
