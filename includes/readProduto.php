<?php
include '../includes/db.php';
session_start();

$sql = "SELECT * FROM produtos";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
  echo "<div class='tabela-linha'>";
  
  // PRODUTO (com ícone e nome)
  echo "<div class='produto-info'>";
  echo "<div class='icone-produto'>" . htmlspecialchars(substr($row['nome'], 0, 1)) . "</div>";
  echo "<div><strong>" . htmlspecialchars($row['nome']) . "</strong><p>" . htmlspecialchars($row['descricao']) . "</p></div>";
  echo "</div>";

  // CATEGORIA
  echo "<span>" . htmlspecialchars($row['categoria']) . "</span>";

  // PREÇO
  echo "<span>R$ " . number_format($row['preco'], 2, ',', '.') . "</span>";

  // STATUS
  $statusClass = strtolower($row['status']) === 'ativo' ? 'ativo' : '';
  echo "<span class='status $statusClass'>" . htmlspecialchars($row['status']) . "</span>";

  // AÇÕES (última coluna)
  echo "<div class='acoes'>";
  echo "<i class='bx bx-edit editar-produto' data-id='" . $row['id'] . "' title='Editar'></i>";
  echo "<i class='bx bx-trash deletar-produto' data-id='" . $row['id'] . "' title='Excluir'></i>";
  echo "</div>";

  echo "</div>";
}
?>
