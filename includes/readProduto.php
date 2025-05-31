<?php
require_once 'db.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../pages/login.php");
    exit();
}

$vendedorEmail = $_SESSION['usuario']['email'];

$sql = "SELECT * FROM produtos WHERE vendedor_email = ? ORDER BY id DESC";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro na preparação: " . $conn->error);
}

$stmt->bind_param("s", $vendedorEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='tabela-linha'>";

        // PRODUTO
        echo "<span class='produto-info'>"; 
        echo "<div class='icone-produto'>" . substr($row['nome'], 0, 1) . "</div>";
        echo "<div><strong>" . htmlspecialchars($row['nome']) . "</strong><p>" . htmlspecialchars($row['descricao']) . "</p></div>";
        echo "</span>";

        // CATEGORIA
        echo "<span>" . htmlspecialchars($row['categoria']) . "</span>";

        // PREÇO
        echo "<span>R$ " . number_format($row['preco'], 2, ',', '.') . "</span>";

        // STATUS
        $classeStatus = strtolower($row['status']) === 'ativo' ? 'ativo' : 'inativo';
        echo "<span><span class='status {$classeStatus}'>" . htmlspecialchars($row['status']) . "</span></span>";

        // AÇÕES
        echo "<span class='acoes'>";

        // Editar
        echo "<i class='bx bx-edit' onclick='abrirModal({
            id: \"{$row['id']}\",
            nome: \"" . addslashes($row['nome']) . "\",
            categoria: \"" . addslashes($row['categoria']) . "\",
            preco: \"{$row['preco']}\",
            descricao: \"" . addslashes($row['descricao']) . "\",
            status: \"{$row['status']}\"
        })'></i>";

        // Excluir
        echo "<form action='../includes/deleteProduto.php' method='POST' style='display:inline;' onsubmit='return confirm(\"Deseja excluir este produto?\")'>";
        echo "<input type='hidden' name='id' value='{$row['id']}'>";
        echo "<button type='submit' style='background:none;border:none;padding:0;margin:0;cursor:pointer;'>";
        echo "<i class='bx bx-trash'></i>";
        echo "</button>";
        echo "</form>";

        echo "</span>";
        echo "</div>";
    }
} else {
    echo "<p style='margin: 10px;'>Nenhum produto cadastrado.</p>";
}

$stmt->close();
$conn->close();
?>
