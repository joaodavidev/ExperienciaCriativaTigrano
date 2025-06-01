<?php
session_start();
require_once 'db.php';

// Verifica se o admin está logado
if (!isset($_SESSION['email'])) {
    header("Location: ../pages/loginadm.php");
    exit();
}

$email = $_SESSION['email'];

// Deleta o admin
$sql = "DELETE FROM adm WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);

if ($stmt->execute()) {
    session_destroy(); // Encerra a sessão
    $mensagem = "Admin excluído com sucesso!";
    $tipo = "success";
} else {
    $mensagem = "Erro ao excluir admin.";
    $tipo = "error";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Excluir Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
Swal.fire({
    icon: '<?= $tipo ?>',
    title: '<?= $mensagem ?>',
    confirmButtonText: 'OK',
    background: localStorage.getItem("tema") === "claro" ? "#ffffff" : "#121212",
    color: localStorage.getItem("tema") === "claro" ? "#121212" : "#ffffff",
    confirmButtonColor: "#1D4ED8"
}).then(() => {
    window.location.href = "../pages/loginadm.php";
});
</script>
</body>
</html>
