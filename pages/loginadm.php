<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <button id="btnModo" class="btn-toggle-tema">
     <i class='bx bx-moon'></i>
    </button>

    <div class="login-container">
        <h2>Login Admin</h2>
        <form action="../includes/autenticaradm.php" method="post">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>

            <label for="senha">Senha:</label>
            <div class="senha-container">
                <input type="password" id="senha" name="senha" required>
                <button type="button" id="toggleSenha"><i class='bx bx-show'></i></button>
            </div>

            <button type="submit">Entrar</button>

            <p>Não possui uma conta? <a href="../includes/createAdmin.php">Cadastre-se</a></p>
        </form>
        <div class="linha-ou">
             <span>OU</span>
        </div>
        <p class="admin-link">É usuário comum? <a href="../pages/login.php">Entrar como usuário</a></p>
    </div>

    <!-- Tratamento de erros de login admin -->
    <?php if (isset($_GET['erro'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const erro = '<?php echo $_GET["erro"]; ?>';
                const temaClaro = localStorage.getItem("tema") === "claro";
                
                let titulo = 'Erro no login';
                let mensagem = 'Ocorreu um erro inesperado. Tente novamente.';
                
                switch(erro) {
                    case 'senha_incorreta':
                        titulo = 'Senha incorreta';
                        mensagem = 'A senha digitada está incorreta. Verifique e tente novamente.';
                        break;
                    case 'usuario_nao_encontrado':
                        titulo = 'Usuário não encontrado';
                        mensagem = 'Este email não está cadastrado como administrador.';
                        break;
                    case 'dados_incompletos':
                        titulo = 'Dados incompletos';
                        mensagem = 'Preencha todos os campos para fazer login.';
                        break;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: titulo,
                    text: mensagem,
                    confirmButtonText: 'OK',
                    background: temaClaro ? "#E6E4E4" : "#262626",
                    color: temaClaro ? "#121212" : "#ffffff",
                    confirmButtonColor: "#DC2626"
                });
            });
        </script>
    <?php endif; ?>

    <script src="../assets/css/js/login.js"></script>
</body>
</html>