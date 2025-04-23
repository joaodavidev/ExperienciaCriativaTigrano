<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/login.css">
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
                <button type="button" id="toggleSenha">👁️</button>
            </div>

            <button type="submit">Entrar</button>
        </form>
    </div>

    <script src="../includes/login.js"></script>
</body>
</html>