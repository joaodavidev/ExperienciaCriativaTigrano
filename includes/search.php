<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];

    $sql_search = "SELECT * FROM produtos WHERE nome = ?";
    $stmt_search = $conn->prepare($sql_search);
    $stmt_search->bind_param("s", $nome);
    $stmt_search->execute();
    $result = $stmt_search->get_result();

    if ($result->num_rows > 0) {
        $produto = $result->fetch_assoc();
        header("Location: /product.php?id=" . $produto['id']);
        exit();
    } else {
        echo "Produto não encontrado.";
        exit();
    }
}
?>
