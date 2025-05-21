<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sucesso na solicitação</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .swal2-custom-popup {
      border-radius: 15px;
      padding: 2em;
      background-color: #1e1e1e;
      color: white;
    }

    .swal2-custom-title {
      font-size: 1.8rem;
      font-weight: bold;
      font-family: 'Poppins', sans-serif;
    }

    .swal2-html-container {
      font-family: 'Poppins', sans-serif;
    }

    .swal2-custom-button {
      padding: 0.6em 1.5em;
      font-size: 1rem;
      font-weight: 600;
      background-color: #4CAF50 !important;
      color: white !important;
      border-radius: 8px;
    }
  </style>
</head>
<body>

<?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1): ?>
  <script>
    Swal.fire({
      icon: 'success',
      title: 'Suporte enviado!',
      text: 'Sua solicitação foi registrada com sucesso.',
      confirmButtonText: 'Voltar',
      customClass: {
        popup: 'swal2-custom-popup',
        title: 'swal2-custom-title',
        confirmButton: 'swal2-custom-button'
      }
    }).then(() => {
      window.location.href = 'suporteUsuario.php';
    });
  </script>
<?php endif; ?>

</body>
</html>
