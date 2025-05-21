<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['usuario'])) {
  header("Location: login.php");
  exit();
}

$email = $_SESSION['usuario']['email'];

// Atualização de dados do perfil
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (isset($_POST['nome'])) {
    $nome = trim($_POST['nome']);
    $sexo = $_POST['sexo'];
    $idade = intval($_POST['idade']);

    $sql = "UPDATE usuarios SET nome = ?, sexo = ?, idade = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $nome, $sexo, $idade, $email);

    if ($stmt->execute()) {
      $_SESSION['usuario']['nome'] = $nome;
      $_SESSION['alerta'] = ['tipo' => 'success', 'mensagem' => 'Perfil atualizado com sucesso!'];
    } else {
      $_SESSION['alerta'] = ['tipo' => 'error', 'mensagem' => 'Erro ao atualizar perfil.'];
    }
    $stmt->close();
  }

  // Redefinir senha
  if (isset($_POST['senha_atual'], $_POST['nova_senha'], $_POST['confirmar_senha'])) {
    $senhaAtual = $_POST['senha_atual'];
    $novaSenha = $_POST['nova_senha'];
    $confirmarSenha = $_POST['confirmar_senha'];

    if ($novaSenha !== $confirmarSenha) {
      $_SESSION['alerta'] = ['tipo' => 'error', 'mensagem' => 'A nova senha e a confirmação não coincidem.'];
    } else {
      $sql = "SELECT senha FROM usuarios WHERE email = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("s", $email);
      $stmt->execute();
      $result = $stmt->get_result();
      $dadosSenha = $result->fetch_assoc();

      if (password_verify($senhaAtual, $dadosSenha['senha'])) {
        $novaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE email = ?");
        $stmt->bind_param("ss", $novaHash, $email);
        if ($stmt->execute()) {
          $_SESSION['alerta'] = ['tipo' => 'success', 'mensagem' => 'Senha atualizada com sucesso!'];
        } else {
          $_SESSION['alerta'] = ['tipo' => 'error', 'mensagem' => 'Erro ao atualizar a senha.'];
        }
        $stmt->close();
      } else {
        $_SESSION['alerta'] = ['tipo' => 'error', 'mensagem' => 'Senha atual incorreta.'];
      }
    }
  }
}

// Dados atuais
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
  <section class="marketplace-header">
    <div class="marketplace-title">
      <h1>Meu Perfil</h1>
    </div>
  </section>

  <section class="perfil-container">

    <h2>Dados Pessoais</h2>
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

    <h2>Redefinir Senha</h2>
    <form action="perfil.php" method="POST" class="perfil-form">
      <label for="senha_atual">Senha atual:</label>
      <input type="password" name="senha_atual" id="senha_atual" required>

      <label for="nova_senha">Nova senha:</label>
      <input type="password" name="nova_senha" id="nova_senha" required>

      <label for="confirmar_senha">Confirmar nova senha:</label>
      <input type="password" name="confirmar_senha" id="confirmar_senha" required>

      <button type="submit">Alterar senha</button>
    </form>

    <form action="../includes/logout.php" method="post" class="logout-form">
      <button type="submit">Sair da conta</button>
    </form>
  </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (isset($_SESSION['alerta'])): ?>
<script>
  Swal.fire({
    icon: '<?= $_SESSION['alerta']['tipo'] ?>',
    title: '<?= $_SESSION['alerta']['mensagem'] ?>',
    confirmButtonText: 'OK'
  });
</script>
<?php unset($_SESSION['alerta']); ?>
<?php endif; ?>

<script src="../assets/css/js/script.js"></script>
</body>
</html>
