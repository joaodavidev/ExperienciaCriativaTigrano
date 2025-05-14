<?php
include '../includes/db.php';

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $preco = floatval($_POST['preco'] ?? 0);
    $descricao = trim($_POST['descricao'] ?? '');
    $status = trim($_POST['status'] ?? 'Ativo');

    if ($id && $nome && $categoria && $preco && $descricao) {
        $sql = "UPDATE produtos SET nome = ?, categoria = ?, preco = ?, descricao = ?, status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Erro no prepare: " . $conn->error);
        }

        $stmt->bind_param("ssdssi", $nome, $categoria, $preco, $descricao, $status, $id);

        if ($stmt->execute()) {
            header("Location: ../pages/produto.php?atualizado=1");
            exit;
        } else {
            echo "Erro ao atualizar: " . $stmt->error;
        }
    } else {
        echo "Todos os campos são obrigatórios.";
    }
} else {
    echo "Requisição inválida.";
}
?>
