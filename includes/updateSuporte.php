<?php
include 'db.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: ../pages/readSuporte.php");
    exit();
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM suporte WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $ticket = $result->fetch_assoc();
} else {
    echo 'Ticket não encontrado.';
    exit();
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $email = $_POST['email'];
    $assunto = $_POST['assunto'];
    $descricao = $_POST['descricao'];
    
    $sql = "UPDATE suporte SET usuario = ?, email = ?, assunto = ?, descricao = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssi', $usuario, $email, $assunto, $descricao, $id);

    if ($stmt->execute()) {
        header('Location: ../pages/readSuporte.php');
        exit();
    } else {
        echo 'Erro ao atualizar: ' . $conn->error;
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Ticket de Suporte</title>
    <link rel="stylesheet" href="../assets/css/updateSuporte.css">
</head>
<body>
    <h2>Editar Ticket de Suporte</h2>
    <form method="post">
        <label for="usuario">Usuário:</label>
        <input type="text" id="usuario" name="usuario" value="<?php echo htmlspecialchars($ticket['usuario']); ?>" required>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($ticket['email']); ?>" required>
        
        <label for="assunto">Assunto:</label>
        <input type="text" id="assunto" name="assunto" value="<?php echo htmlspecialchars($ticket['assunto']); ?>" required>
        
        <label for="descricao">Descrição:</label>
        <textarea id="descricao" name="descricao" required><?php echo htmlspecialchars($ticket['descricao']); ?></textarea>
        
        <button type="submit">Atualizar Ticket</button>
    </form>
</body>
</html>
