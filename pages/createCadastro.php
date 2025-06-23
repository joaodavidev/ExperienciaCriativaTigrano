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
    echo '
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
      <meta charset="UTF-8">
      <title>Cadastro Concluído</title>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
      <script>
        const temaClaro = localStorage.getItem("tema") === "claro";
        Swal.fire({
          icon: "success",
          title: "Cadastro realizado com sucesso!",
          text: "Você será redirecionado para o login.",
          confirmButtonText: "OK",
          background: temaClaro ? "#E6E4E4" : "#262626",
          color: temaClaro ? "#121212" : "#ffffff",
          confirmButtonColor: "#1D4ED8"
        }).then(() => {
          window.location.href = "../pages/login.php";
        });
      </script>
    </body>
    </html>';
    exit();
} else {
    echo "Erro ao cadastrar: " . $stmt->error;
}

}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastro</title>
  <link rel="stylesheet" href="../assets/css/login.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
  <button id="btnModo" class="btn-toggle-tema">
    <i class='bx bx-moon'></i>
  </button>

  <div class="login-container">
    <h2>Cadastro</h2>
    <form action="../pages/createCadastro.php" method="POST">
      <label for="nome">Nome completo:</label>
      <input type="text" id="nome" name="nome" required>

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>

      <label for="senha">Senha:</label>
      <input type="password" id="senha" name="senha" required>

      <label for="sexo">Sexo:</label>
      <select id="sexo" name="sexo" required>
        <option value="">Selecione</option>
        <option value="masculino">Masculino</option>
        <option value="feminino">Feminino</option>
        <option value="outro">Outro</option>
      </select>

      <label for="idade">Idade:</label>
      <input type="number" id="idade" name="idade" required>

      <label for="cpf">CPF:</label>
      <input type="text" id="cpf" name="cpf" pattern="\d{11}" placeholder="Somente números" required>

      <button type="submit">Cadastrar</button>

      <p>Já possui uma conta? <a href="../pages/login.php">Fazer login</a></p>
    </form>
  </div>
  <script>
    const btnModo = document.getElementById('btnModo');
    if (btnModo) {
      // Aplicar tema salvo no carregamento
      const temaSalvo = localStorage.getItem('tema');
      if (temaSalvo === 'claro') {
        document.body.classList.add('light-mode');
        btnModo.querySelector('i').classList.remove('bx-moon');
        btnModo.querySelector('i').classList.add('bx-sun');
      }

      btnModo.addEventListener('click', () => {
        document.body.classList.toggle('light-mode');
        const icon = btnModo.querySelector('i');
        
        if (document.body.classList.contains('light-mode')) {
          icon.classList.remove('bx-moon');
          icon.classList.add('bx-sun');
          localStorage.setItem('tema', 'claro');
        } else {
          icon.classList.remove('bx-sun');
          icon.classList.add('bx-moon');
          localStorage.setItem('tema', 'escuro');
        }
      });
    }
  </script>
</body>
</html>
