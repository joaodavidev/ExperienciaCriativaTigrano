<?php
session_start(); 


if (!isset($_SESSION['usuario'])) {
    header("Location: login.php"); 
    exit();
}

require_once 'db.php'; 

$sql = "SELECT id, usuario FROM usuarios";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários Cadastrados</title>
    <link rel="stylesheet" href="readCadastro.css">
</head>
<body>

    <div class="login-container">
        <header>
            <h1>TIGRANO</h1>
            <nav>
                <a href="readCadastro.php"><button>Usuários</button></a>
                <a href="createCadastro.php"><button>Novo Cadastro</button></a>
                <a href="logout.php"><button>Sair</button></a>
            </nav>
        </header>

<h2>Usuários Cadastrados</h2>
        <table class="tabela-usuarios">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuário</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['id'] . "</td>
                                <td>" . $row['usuario'] . "</td>
                                <td>
                                    <a href='updateCadastro.php?id=" . $row['id'] . "'>Editar Cadastro</a> | 
                                    <a href='deleteCadastro.php?id=" . $row['id'] . "'>Excluir Cadastro</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Nenhum usuário cadastrado.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>
