<?php
session_start();

require_once 'db.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];  
    $senha = $_POST['senha'];

  
    $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();


        if (password_verify($senha, $user['senha'])) {
            $_SESSION['usuario'] = $usuario;
            header("Location: readCadastro.php");
            exit();
        } else {
            echo "Senha incorreta!";
        }
    } else {
        echo "Usuário não encontrado!";
    }
}
?>
