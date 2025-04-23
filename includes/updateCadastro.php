<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "ecommerce");

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$id = $_GET['id'];

$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Usuário não encontrado.");
}

// processando a att
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'] ? password_hash($_POST['senha'], PASSWORD_DEFAULT) : $user['senha'];

    $sql = "UPDATE usuarios SET usuario = ?, senha = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $usuario, $senha, $id);
    $stmt->execute();

    header("Location: readCadastro.php"); // joga no read 
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Cadastro de Usuário</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

    <div class="login-container">
        <h1>Editar Cadastro de Usuário</h1>
        <form action="updateCadastro.php?id=<?php echo $user['id']; ?>" method="post">
            <label for="usuario">Usuário:</label>
            <input type="text" id="usuario" name="usuario" value="<?php echo $user['usuario']; ?>" required>

            <label for="senha">Nova Senha:</label>
            <input type="password" id="senha" name="senha">

            <label for="confirmar_senha">Confirmar Nova Senha:</label>
            <input type="password" id="confirmar_senha" name="confirmar_senha">

            <button type="submit">Atualizar Cadastro</button>
        </form>
    </div>

</body>
</html>