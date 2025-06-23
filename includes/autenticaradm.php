<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    // Validar campos obrigatórios
    if (empty($email) || empty($senha)) {
        header("Location: ../pages/loginadm.php?erro=dados_incompletos");
        exit();
    }

    $stmt = $conn->prepare("SELECT email, senha FROM adm WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($senha, $user['senha'])) {
            $_SESSION['email'] = $user['email'];
            $_SESSION['admin'] = true;

            header("Location: ../pages/admin.php");
            exit();        } else {
            header("Location: ../pages/loginadm.php?erro=senha_incorreta");
            exit();
        }
    } else {
        header("Location: ../pages/loginadm.php?erro=usuario_nao_encontrado");
        exit();
    }

    $stmt->close();
}
?>
