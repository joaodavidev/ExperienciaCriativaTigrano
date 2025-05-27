<?php
$host = 'localhost';
$db = 'ecommerce';
$user = 'root';
$pass = '';
$port = '3307';

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
  die("Conexão falhou: " . $conn->connect_error);
}
?>