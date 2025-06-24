<?php
session_start();
require_once '../includes/db.php';

function exibirAlerta($icone, $titulo, $mensagem, $redirecionar = '../pages/tickets.php') {
  echo "<!DOCTYPE html>
  <html lang='pt-br'>
  <head>
    <meta charset='UTF-8'>
    <title>Resposta</title>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <style>
      .swal2-popup {
        border-radius: 15px !important;
        padding: 2em !important;
        font-family: 'Poppins', sans-serif !important;
      }

      .swal2-title {
        font-size: 1.8rem !important;
        font-weight: bold !important;
      }

      .swal2-html-container {
        font-size: 1rem !important;
      }

      .swal2-confirm {
        padding: 0.6em 1.5em !important;
        font-size: 1rem !important;
        font-weight: 600 !important;
        border-radius: 8px !important;
      }
    </style>
  </head>
  <body>
    <script>
      const temaClaro = localStorage.getItem('tema') === 'claro';      Swal.fire({
        icon: '$icone',
        title: '$titulo',
        text: '$mensagem',
        confirmButtonText: 'OK',
        background: temaClaro ? '#E6E4E4' : '#262626',
        color: temaClaro ? '#121212' : '#ffffff',
        confirmButtonColor: '#1D4ED8'
      }).then(() => {
        window.location.href = '$redirecionar';
      });
    </script>
  </body>
  </html>";
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['ticket_id'] ?? null;
  $resposta = trim($_POST['resposta'] ?? '');

  if ($id && $resposta) {
    $emailAdm = $_SESSION['email'] ?? 'admin@email.com';
    $dataResposta = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("UPDATE suporte SET resposta = ?, email_adm = ?, data_resposta = ? WHERE id = ?");
    $stmt->bind_param("sssi", $resposta, $emailAdm, $dataResposta, $id);

    if ($stmt->execute()) {
      exibirAlerta('success', 'Respondido com sucesso!', 'A resposta foi salva com êxito.');
    } else {
      exibirAlerta('error', 'Erro ao responder', 'Erro técnico: " . $stmt->error . "');
    }

    $stmt->close();
  } else {
    exibirAlerta('warning', 'Campos obrigatórios', 'Preencha todos os campos obrigatórios.');
  }
} else {
  header("Location: ../pages/tickets.php");
  exit();
}
?>
