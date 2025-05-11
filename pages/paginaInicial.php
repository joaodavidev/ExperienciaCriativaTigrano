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
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tigrano Marketplace</title>
  <link rel="stylesheet" href="../assets/css/paginainicial.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
  <nav class="sidebar active">
      <div class="logo-menu">
        <h2 class="logo">Tigrano</h2>
        <i class='bx bx-menu toggle-btn'></i>
      </div>
      <ul class="lista">
        <li class="lista-item active">
            <a href="../pages/paginaInicial.php">
                <i class='bx bxs-shopping-bag-alt' ></i>
                <span class="nome-link" style="--i:1;">Marketplace</span>
            </a>
        </li>
        <li class="lista-item">
            <a href="#">
                <i class='bx bxs-dashboard' ></i>
                <span class="nome-link" style="--i:2;">Dashboard</span>
            </a>
        </li>
        <li class="lista-item">
            <a href="../pages/produtos.php">
                <i class='bx bxs-purchase-tag' ></i>
                <span class="nome-link" style="--i:3;">Produtos</span>
            </a>
        </li>
        <li class="lista-item">
            <a href="#">
                <i class='bx bx-shopping-bag' ></i>
                <span class="nome-link" style="--i:4;">Compras</span>
            </a>
              </li>
        <li class="lista-item grupo-inferior">
            <a href="#" class="btn-toggle-tema">
              <i class='bx bx-moon'></i>
              <span class="nome-link" style="--i:5;">Claro/Escuro</span>
            </a>
          </li>
        <li class="lista-item">
          <a href="#">
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
        <section class="marketplace">
          <h1>Marketplace</h1>
          <p>Encontre aqui os melhores infoprodutos.</p>
          <div class="search-bar">
            <input type="text" name="nome" placeholder="Buscar...">
            <i class='bx bx-search'></i>
          </div>
        </section>
      </main>

      
    <script src="../assets/css/js/script.js"></script>
</body>
</html>