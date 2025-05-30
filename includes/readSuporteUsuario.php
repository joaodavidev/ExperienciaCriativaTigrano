<?php
require_once '../includes/db.php';
$email = $_SESSION['usuario']['email'];
$filtro = $_GET['filtro'] ?? 'todos';

switch ($filtro) {
    case 'abertos':
        $sql = "SELECT * FROM suporte WHERE email_usuario = ? AND (resposta IS NULL OR TRIM(resposta) = '') ORDER BY data_envio DESC";
        break;
    case 'respondidos':
        $sql = "SELECT * FROM suporte WHERE email_usuario = ? AND resposta IS NOT NULL AND TRIM(resposta) <> '' ORDER BY data_envio DESC";
        break;
    default:
        $sql = "SELECT * FROM suporte WHERE email_usuario = ? ORDER BY data_envio DESC";
        break;
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0):
    while ($ticket = $result->fetch_assoc()):
        $dadosJS = htmlspecialchars(json_encode([
            'id' => $ticket['id'],
            'assunto' => $ticket['assunto'],
            'descricao' => $ticket['descricao'],
            'data_envio' => $ticket['data_envio'],
            'resposta' => $ticket['resposta'],
            'status' => trim($ticket['resposta']) ? 'Concluído' : 'Pendente'
        ]), ENT_QUOTES, 'UTF-8');
?>
    <div class="suporte-item" onclick="abrirModalSuporte(<?= $dadosJS ?>)">
        <p><strong>Assunto:</strong> <?= htmlspecialchars($ticket['assunto']) ?></p>
        <p><strong>Mensagem:</strong> <?= htmlspecialchars(mb_strimwidth($ticket['descricao'], 0, 60, '...')) ?></p>
    </div>
<?php
    endwhile;
else:
    echo "<p>Nenhum ticket encontrado.</p>";
endif;

$stmt->close();
?>
