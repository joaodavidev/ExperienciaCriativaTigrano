<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
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

    <script src="../assets/css/js/login.js"></script>
</body>
</html>