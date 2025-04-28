<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $id = $_POST['id'];

  $sql = "DELETE FROM produtos WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    header("Location: cadastrarProdutos.php");
    exit();
  } else {
    echo "Erro ao excluir produto: " . $conn->error;
  }
}
?>