<?php
session_start();
include 'db.php';
include 'verificar_login.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $assunto = trim($_POST['assunto'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $data_envio = $_POST['data_envio'] ?? date('Y-m-d H:i:s');
    $email_usuario = $_SESSION['usuario']['email']; 

    // Validar campos obrigatórios
    if (empty($assunto) || empty($descricao)) {
        header("Location: ../pages/suporteUsuario.php?erro=campos_obrigatorios");
        exit();
    }

    $sql = 'INSERT INTO suporte (email_usuario, assunto, descricao, data_envio) VALUES (?, ?, ?, ?)';
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        header("Location: ../pages/suporteUsuario.php?erro=erro_prepare");
        exit();
    }
    
    $stmt->bind_param("ssss", $email_usuario, $assunto, $descricao, $data_envio);

    if ($stmt->execute()) {
        header("Location: ../pages/sucesso.php?sucesso=1");
        exit();
    } else {
        header("Location: ../pages/suporteUsuario.php?erro=erro_inserir");
        exit();
    }

    $stmt->close();
}
$conn->close();
?>
