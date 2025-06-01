<?php
session_start();
include 'db.php';
include 'verificar_login.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $assunto = $_POST['assunto'];
    $descricao = $_POST['descricao'];
    $data_envio = $_POST['data_envio'];

    $email_usuario = $_SESSION['usuario']['email']; 

    $sql = 'INSERT INTO suporte (email_usuario, assunto, descricao, data_envio) VALUES (?, ?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $email_usuario, $assunto, $descricao, $data_envio);

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
