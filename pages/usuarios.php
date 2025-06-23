<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {    if (isset($_POST['excluir'])) {
        $email = $_POST['excluir'];
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->close();
        header("Location: usuarios.php?usuario_excluido=1");
        exit();
    }if (isset($_POST['resetar'])) {
        $email = $_POST['resetar'];
        $novaSenha = password_hash("Tigrano123", PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE email = ?");
        $stmt->bind_param("ss", $novaSenha, $email);
        $stmt->execute();
        $stmt->close();
        header("Location: usuarios.php?senha_resetada=1");
        exit();
    }
}

// Consulta usuários
$result = $conn->query("SELECT email, nome, sexo, idade, cpf FROM usuarios");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Gerenciar Usuários</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
  <link rel="stylesheet" href="../assets/css/usuarios.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<nav class="sidebar active">
  <div class="logo-menu">
    <h2 class="logo">Tigrano</h2>
    <i class='bx bx-menu toggle-btn'></i>
  </div>
<ul class="lista">
  <li class="lista-item"><a href="admin.php"><i class='bx bx-pie-chart-alt-2'></i><span class="nome-link" style="--i:1;">Admin</span></a></li>
  <li class="lista-item"><a href="usuarios.php"><i class='bx bxs-user-detail'></i><span class="nome-link" style="--i:2;">Usuários</span></a></li>
  <li class="lista-item"><a href="tickets.php"><i class='bx bx-support'></i><span class="nome-link" style="--i:3;">Tickets</span></a></li>
  <li class="espacador"></li>
  <li class="lista-item">
    <form id="formDeleteAdmin" action="../includes/deletecadastroAdmin.php" method="POST" style="display:inline;">
      <input type="hidden" name="excluir" value="1">
      <a href="#" onclick="confirmarExclusao(event)">
        <i class='bx bx-user-x'></i>
        <span class="nome-link" style="--i:4;">Excluir Admin</span>
      </a>
    </form>
  </li>
  <li class="lista-item"><a href="#" class="btn-toggle-tema"><i class='bx bx-moon'></i><span class="nome-link" style="--i:5;">Claro/Escuro</span></a></li>
  <li class="lista-item"><a href="../includes/logout.php"><i class='bx bx-log-out'></i><span class="nome-link" style="--i:6;">Sair</span></a></li>
</ul>
</nav>
<main class="main-content">
  <h1>Gerenciar Usuários</h1>

  <div class="tabela-usuarios">
    <table>
      <thead>
        <tr>
          <th>Nome</th>
          <th>Email</th>
          <th>Sexo</th>
          <th>Idade</th>
          <th>CPF</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['nome']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['sexo']) ?></td>
            <td><?= $row['idade'] ?></td>
            <td><?= $row['cpf'] ?></td>            
            <td style="display: flex; gap: 8px;">
              <form method="POST">
                <input type="hidden" name="excluir" value="<?= htmlspecialchars($row['email']) ?>">
                <button type="button" onclick="confirmarExclusaoUsuario(this)" class="btn-excluir">Excluir</button>
              </form>

              <form method="POST">
                <input type="hidden" name="resetar" value="<?= htmlspecialchars($row['email']) ?>">
                <button type="button" class="btn-reset" data-email="<?= $row['email'] ?>" onclick="confirmarReset(this)">Resetar Senha</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</main>
<script>
function confirmarExclusaoUsuario(botao) {
  const temaClaro = localStorage.getItem("tema") === "claro";
  Swal.fire({
    icon: 'warning',
    title: 'Tem certeza?',
    text: 'Essa ação excluirá o usuário permanentemente.',
    showCancelButton: true,
    confirmButtonText: 'Sim, excluir',
    cancelButtonText: 'Cancelar',
    background: temaClaro ? "#E6E4E4" : "#262626",
    color: temaClaro ? "#121212" : "#ffffff",
    confirmButtonColor: "#b91c1c",
    cancelButtonColor: "#4b5563"
  }).then((result) => {
    if (result.isConfirmed) {
      botao.closest('form').submit();
    }
  });
}
function confirmarReset(botao) {
  const temaClaro = localStorage.getItem("tema") === "claro";
  Swal.fire({
    icon: 'question',
    title: 'Resetar senha?',
    text: 'A senha será redefinida para "Tigrano123".',
    showCancelButton: true,
    confirmButtonText: 'Sim, redefinir',
    cancelButtonText: 'Cancelar',
    background: temaClaro ? "#E6E4E4" : "#262626",
    color: temaClaro ? "#121212" : "#ffffff",
    confirmButtonColor: "#1D4ED8",
    cancelButtonColor: "#4b5563"
  }).then((result) => {
    if (result.isConfirmed) {
      botao.closest('form').submit();    }
  });
}

