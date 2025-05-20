<?php
include 'db.php';
session_start();

$sql = "SELECT * FROM suporte";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Usuário</th><th>Email</th><th>Assunto</th><th>Descrição</th><th>Ações</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['usuario']}</td>
            <td>{$row['email']}</td>
            <td>{$row['assunto']}</td>
            <td>{$row['descricao']}</td>
            <td>
                <a href="visualizarTicket.php?id=<?= $ticket['id'] ?>">Visualizar</a> |
                <a href="updateSuporte.php?id=<?= $ticket['id'] ?>">Editar</a> |
                <a href="deleteSuporte.php?id=<?= $ticket['id'] ?>" onclick="return confirm('Tem certeza que deseja deletar?')">Deletar</a>
<           /td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "Nenhum ticket encontrado.";
}

$conn->close();
?>