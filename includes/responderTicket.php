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
  </head>
  <body>
    <script>
      Swal.fire({
        icon: '$icone',
        title: '$titulo',
        text: '$mensagem',
        confirmButtonText: 'OK'
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
      exibirAlerta('error', 'Erro', 'Erro ao salvar a resposta: " . $stmt->error . "');
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
