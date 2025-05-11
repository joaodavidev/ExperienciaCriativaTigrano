<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    // Protegido contra SQL Injection
    $stmt = $conn->prepare("SELECT id, usuario, senha, tipo FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($senha, $user['senha'])) {
            $_SESSION['usuario'] = [
                'id' => $user['id'],
                'nome' => $user['usuario'],
                'tipo' => $user['tipo'] // Ex: 'admin' ou 'usuario'
            ];

            header("Location: ../pages/paginaInicial.php"); // Altere para sua home real
            exit();
        } else {
            echo "Senha incorreta!";
        }
    } else {
        echo "Usuário não encontrado!";
    }

    $stmt->close();
}
?>

