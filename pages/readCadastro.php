<?php
require_once '../includes/db.php';
session_start(); 


if (!isset($_SESSION['usuario'])) {
    header("Location: login.php"); 
    exit();
}

$sql = "SELECT email FROM usuarios";
$result = $conn->query($sql);
?>
