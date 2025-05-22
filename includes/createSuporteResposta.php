<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $resposta = $_POST['resposta'];
    
    if (!isset($_SESSION['usuario']['email'])) {
        die("Usuário não autenticado.");
    }
    $email_adm = $_SESSION['usuario']['email'];
    $status = 'respondido';

    $sql = 'INSERT INTO suporte (resposta, email_adm, status) VALUES (?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $resposta, $email_adm, $status);

    if ($stmt->execute()) {
        header("Location: ../pages/sucesso.php?sucesso=1");
        exit();
    } else {
        echo "Erro ao cadastrar: " . $conn->error;
    }

    $stmt->close();
}
$conn->close();
?>
