<?php
session_start();
include 'db.php';
include 'verificar_login.php';

if (!isset($_GET['id'])) {
    header("Location: ../pages/readSuporte.php");
    exit();
}

$id = intval($_GET['id']);
$email = $_SESSION['usuario']['email']; 

// Deleta somente se o ticket pertence ao usuário
$sql = "DELETE FROM suporte WHERE id = ? AND email_usuario = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro no prepare: " . $conn->error);
}

$stmt->bind_param('is', $id, $email);

if ($stmt->execute()) {
    header("Location: ../pages/readSuporte.php");
    exit();
} else {
    echo "Erro ao deletar: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
