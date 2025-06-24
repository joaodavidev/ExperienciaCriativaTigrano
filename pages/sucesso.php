<?php
session_start();
include '../includes/verificar_login.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sucesso na solicitação</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .swal2-popup {
      border-radius: 15px !important;
      padding: 2em !important;
      font-family: 'Poppins', sans-serif;
    }

    .swal2-title {
      font-size: 1.8rem !important;
      font-weight: bold !important;
      font-family: 'Poppins', sans-serif !important;
    }

    .swal2-html-container {
      font-family: 'Poppins', sans-serif !important;
      font-size: 1rem !important;
    }

    .swal2-confirm {
      padding: 0.6em 1.5em !important;
      font-size: 1rem !important;
      font-weight: 600 !important;
      border-radius: 8px !important;
      font-family: 'Poppins', sans-serif !important;
    }
  </style>
</head>
<body>

<?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1): ?>  <script>
    const temaClaro = localStorage.getItem("tema") === "claro";

    Swal.fire({
      icon: 'success',
      title: 'Solicitação enviada!',
      text: 'Sua mensagem foi registrada com sucesso.',
      confirmButtonText: 'OK',
      background: temaClaro ? '#E6E4E4' : '#262626',
      color: temaClaro ? '#121212' : '#ffffff',
      confirmButtonColor: '#1D4ED8'
    }).then(() => {
      window.location.href = 'suporteUsuario.php';
    });
  </script>
<?php endif; ?>

</body>
</html>
