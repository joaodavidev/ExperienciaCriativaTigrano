<?php
session_start();
require_once 'db.php';
include 'verificar_login.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $assunto = trim($_POST['assunto'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $email = $_SESSION['usuario']['email'];

    // Verifica se o ticket pertence ao usuário logado
    $check = $conn->prepare("SELECT id FROM suporte WHERE id = ? AND email_usuario = ?");
    $check->bind_param("is", $id, $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'Acesso negado ao ticket.']);
        exit();
    }

    $sql = "UPDATE suporte SET assunto = ?, descricao = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssi', $assunto, $descricao, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erro ao atualizar ticket.']);
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>
