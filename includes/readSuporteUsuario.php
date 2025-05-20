<?php
require_once '../includes/db.php';

if (!isset($_SESSION)) {
    session_start();
}
$email = $_SESSION['usuario']['email'];

$sql = "SELECT * FROM suporte WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<h3>Suas solicitações de suporte:</h3>";
    while ($row = $result->fetch_assoc()) {
        echo "<div class='suporte-item'>";
        echo "<strong>Assunto:</strong> " . htmlspecialchars($row['assunto']) . "<br>";
        echo "<strong>Mensagem:</strong> " . htmlspecialchars($row['descricao']) . "<br>";
        echo "<strong>Data:</strong> " . $row['data_envio'] . "<br>";
        echo "<hr>";
        echo "</div>";
    }
} else {
    echo "<p>Você ainda não enviou nenhuma solicitação de suporte.</p>";
}
?>
