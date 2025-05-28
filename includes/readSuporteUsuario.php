<?php
require_once '../includes/db.php';
$email = $_SESSION['usuario']['email'];
$filtro = $_GET['filtro'] ?? 'todos';

// Usar consultas preparadas para segurança
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
        // Formatação da data para exibição
        $data_formatada = date('d/m/Y H:i', strtotime($ticket['data_envio']));
        
        // Determina o status do ticket
        $status = trim($ticket['resposta']) ? 'Concluído' : 'Pendente';
        $statusClass = trim($ticket['resposta']) ? 'status-concluido' : 'status-pendente';
        
        // Preparação de dados JSON para o modal
        $dadosJS = htmlspecialchars(json_encode([
            'assunto' => $ticket['assunto'],
            'descricao' => $ticket['descricao'],
            'data_envio' => $data_formatada,
            'resposta' => $ticket['resposta'],
            'status' => $status
        ]), ENT_QUOTES, 'UTF-8');
?>
    <div class="suporte-item" onclick="abrirModalSuporte(<?= $dadosJS ?>)">
        <div class="suporte-header">
            <span class="status-badge <?= $statusClass ?>"><?= $status ?></span>
            <span class="data-ticket"><?= $data_formatada ?></span>
        </div>
        <p><strong>Assunto:</strong> <?= htmlspecialchars($ticket['assunto']) ?></p>
        <p><strong>Mensagem:</strong> <?= htmlspecialchars(mb_strimwidth($ticket['descricao'], 0, 80, '...')) ?></p>
    </div>
<?php
    endwhile;
else:
    echo "<p class='mensagem-vazia'>Nenhum ticket encontrado.</p>";
endif;

$stmt->close();
?>
