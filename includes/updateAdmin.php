<?php
session_start();

// Proteção: só permite acesso de admins
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

// Carrega os dados do admin selecionado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && !isset($_POST['update'])) {
    $id = $_POST['id'];

    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
}

// Atualiza os dados do admin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $usuario = $_POST['usuario'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $sql = "UPDATE usuarios SET usuario = ?, senha = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $usuario, $senha, $id);    if ($stmt->execute()) {
        header("Location: ../pages/usuarios.php?admin_atualizado=1");
        exit();
    } else {
        header("Location: ../pages/usuarios.php?erro=erro_atualizar_admin");
        exit();
    }
}
?>

<?php if (isset($admin)) : ?>
<form method="POST">
    <input type="hidden" name="id" value="<?= $admin['id'] ?>" required>
    <input type="text" name="usuario" value="<?= $admin['usuario'] ?>" required><br>
    <input type="password" name="senha" placeholder="Nova Senha" required><br>
    <button type="submit" name="update">Atualizar</button>
</form>
<?php endif; ?>
