<?php
require_once '../includes/db.php';

if (!isset($_SESSION['usuario']['email'])) {
    echo "<p>Você precisa estar logado para ver os tickets de suporte.</p>";
    exit();
}

$email = $_SESSION['usuario']['email'];

$sql = "SELECT * FROM suporte WHERE email_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dadosJSON = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');

        echo "<div class='suporte-item' onclick='abrirModalSuporte({$dadosJSON})'>";
        echo "<p><strong>Assunto:</strong> " . htmlspecialchars($row['assunto']) . "</p>";
        echo "<p><strong>Mensagem:</strong> " . htmlspecialchars($row['descricao']) . "</p>";
        echo "<p><strong>Data:</strong> " . $row['data_envio'] . "</p>";
        echo "<p><strong>Resposta:</strong> " . htmlspecialchars($row['resposta']) . "</p>";
        echo "<p><strong>Status:</strong> " . htmlspecialchars($row['status']) . "</p>";
        echo "</div>";
    }
}
else {
    echo "<p>Você ainda não enviou nenhuma solicitação de suporte.</p>";
}
?>
