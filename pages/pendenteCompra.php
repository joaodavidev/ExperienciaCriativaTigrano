<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pagamento Pendente</title>
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
      background-color: #fbc02d !important;
      color: black !important;
      border-radius: 8px;
    }
  </style>
</head>
<body>

<script>
  Swal.fire({
    icon: 'warning',
    title: 'Pagamento Pendente!',
    text: 'Seu pagamento está em análise. Você será notificado quando for confirmado.',
    confirmButtonText: 'Voltar à loja',
    customClass: {
      popup: 'swal2-custom-popup',
      title: 'swal2-custom-title',
      confirmButton: 'swal2-custom-button'
    }
  }).then(() => {
    window.location.href = 'index.php';
  });
</script>

</body>
</html>