function confirmarExclusao(event) {
  event.preventDefault();
  const temaClaro = localStorage.getItem("tema") === "claro";
  Swal.fire({
    icon: 'warning',
    title: 'Tem certeza?',
    text: 'Esta ação irá excluir sua conta de administrador permanentemente.',
    showCancelButton: true,
    confirmButtonText: 'Sim, excluir',
    cancelButtonText: 'Cancelar',
    background: temaClaro ? "#E6E4E4" : "#262626",
    color: temaClaro ? "#121212" : "#ffffff",
    confirmButtonColor: "#b91c1c",
    cancelButtonColor: "#4b5563"
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById('formDeleteAdmin').submit();
    }
  });
}
</script>

<!-- Mensagens de sucesso e erro para usuários -->
<?php if (isset($_GET['usuario_excluido']) && $_GET['usuario_excluido'] == 1): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const tema = localStorage.getItem("tema") === "claro";
      
      Swal.fire({
        icon: 'success',
        title: 'Usuário excluído com sucesso!',
        confirmButtonText: 'OK',
        background: tema ? '#E6E4E4' : '#262626',
        color: tema ? '#121212' : '#ffffff',
        confirmButtonColor: '#1D4ED8'
      }).then(() => {
        const url = new URL(window.location.href);
        url.searchParams.delete('usuario_excluido');
        window.history.replaceState({}, document.title, url.toString());
      });
    });
  </script>
<?php endif; ?>

<?php if (isset($_GET['senha_resetada']) && $_GET['senha_resetada'] == 1): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const tema = localStorage.getItem("tema") === "claro";
      
      Swal.fire({
        icon: 'success',
        title: 'Senha redefinida com sucesso!',
        text: 'A nova senha é: Tigrano123',
        confirmButtonText: 'OK',
        background: tema ? '#E6E4E4' : '#262626',
        color: tema ? '#121212' : '#ffffff',
        confirmButtonColor: '#1D4ED8'
      }).then(() => {
        const url = new URL(window.location.href);
        url.searchParams.delete('senha_resetada');
        window.history.replaceState({}, document.title, url.toString());
      });
    });
  </script>
<?php endif; ?>

<?php if (isset($_GET['admin_atualizado']) && $_GET['admin_atualizado'] == 1): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const tema = localStorage.getItem("tema") === "claro";
      
      Swal.fire({
        icon: 'success',
        title: 'Administrador atualizado com sucesso!',
        confirmButtonText: 'OK',
        background: tema ? '#E6E4E4' : '#262626',
        color: tema ? '#121212' : '#ffffff',
        confirmButtonColor: '#1D4ED8'
      }).then(() => {
        const url = new URL(window.location.href);
        url.searchParams.delete('admin_atualizado');
        window.history.replaceState({}, document.title, url.toString());
      });
    });
  </script>
<?php endif; ?>

<!-- Tratamento de erros para usuários -->
<?php if (isset($_GET['erro'])): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const erro = '<?php echo $_GET["erro"]; ?>';
      const tema = localStorage.getItem("tema") === "claro";
      
      let titulo = 'Erro';
      let mensagem = 'Ocorreu um erro inesperado. Tente novamente.';
      
      switch(erro) {
        case 'erro_atualizar_admin':
          titulo = 'Erro ao atualizar';
          mensagem = 'Erro ao atualizar os dados do administrador. Tente novamente.';
          break;
        case 'erro_excluir_usuario':
          titulo = 'Erro ao excluir';
          mensagem = 'Erro ao excluir o usuário. Tente novamente.';
          break;
        case 'erro_resetar_senha':
          titulo = 'Erro ao resetar';
          mensagem = 'Erro ao resetar a senha do usuário. Tente novamente.';
          break;
      }
      
      Swal.fire({
        icon: 'error',
        title: titulo,
        text: mensagem,
        confirmButtonText: 'OK',
        background: tema ? '#E6E4E4' : '#262626',
        color: tema ? '#121212' : '#ffffff',
        confirmButtonColor: '#DC2626'
      }).then(() => {
        const url = new URL(window.location.href);
        url.searchParams.delete('erro');
        window.history.replaceState({}, document.title, url.toString());
      });
    });
  </script>
<?php endif; ?>

<script src="../assets/css/js/admin.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../assets/css/js/script.js"></script>
</body>
</html>
