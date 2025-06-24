<?php
require_once '../includes/db.php';
session_start();

function exibirErroSweetAlert($titulo, $mensagem) {
    echo '
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
      <meta charset="UTF-8">
      <title>Erro no Cadastro</title>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
      <script>
        const temaClaro = localStorage.getItem("tema") === "claro";
        Swal.fire({
          icon: "error",
          title: "' . $titulo . '",
          text: "' . $mensagem . '",
          confirmButtonText: "OK",
          background: temaClaro ? "#E6E4E4" : "#262626",
          color: temaClaro ? "#121212" : "#ffffff",
          confirmButtonColor: "#DC2626"
        }).then(() => {
          window.history.back();
        });
      </script>
    </body>
    </html>';
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $sexo = $_POST['sexo'];
    $idade = intval($_POST['idade']);
    $cpf = trim($_POST['cpf']);

    // Validar dados básicos
    if (empty($nome) || empty($email) || empty($cpf)) {
        exibirErroSweetAlert("Dados incompletos", "Preencha todos os campos obrigatórios.");
    }

    // Validar formato do email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        exibirErroSweetAlert("Email inválido", "Digite um endereço de email válido.");
    }    // Validar CPF (apenas números e 11 dígitos)
    if (!preg_match('/^\d{11}$/', $cpf)) {
        exibirErroSweetAlert("CPF inválido", "O CPF deve conter exatamente 11 números.");
    }

    // Validar senha forte (mínimo 8 caracteres e 1 caractere especial)
    if (strlen($_POST['senha']) < 8 || !preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $_POST['senha'])) {
        exibirErroSweetAlert("Senha muito fraca", "A senha deve ter pelo menos 8 caracteres e pelo menos 1 caractere especial (!@#$%^&*).");
    }

    // Verificar se email já existe
    $stmtEmailCheck = $conn->prepare("SELECT email FROM usuarios WHERE email = ?");
    $stmtEmailCheck->bind_param("s", $email);
    $stmtEmailCheck->execute();
    $resultEmail = $stmtEmailCheck->get_result();
    
    if ($resultEmail->num_rows > 0) {
        $stmtEmailCheck->close();
        exibirErroSweetAlert("Email já cadastrado", "Este email já está em uso. Tente fazer login ou use outro email.");
    }
    $stmtEmailCheck->close();

    // Verificar se CPF já existe
    $stmtCpfCheck = $conn->prepare("SELECT cpf FROM usuarios WHERE cpf = ?");
    $stmtCpfCheck->bind_param("s", $cpf);
    $stmtCpfCheck->execute();
    $resultCpf = $stmtCpfCheck->get_result();
    
    if ($resultCpf->num_rows > 0) {
        $stmtCpfCheck->close();
        exibirErroSweetAlert("CPF já cadastrado", "Este CPF já está registrado em nossa base de dados.");
    }
    $stmtCpfCheck->close();

    // Tentar inserir usuário
    $sql = "INSERT INTO usuarios (email, nome, senha, sexo, idade, cpf)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        exibirErroSweetAlert("Erro interno", "Erro ao preparar a consulta. Tente novamente mais tarde.");
    }
    
    $stmt->bind_param("ssssis", $email, $nome, $senha, $sexo, $idade, $cpf);

    if ($stmt->execute()) {
        $stmt->close();
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
        $stmt->close();
        // Verificar se é erro de duplicate key
        if ($conn->errno == 1062) {
            if (strpos($conn->error, 'email') !== false) {
                exibirErroSweetAlert("Email já cadastrado", "Este email já está em uso. Tente fazer login ou use outro email.");
            } elseif (strpos($conn->error, 'cpf') !== false) {
                exibirErroSweetAlert("CPF já cadastrado", "Este CPF já está registrado em nossa base de dados.");
            } else {
                exibirErroSweetAlert("Dados duplicados", "Já existe um usuário com estes dados. Verifique email e CPF.");
            }
        } else {
            exibirErroSweetAlert("Erro no cadastro", "Ocorreu um erro inesperado. Tente novamente mais tarde.");
        }
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
    <form action="../pages/createCadastro.php" method="POST">      <label for="nome">Nome completo:</label>
      <input type="text" id="nome" name="nome" placeholder="Digite seu nome completo" required>

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" placeholder="Digite seu email" required><label for="senha">Senha:</label>
      <input type="password" id="senha" name="senha" placeholder="Mínimo 8 caracteres, 1 especial" required>
      <div class="senha-requisitos" id="senhaRequisitos">
        <p>• Mínimo 8 caracteres</p>
        <p>• Pelo menos 1 caractere especial (!@#$%^&*)</p>
      </div>

      <label for="sexo">Sexo:</label>
      <select id="sexo" name="sexo" required>
        <option value="">Selecione</option>
        <option value="masculino">Masculino</option>
        <option value="feminino">Feminino</option>
        <option value="outro">Outro</option>
      </select>      <label for="idade">Idade:</label>
      <input type="number" id="idade" name="idade" placeholder="Digite sua idade" min="1" max="120" required>

      <label for="cpf">CPF:</label>
      <input type="text" id="cpf" name="cpf" pattern="\d{11}" placeholder="Digite apenas os 11 números do CPF" required>

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

    // Validação de senha forte
    function validarSenhaForte(senha) {
      const minLength = 8;
      const temCaracterEspecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(senha);
      
      return senha.length >= minLength && temCaracterEspecial;
    }

    // Feedback visual da força da senha
    const senhaInput = document.getElementById('senha');
    const requisitos = document.getElementById('senhaRequisitos');
    
    if (senhaInput && requisitos) {
      senhaInput.addEventListener('input', function() {
        const senha = this.value;
        
        if (validarSenhaForte(senha)) {
          requisitos.classList.add('forte');
          requisitos.innerHTML = '<p>✓ Senha forte!</p>';
        } else {
          requisitos.classList.remove('forte');
          requisitos.innerHTML = '<p>• Mínimo 8 caracteres</p><p>• Pelo menos 1 caractere especial (!@#$%^&*)</p>';
        }
      });
    }

    // Validação no envio do formulário
    const form = document.querySelector('form');
    if (form) {
      form.addEventListener('submit', function(e) {
        const senha = document.getElementById('senha').value;
        
        if (!validarSenhaForte(senha)) {
          e.preventDefault();
          
          const tema = localStorage.getItem("tema") === "claro";
          
          Swal.fire({
            icon: 'error',
            title: 'Senha muito fraca!',
            text: 'A senha deve ter pelo menos 8 caracteres e 1 caractere especial (!@#$%^&*)',
            confirmButtonText: 'OK',
            background: tema ? '#E6E4E4' : '#262626',
            color: tema ? '#121212' : '#ffffff',
            confirmButtonColor: '#DC2626'
          });
          return false;
        }
      });
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
