<?php
session_start();
require_once '../includes/db.php';

$totalUsuarios = $conn->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()['total'];
$totalTickets = $conn->query("SELECT COUNT(*) AS total FROM suporte")->fetch_assoc()['total'];
$ticketsRespondidos = $conn->query("SELECT COUNT(*) AS total FROM suporte WHERE resposta IS NOT NULL AND TRIM(resposta) <> ''")->fetch_assoc()['total'];
$ticketsPendentes = $conn->query("SELECT COUNT(*) AS total FROM suporte WHERE resposta IS NULL OR TRIM(resposta) = ''")->fetch_assoc()['total'];
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Painel do Administrador</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<nav class="sidebar active">
  <div class="logo-menu">
    <h2 class="logo">Tigrano</h2>
    <i class='bx bx-menu toggle-btn'></i>
  </div>
  <ul class="lista">
     <li class="lista-item"><a href="   admin.php"><i class='bx bx-pie-chart-alt-2' ></i><span class="nome-link" style="--i:1;">Admin</span></a></li>
    <li class="lista-item"><a href="usuarios.php"><i class='bx bxs-user-detail'></i><span class="nome-link" style="--i:2;">Usuários</span></a></li>
    <li class="lista-item"><a href="tickets.php"><i class='bx bx-support'></i><span class="nome-link" style="--i:3;">Tickets</span></a></li>
    <li class="espacador"></li>
    <li class="lista-item"><a href="#" class="btn-toggle-tema"><i class='bx bx-moon'></i><span class="nome-link" style="--i:5;">Claro/Escuro</span></a></li>
    <li class="lista-item"><a href="../includes/logout.php"><i class='bx bx-log-out'></i><span class="nome-link" style="--i:6;">Sair</span></a></li>
  </ul>
</nav>
<main class="main-content">
  <h1>Bem-vindo(a) ao Painel do Administrador</h1>

  <div class="cards">
    <div class="card">
      <h3>Usuários cadastrados</h3>
      <p><?= $totalUsuarios ?></p>
    </div>
    <div class="card">
      <h3>Tickets recebidos</h3>
      <p><?= $totalTickets ?></p>
    </div>
    <div class="card">
      <h3>Tickets respondidos</h3>
      <p><?= $ticketsRespondidos ?></p>
    </div>
    <div class="card">
      <h3>Tickets pendentes</h3>
      <p><?= $ticketsPendentes ?></p>
    </div>
  </div>
</main>


<script src="../assets/css/js/script.js"></script>
</body>
</html>
