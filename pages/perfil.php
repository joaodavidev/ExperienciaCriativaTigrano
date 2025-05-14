<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}

$email = $_SESSION['email'];

// Atualizar dados
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nome = trim($_POST['nome']);
  $sexo = $_POST['sexo'];
  $idade = intval($_POST['idade']);

  $sql = "UPDATE usuarios SET nome = ?, sexo = ?, idade = ? WHERE email = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssis", $nome, $sexo, $idade, $email);

  if ($stmt->execute()) {
    $_SESSION['usuario']['nome'] = $nome;
    $mensagem = "Perfil atualizado com sucesso!";
  } else {
    $mensagem = "Erro ao atualizar perfil.";
  }
}
//dados atuais
$sql = "SELECT nome, sexo, idade FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$dados = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perfil</title>
  <link rel="stylesheet" href="../assets/css/perfil.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
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
          <a href="perfil.php">
            <i class='bx bx-user' ></i>
            <span class="nome-link" style="--i:7;">Perfil</span>
          </a>
        </li>
      </ul>
</nav>

<main class="main-content">
  <section class="marketplace-header">
    <div class="marketplace-title">
      <h1>Meu Perfil</h1>
    </div>
  </section>

  <section class="perfil-container">
    <?php if (isset($mensagem)) echo "<p class='mensagem'>$mensagem</p>"; ?>

    <form action="perfil.php" method="POST" class="perfil-form">
      <label for="nome">Nome:</label>
      <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($dados['nome']) ?>" required>

      <label for="sexo">Sexo:</label>
      <select name="sexo" id="sexo" required>
        <option value="masculino" <?= $dados['sexo'] == 'masculino' ? 'selected' : '' ?>>Masculino</option>
        <option value="feminino" <?= $dados['sexo'] == 'feminino' ? 'selected' : '' ?>>Feminino</option>
        <option value="outro" <?= $dados['sexo'] == 'outro' ? 'selected' : '' ?>>Outro</option>
      </select>

      <label for="idade">Idade:</label>
      <input type="number" name="idade" id="idade" value="<?= $dados['idade'] ?>" required>

      <button type="submit">Salvar</button>
    </form>

    <form action="../includes/logout.php" method="post" class="logout-form">
      <button type="submit">Sair da conta</button>
    </form>
  </section>
</main>

<script src="../assets/css/js/script.js"></script>
</body>
</html>