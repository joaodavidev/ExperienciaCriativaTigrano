<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $usuario = $_POST['usuario'];
  $email = $_POST['email'];
  $assunto = $_POST['assunto'];
  $descricao = $_POST['descricao'];

  $sql = "INSERT INTO suporte (usuario, email, assunto, descricao) VALUES (?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssss", $usuario, $email, $assunto, $descricao);

  if ($stmt->execute()) {
    header("Location: <pages>sucesso.php"); // Página de confirmação
    exit();
  } else {
    echo "Erro ao cadastrar: " . $conn->error;
  }

  $stmt->close();
  $conn->close();
}
?>
