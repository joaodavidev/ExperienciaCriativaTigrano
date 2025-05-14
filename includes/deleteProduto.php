<?php
include 'db.php';

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $sql = "DELETE FROM produtos WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Erro no prepare: " . $conn->error);
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: ../pages/produto.php?removido=1");
        exit;
    } else {
        echo "Erro ao deletar: " . $stmt->error;
    }
} else {
    echo "Requisição inválida.";
}
?>
