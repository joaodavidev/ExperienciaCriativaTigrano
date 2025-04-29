<?php
include '../includes/db.php';

$sql = "SELECT * FROM produtos";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
  echo "<div class='produto' id='produto-" . $row['id'] . "'>";
  echo "<strong>" . htmlspecialchars($row['nome']) . "</strong><br>";
  echo "R$ " . number_format($row['preco'], 2, ',', '.') . "<br>";
  echo "<p>" . htmlspecialchars($row['descricao']) . "</p>";
  
  echo "<div class='produto-conteudo'>";

  echo "<form class='form-editar' action='updateProduto.php' method='POST'>
          <input type='hidden' name='id' value='" . $row['id'] . "'>
          <input type='text' name='nome' value='" . htmlspecialchars($row['nome']) . "'>
          <input type='number' step='0.01' name='preco' value='" . $row['preco'] . "'>
          <input type='text' name='descricao' value='" . htmlspecialchars($row['descricao']) . "'>
          <button type='submit'>Editar</button>
        </form>";

  echo "<form class='form-excluir' action='deleteProduto.php' method='POST'>
          <input type='hidden' name='id' value='" . $row['id'] . "'>
          <button type='submit'>Excluir</button>
        </form>";
  
  echo "<form class='form-selecionar' action='../pages/carrinho.php' method='POST'>
          <input type='hidden' name='id' value='" . $row['id'] . "'>
          <button type='submit'>Selecionar</button>
        </form>";

  echo "</div>";
  echo "</div>";
}
?>
