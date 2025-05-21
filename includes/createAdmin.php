<?php
include 'db.php';

$sql_checkar = "SELECT * FROM adm WHERE email = 'admin@email.com'";
$result_checkar = $conn->query($sql_checkar);

if ($result_checkar->num_rows == 0) {
    $email = 'admin@email.com';
    $senha = password_hash(123, PASSWORD_DEFAULT); // senha segura

    $sql = "INSERT INTO adm (email, senha) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ss", $email, $senha);
        if ($stmt->execute()) {
            echo '
                <!DOCTYPE html>
                <html lang="pt-br">
                <head>
                    <meta charset="UTF-8">
                    <title>Admin Criado</title>
                    <link rel="stylesheet" href="../assets/css/login.css">
                    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                </head>
                <body>
                    <script>
                        Swal.fire({
                            icon: "success",
                            title: "Admin criado com sucesso!",
                            text: "Você será redirecionado para o login.",
                            confirmButtonText: "OK"
                        }).then(() => {
                            window.location.href = "../pages/loginadm.php";
                        });
                    </script>
                </body>
                </html>';
            exit();
        } else {
            echo "Erro ao cadastrar admin: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Erro ao preparar SQL: " . $conn->error;
    }
} else {
    echo '
        <!DOCTYPE html>
        <html lang="pt-br">
        <head>
            <meta charset="UTF-8">
            <title>Admin já existe</title>
            <link rel="stylesheet" href="../assets/css/login.css">
            <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: "info",
                    title: "Admin já existe!",
                    text: "Redirecionando para o login...",
                    confirmButtonText: "OK"
                }).then(() => {
                    window.location.href = "../pages/loginadm.php";
                });
            </script>
        </body>
        </html>';
    exit();
}

$conn->close();
?>
