<?php
session_start();
require_once 'db.php';
include 'verificar_login.php'; // Protege acesso

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $email = $_SESSION['usuario']['email'];
    
    error_log('POST recebido em deleteTicket.php: ' . var_export($_POST, true));

    if ($id > 0) {
        // ✅ Garante que o ticket pertence ao usuário logado
        $sql = "DELETE FROM suporte WHERE id = ? AND email_usuario = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param('is', $id, $email);
            if ($stmt->execute()) {
                error_log('Ticket excluído com sucesso: id=' . $id);
                header('Location: ../pages/suporteUsuario.php?excluido=1');
                exit();
            } else {
                error_log('Erro ao excluir ticket: ' . $stmt->error);
                echo 'Erro ao excluir ticket: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            error_log('Erro na preparação da query: ' . $conn->error);
            echo 'Erro na preparação da query: ' . $conn->error;
        }
    } else {
        error_log('ID inválido para exclusão: ' . $id);
        echo 'ID inválido.';
    }
} else {
    error_log('Requisição inválida em deleteTicket.php. POST=' . var_export($_POST, true));
    echo 'Requisição inválida.';
}

$conn->close();
?>
