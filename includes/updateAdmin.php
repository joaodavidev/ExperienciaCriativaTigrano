<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $usuario = $_POST['usuario'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $sql = "UPDATE usuarios SET usuario = ?, senha = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $usuario, $senha, $id);

    if ($stmt->execute()) {
        echo "Administrador atualizado com sucesso!";
    } else {
        echo "Erro ao atualizar.";
    }
}
?>

<form method="POST">
    <input type="hidden" name="id" value="<?= $admin['id'] ?>" required>
    <input type="text" name="usuario" value="<?= $admin['usuario'] ?>" required><br>
    <input type="password" name="senha" placeholder="Nova Senha" required><br>
    <button type="submit" name="update">Atualizar</button>
</form>
