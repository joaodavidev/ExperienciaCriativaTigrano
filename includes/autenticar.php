<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['usuario'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    // Validar campos obrigatórios
    if (empty($email) || empty($senha)) {
        header("Location: ../pages/login.php?erro=dados_incompletos");
        exit();
    }

    // Verificar se a coluna 'ativo' existe, se não existir, adicionar
    $resultColumn = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'ativo'");
    if ($resultColumn->num_rows == 0) {
        $conn->query("ALTER TABLE usuarios ADD COLUMN ativo TINYINT(1) DEFAULT 1");
    }

    $stmt = $conn->prepare("SELECT email, nome, senha, COALESCE(ativo, 1) as ativo FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verificar se a conta está ativa
        if ($user['ativo'] == 0) {
            $stmt->close();
            header("Location: ../pages/login.php?conta_desativada_login=1");
            exit();
        }
        
        if (password_verify($senha, $user['senha'])) {
            $_SESSION['usuario'] = [
                'email' => $user['email'],
                'nome'  => $user['nome']
            ];
            $_SESSION['usuario_logado'] = true;

            header("Location: ../pages/marketplace.php");
            exit();        } else {
            header("Location: ../pages/login.php?erro=senha_incorreta");
            exit();
        }
    } else {
        header("Location: ../pages/login.php?erro=usuario_nao_encontrado");
        exit();
    }

    $stmt->close();
}
?>
