<?php
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST['action'] === 'buscar') {
  $id = $_POST['id'];
  $sql = "SELECT * FROM produtos WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $produto = $result->fetch_assoc();

  echo json_encode($produto);
}
?>
