<?php
include 'db.php';

$sql_checkar = "SELECT * FROM usuarios WHERE usuario = 'admin'";
$result_checkar = $conn->query($sql_checkar);

if ($result_checkar->num_rows == 0) {
    $usuario = 'admin';

    $senha = password_hash(123, PASSWORD_DEFAULT); //usa hash na senha para segurança

    $sql = "INSERT INTO usuarios (usuario, senha) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ss", $usuario, $senha);
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