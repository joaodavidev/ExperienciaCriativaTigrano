<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['usuario'])) {
  header("Location: login.php");
  exit();
}

$email = $_SESSION['usuario']['email'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['deletar_conta'])) {
  $sql = "DELETE FROM usuarios WHERE email = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);
  
  if ($stmt->execute()) {
    session_destroy();
    header("Location: ../pages/login.php");
    exit();
  } else {
    $erro = "Erro ao deletar a conta.";
  }
}
?>