<?php
session_start();

require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); 

    $sql = "INSERT INTO usuarios (usuario, senha) VALUES ('$usuario', '$senha')";

    if ($conn->query($sql) === TRUE) {
        header("Location: readCadastro.php"); 
        exit();
    } else {
        echo "Erro: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Cadastrar Novo Usuário</title>
    <link rel="stylesheet" href="createCadastro.css" />
</head>
<body>

    <div class="container">
        <h1>Cadastrar Novo Usuário</h1>

        <form action="createCadastroProcess.php" method="POST">
            <label for="usuario">Usuário:</label>
            <input type="text" name="usuario" id="usuario" placeholder="Digite o nome de usuário" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" placeholder="Digite o email" required>

            <label for="senha">Senha:</label>
            <input type="password" name="senha" id="senha" placeholder="Digite a senha" required>

            <button type="submit">Cadastrar</button>
        </form>

        <a href="readCadastro.php" class="btn">Voltar à Lista de Usuários</a>
    </div>

</body>
</html>
