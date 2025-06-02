<?php
session_start();
include '../includes/db.php';
include '../includes/verificar_login.php';

$email_logado = $_SESSION['usuario']['email'];

$id = intval($_GET['id']);

$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Usuário não encontrado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];
    $confirmar = $_POST['confirmar_senha'];

    if (!empty($senha)) {
        if ($senha !== $confirmar) {
            die("As senhas não coincidem.");
        }
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    } else {
        $senhaHash = $user['senha']; // mantém a senha atual
    }

    $sql = "UPDATE usuarios SET usuario = ?, senha = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $usuario, $senhaHash, $id);
    $stmt->execute();

    header("Location: readCadastro.php");
    exit();
}
?>
