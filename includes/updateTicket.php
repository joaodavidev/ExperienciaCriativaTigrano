<?php
// updateTicket.php
require_once 'db.php';

if (!isset($_GET['id'])) {
    die('ID do ticket não informado.');
}
$id = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assunto = $_POST['assunto'];
    $descricao = $_POST['descricao'];
    $sql = "UPDATE suporte SET assunto = ?, descricao = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssi', $assunto, $descricao, $id);
    if ($stmt->execute()) {
        header('Location: ../pages/suporteUsuario.php?editado=1');
        exit();
    } else {
        echo 'Erro ao atualizar ticket.';
    }
    $stmt->close();
}

$sql = "SELECT * FROM suporte WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$ticket = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();
if (!$ticket) die('Ticket não encontrado.');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Ticket</title>
</head>
<body>
    <h2>Editar Ticket</h2>
    <form method="POST">
        <label>Assunto:<br><input type="text" name="assunto" value="<?= htmlspecialchars($ticket['assunto']) ?>" required></label><br>
        <label>Descrição:<br><textarea name="descricao" required><?= htmlspecialchars($ticket['descricao']) ?></textarea></label><br>
        <button type="submit">Salvar Alterações</button>
        <a href="../pages/suporteUsuario.php">Cancelar</a>
    </form>
</body>
</html>
