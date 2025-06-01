<?php
session_start();
require_once 'db.php';
include 'verificar_login.php'; // ✅ Proteção unificada

$vendedorEmail = $_SESSION['usuario']['email'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim($_POST['nome'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $preco = floatval($_POST['preco'] ?? 0);
    $descricao = trim($_POST['descricao'] ?? '');
    $status = trim($_POST['status'] ?? 'Ativo');

    if ($nome && $categoria && $preco && $descricao && $vendedorEmail) {
        $sql = "INSERT INTO produtos (nome, categoria, preco, descricao, status, vendedor_email)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Erro no prepare: " . $conn->error);
        }

        $stmt->bind_param("ssdsss", $nome, $categoria, $preco, $descricao, $status, $vendedorEmail);

        if ($stmt->execute()) {
            header("Location: ../pages/produto.php?sucesso=1");
            exit;
        } else {
            echo "Erro ao inserir: " . $stmt->error;
        }
    } else {
        echo "Todos os campos são obrigatórios.";
    }
} else {
    echo "Requisição inválida.";
}
