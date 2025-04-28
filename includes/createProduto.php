<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nome = $_POST['nome'];
  $preco = $_POST['preco'];
  $descricao = $_POST['descricao'];

  $sql = "INSERT INTO produtos (nome, preco, descricao) VALUES (?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sds", $nome, $preco, $descricao);

  if ($stmt->execute()) {
    header("Location: cadastrarProdutos.php");
    exit();
  } else {
    echo "Erro ao cadastrar: " . $conn->error;
  }
}
?>