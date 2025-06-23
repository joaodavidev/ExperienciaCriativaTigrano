<?php
session_start();
require_once 'db.php';
include 'verificar_login.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $email = $_SESSION['usuario']['email'];
    
    error_log('POST recebido em deleteTicket.php: ' . var_export($_POST, true));

    if ($id > 0) {
        $sql = "DELETE FROM suporte WHERE id = ? AND email_usuario = ?";
        $stmt = $conn->prepare($sql);        if ($stmt) {
            $stmt->bind_param('is', $id, $email);
            if ($stmt->execute()) {
                error_log('Ticket excluído com sucesso: id=' . $id);
                header('Location: ../pages/suporteUsuario.php?excluido=1');
                exit();
            } else {
                error_log('Erro ao excluir ticket: ' . $stmt->error);
                header('Location: ../pages/suporteUsuario.php?erro=erro_excluir');
                exit();
            }
            $stmt->close();
        } else {
            error_log('Erro na preparação da query: ' . $conn->error);
            header('Location: ../pages/suporteUsuario.php?erro=erro_prepare');
            exit();
        }
    } else {
        error_log('ID inválido para exclusão: ' . $id);
        header('Location: ../pages/suporteUsuario.php?erro=id_invalido');
        exit();
    }
} else {
    error_log('Requisição inválida em deleteTicket.php. POST=' . var_export($_POST, true));
    header('Location: ../pages/suporteUsuario.php?erro=requisicao_invalida');
    exit();
}

$conn->close();
?>
