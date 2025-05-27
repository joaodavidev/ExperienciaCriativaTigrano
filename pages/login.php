<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <button id="btnModo" class="btn-toggle-tema" style="position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 24px; color: white; cursor: pointer;">
     <i class='bx bx-moon'></i>
    </button>

    <div class="login-container">
        <h2>Login</h2>
        <form action="../includes/autenticar.php" method="post">
            <label for="usuario">Email:</label>
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
        <p class="admin-link">É administrador? <a href="../pages/loginadm.php">Entrar como Adm</a></p>
    </div>
    <script src="../assets/css/js/login.js"></script>
</body>
</html>