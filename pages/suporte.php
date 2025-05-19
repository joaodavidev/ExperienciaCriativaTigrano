<?php
require_once '../includes/db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $sexo = $_POST['sexo'];
    $idade = intval($_POST['idade']);
    $cpf = $_POST['cpf'];

    $sql = "INSERT INTO usuarios (email, nome, senha, sexo, idade, cpf)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssis", $email, $nome, $senha, $sexo, $idade, $cpf);

    if ($stmt->execute()) {
        header("Location: readCadastro.php");
        exit();
    } else {
        echo "Erro ao cadastrar: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
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
          <a href="#">
            <i class='bx bx-log-out'></i>
            <span class="nome-link" style="--i:7;">LogOut</span>
          </a>
        </li>
      </ul>
    </nav>
</body>
</html>