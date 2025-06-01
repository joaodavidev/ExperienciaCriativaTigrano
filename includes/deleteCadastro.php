<?php
session_start();
require_once '../includes/db.php';
include 'verificar_login.php';

$email = $_SESSION['usuario']['email'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['deletar_conta'])) {
  $sql = "DELETE FROM usuarios WHERE email = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);

  if ($stmt->execute()) {
    $stmt->close();
    header("Location: ../pages/configuracoes.php?sucesso=1");
    exit();
  } else {
    $stmt->close();
    header("Location: ../pages/configuracoes.php?erro=Erro ao deletar a conta");
    exit();
  }
}
?>
