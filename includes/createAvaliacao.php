<?php
session_start();
require_once 'db.php';
include 'verificar_login.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_email = $_SESSION['usuario']['email'];
    $produto_id = intval($_POST['produto_id'] ?? 0);
    $estrelas = intval($_POST['estrelas'] ?? 0);
    $comentario = trim($_POST['comentario'] ?? '');    // Validações
    if ($produto_id <= 0) {
        header("Location: ../pages/compras.php?erro=produto_invalido");
        exit();
    }

    if ($estrelas < 1 || $estrelas > 5) {
        header("Location: ../pages/compras.php?erro=avaliacao_invalida");
        exit();
    }

    // Verificar se o usuário realmente comprou este produto
    $stmt_verificar = $conn->prepare("SELECT id FROM vendas WHERE comprador_email = ? AND produto_id = ?");
    $stmt_verificar->bind_param("si", $usuario_email, $produto_id);
    $stmt_verificar->execute();
    $result_verificar = $stmt_verificar->get_result();    if ($result_verificar->num_rows === 0) {
        header("Location: ../pages/compras.php?erro=produto_nao_comprado");
        exit();
    }
    $stmt_verificar->close();

    // Verificar se já existe uma avaliação deste usuário para este produto
    $stmt_existe = $conn->prepare("SELECT id FROM avaliacao WHERE usuario_email = ? AND produto_id = ?");
    $stmt_existe->bind_param("si", $usuario_email, $produto_id);
    $stmt_existe->execute();
    $result_existe = $stmt_existe->get_result();

    if ($result_existe->num_rows > 0) {
        // Atualizar avaliação existente
        $stmt_update = $conn->prepare("UPDATE avaliacao SET estrelas = ?, comentario = ? WHERE usuario_email = ? AND produto_id = ?");
        $stmt_update->bind_param("issi", $estrelas, $comentario, $usuario_email, $produto_id);
          if ($stmt_update->execute()) {
            header("Location: ../pages/compras.php?avaliacao_atualizada=1");
        } else {
            header("Location: ../pages/compras.php?erro=erro_atualizar_avaliacao");
        }
        $stmt_update->close();
    } else {
        // Inserir nova avaliação
        $stmt_insert = $conn->prepare("INSERT INTO avaliacao (usuario_email, estrelas, comentario, produto_id) VALUES (?, ?, ?, ?)");
        $stmt_insert->bind_param("sisi", $usuario_email, $estrelas, $comentario, $produto_id);
        
        if ($stmt_insert->execute()) {
            header("Location: ../pages/compras.php?avaliacao_criada=1");
        } else {
            header("Location: ../pages/compras.php?erro=erro_criar_avaliacao");
        }
        $stmt_insert->close();
    }
    
    $stmt_existe->close();
} else {
    header("Location: ../pages/compras.php?erro=metodo_invalido");
}

$conn->close();
?>
