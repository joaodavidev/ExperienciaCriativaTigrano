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
            <td>{$row['data_envio']}</td>
            <td>{$row['status']}</td>
            <td>
                <a href='../includes/updateSuporte.php?id={$row['id']}'>Editar</a>
                <a href='../includes/deleteSuporte.php?id={$row['id']}' onclick='return confirm(\"Tem certeza que deseja deletar?\")'>Deletar</a>
                form action='../includes/createSuporteResposta.php' method='POST'>
                    <input type='hidden' name='id' value='{$row['id']}'>
                    <textarea name='resposta' placeholder='Digite sua resposta aqui'></textarea>
                    <button type='submit'>Responder</button>
                </form>
            </td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "Nenhum ticket encontrado.";
}

$conn->close();
?>