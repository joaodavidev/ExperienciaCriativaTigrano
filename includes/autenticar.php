<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['usuario'];
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("SELECT email, nome, senha FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();        if (password_verify($senha, $user['senha'])) {
            $_SESSION['usuario'] = [
                'email' => $user['email'],
                'nome'  => $user['nome']
            ];
            $_SESSION['usuario_logado'] = true;
            $_SESSION['usuario_logado'] = true;

            header("Location: ../pages/marketplace.php");
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
