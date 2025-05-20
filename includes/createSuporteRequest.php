<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $usuario = $_POST['usuario'];
    $assunto = $_POST['assunto'];
    $descricao = $_POST['descricao'];
    $email = $_POST['email'];
    $data_envio = $_POST('data_envio');

    $sql = 'INSERT INTO suporte (usuario, assunto, descricao, email, data_envio) VALUES (?, ?, ?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $usuario, $email, $assunto, $descricao, $data_envio);

    if ($stmt->execute()) {
        header("Location: ../pages/sucesso.php");
        exit();
    } else {
        echo "Erro ao cadastrar: " . $conn->error;
    }

    $stmt->close();
}
$conn->close();
?>
