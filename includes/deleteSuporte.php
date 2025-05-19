<?php
include 'db.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: ../pages/readSuporte.php");
    exit();
}

$id = intval($_GET['id']);

$sql = "DELETE FROM suporte WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    header("Location: ../pages/readSuporte.php");
    exit();
} else {
    echo "Erro ao deletar: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
