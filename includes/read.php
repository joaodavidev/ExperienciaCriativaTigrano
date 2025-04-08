<?php
include 'db.php';

$sql = "SELECT * FROM produtos";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
  echo "<div class='produto'>";
  echo "<strong>" . htmlspecialchars($row['nome']) . "</strong><br>";
  echo "R$ " . number_format($row['preco'], 2, ',', '.') . "<br>";
  echo "<p>" . htmlspecialchars($row['descricao']) . "</p>";
  
  echo "<div class='produto-conteudo'>";

  // form de atualizacao
  echo "<form class='form-editar' action='update.php' method='POST'>
          <input type='hidden' name='id' value='" . $row['id'] . "'>
          <input type='text' name='nome' value='" . htmlspecialchars($row['nome']) . "'>
          <input type='number' step='0.01' name='preco' value='" . $row['preco'] . "'>
          <input type='text' name='descricao' value='" . htmlspecialchars($row['descricao']) . "'>
          <button type='submit'>Editar</button>
        </form>";

  // form de excluir 
  echo "<form class='form-excluir' action='delete.php' method='POST'>
          <input type='hidden' name='id' value='" . $row['id'] . "'>
          <button type='submit'>Excluir</button>
        </form>";

  echo "</div>"; // .produto-conteudo
  echo "</div>"; // .produto
}
?>
