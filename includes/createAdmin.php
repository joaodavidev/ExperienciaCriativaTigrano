<?php
include 'db.php';

$sql_checkar = "SELECT * FROM adm WHERE nome = 'admin'";
$result_checkar = $conn->query($sql_checkar);

if ($result_checkar->num_rows == 0) {
    $usuario = 'admin';
    $email = 'adm@adm';
    $senha = password_hash(123, PASSWORD_DEFAULT); //usa hash na senha para segurança

    $sql = "INSERT INTO adm (nome, email, senha) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sss", $usuario, $email, $senha);
        if ($stmt->execute()) {
            echo "Admin cadastrado com sucesso!";
        } else {
            echo "Erro ao cadastrar admin: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Erro ao preparar SQL: " . $conn->error;
    }
} else {
    echo "Admin já existe!";
}

$conn->close();
?>