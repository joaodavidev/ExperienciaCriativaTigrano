<?php
session_start();
include '../includes/db.php';
include '../includes/verificar_login.php';

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

    $email = $_SESSION['usuario']['email']; // email do vendedor logado

    if ($id && $nome && $categoria && $preco && $descricao) {
        // Garante que o produto pertence ao vendedor logado
        $verifica = $conn->prepare("SELECT id FROM produtos WHERE id = ? AND vendedor_email = ?");
        $verifica->bind_param("is", $id, $email);
        $verifica->execute();
        $resultado = $verifica->get_result();

        if ($resultado->num_rows === 0) {
            die("Produto não encontrado ou você não tem permissão para editar.");
        }

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
