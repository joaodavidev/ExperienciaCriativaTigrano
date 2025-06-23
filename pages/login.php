<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <button id="btnModo" class="btn-toggle-tema">
        <i class='bx bx-moon'></i>
    </button>

    <div class="login-container">
        <h2>Login</h2>
        <form action="../includes/autenticar.php" method="post">
            <label for="usuario">Usuário:</label>
            <input type="text" id="usuario" name="usuario" required>

            <label for="senha">Senha:</label>
            <div class="senha-container">
                <input type="password" id="senha" name="senha" required>
                <button type="button" id="toggleSenha"><i class='bx bx-show'></i></button>
            </div>

            <button type="submit">Entrar</button>

            <p>Não possui uma conta? <a href="../pages/createCadastro.php">Cadastre-se</a></p>
        </form>
        <div class="linha-ou">
             <span>OU</span>
        </div>
        <p class="admin-link">É administrador? <a href="../pages/loginadm.php">Entrar como Adm</a></p>    </div>

    <!-- Mensagens de feedback -->
    <?php if (isset($_GET['sucesso'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const temaClaro = localStorage.getItem("tema") === "claro";
                Swal.fire({
                    icon: 'success',
                    title: 'Conta deletada com sucesso!',
                    text: 'Sua conta foi removida permanentemente.',
                    confirmButtonText: 'OK',
                    background: temaClaro ? "#E6E4E4" : "#262626",
                    color: temaClaro ? "#121212" : "#ffffff",
                    confirmButtonColor: "#1D4ED8"
                });
            });
        </script>
    <?php endif; ?>    <?php if (isset($_GET['conta_desativada'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const temaClaro = localStorage.getItem("tema") === "claro";
                Swal.fire({
                    icon: 'info',
                    title: 'Conta desativada',
                    html: 'Sua conta foi <strong>desativada</strong> ao invés de deletada.<br><br>' +
                          'Isso aconteceu porque você já vendeu produtos e os compradores precisam continuar tendo acesso aos produtos adquiridos.<br><br>' +
                          'Para reativar sua conta, entre em contato com o suporte.',
                    confirmButtonText: 'Entendi',
                    background: temaClaro ? "#E6E4E4" : "#262626",
                    color: temaClaro ? "#121212" : "#ffffff",
                    confirmButtonColor: "#1D4ED8"
                });
            });
        </script>
    <?php endif; ?>

    <?php if (isset($_GET['conta_desativada_login'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const temaClaro = localStorage.getItem("tema") === "claro";
                Swal.fire({
                    icon: 'warning',
                    title: 'Conta desativada',
                    html: 'Sua conta está <strong>desativada</strong>.<br><br>' +
                          'Isso aconteceu porque você solicitou a exclusão da conta, mas como já vendeu produtos, ela foi apenas desativada para preservar o acesso dos compradores aos produtos adquiridos.<br><br>' +
                          'Para reativar sua conta, entre em contato com o suporte.',
                    confirmButtonText: 'Entendi',
                    background: temaClaro ? "#E6E4E4" : "#262626",
                    color: temaClaro ? "#121212" : "#ffffff",
                    confirmButtonColor: "#f59e0b"
                });
            });
        </script>
    <?php endif; ?>

    <!-- Tratamento de erros de login -->
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
                        mensagem = 'Este email não está cadastrado. Verifique o email ou faça seu cadastro.';
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