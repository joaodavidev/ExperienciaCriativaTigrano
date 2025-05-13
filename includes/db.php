<?php
$host = 'localhost';
$db = 'ecommerce';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Conexão falhou: " . $conn->connect_error);
}
?>