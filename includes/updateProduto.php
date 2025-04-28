<?php
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $id = $_POST['id'];
  $nome = $_POST['nome'];
  $preco = $_POST['preco'];
  $descricao = $_POST['descricao'];

  // metodo prepared statements = mais seguro para SQL Injection
  $sql = "UPDATE produtos SET nome = ?, preco = ?, descricao = ? WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sdsi", $nome, $preco, $descricao, $id);

  if ($stmt->execute()) {
    header("Location: cadastarProdutos.php");
    exit();
  } else {
    echo "Erro ao atualizar: " . $conn->error;
  }
}
?>