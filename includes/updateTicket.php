<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $assunto = $_POST['assunto'];
    $descricao = $_POST['descricao'];
    
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