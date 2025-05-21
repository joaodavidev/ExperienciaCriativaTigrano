<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['usuario'])) {
  header("Location: ../pages/login.php");
  exit();
}

$email = $_SESSION['usuario']['email'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['deletar_conta'])) {
  $sql = "DELETE FROM usuarios WHERE email = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);

  if ($stmt->execute()) {
    $stmt->close();
    // ⚠️ NÃO destruir a sessão aqui
    header("Location: ../pages/configuracoes.php?sucesso=1");
    exit();
  } else {
    $stmt->close();
    header("Location: ../pages/configuracoes.php?erro=Erro ao deletar a conta");
    exit();
  }
}
?>
