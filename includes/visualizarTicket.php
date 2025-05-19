<?php
include '../includes/db.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: ../pages/readSuporte.php");
    exit();
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM suporte WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$ticket = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Ticket</title>
    <link rel="stylesheet" href="../assets/css/visualizarTicket.css">
</head>
<body>
    <h2>Detalhes do Ticket</h2>
    <?php if (!empty($ticket)): ?>
    <div class="ticket-info">
        <p><strong>ID:</strong> <?php echo $ticket['id']; ?></p>
        <p><strong>Usuário:</strong> <?php echo htmlspecialchars($ticket['usuario']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($ticket['email']); ?></p>
        <p><strong>Assunto:</strong> <?php echo htmlspecialchars($ticket['assunto']); ?></p>
        <p><strong>Descrição:</strong> <?php echo nl2br(htmlspecialchars($ticket['descricao'])); ?></p>
    </div>
    <?php else: ?>
    <p>Ticket não encontrado.</p>
    <?php endif; ?>
    <a href="../pages/readSuporte.php">Voltar</a>
</body>
</html>