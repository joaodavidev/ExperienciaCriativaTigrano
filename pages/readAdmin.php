<?php
session_start();

// Bloqueia acesso de não-admins
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

// Busca todos os usuários com tipo admin
$sql = "SELECT * FROM usuarios WHERE tipo = 'admin'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Usuário</th>
                <th>Ações</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['usuario']}</td>
                <td>
                    <form action='updateAdmin.php' method='POST' style='display:inline'>
                        <input type='hidden' name='id' value='{$row['id']}'>
                        <button type='submit'>Editar</button>
                    </form>
                    <form action='deleteAdmin.php' method='POST' style='display:inline'>
                        <input type='hidden' name='id' value='{$row['id']}'>
                        <button type='submit'>Excluir</button>
                    </form>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "Nenhum administrador encontrado.";
}

$conn->close();
?>

