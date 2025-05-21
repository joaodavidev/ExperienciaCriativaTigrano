<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['usuario']['email'])) {
    die("Usuário não autenticado.");
}

$usuario_email = $_SESSION['usuario']['email'];

// Buscar produtos no carrinho
$sql = "SELECT p.id, p.nome, p.preco, p.descricao 
        FROM carrinho c
        JOIN produtos p ON c.produto_id = p.id
        WHERE c.usuario_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario_email);
$stmt->execute();
$result = $stmt->get_result();

$produtos = [];
$total = 0;

if ($result->num_rows > 0) {
    $produtos = $result->fetch_all(MYSQLI_ASSOC);
} else {
    header("Location: carrinho.php");
    exit();
}

$stmt->close();

// Montar itens
$items = [];
foreach ($produtos as $produto) {
    $items[] = [
        'title' => $produto['nome'],
        'quantity' => 1,
        'currency_id' => 'BRL',
        'unit_price' => (float)$produto['preco']
    ];
}

// Criar preferência no Mercado Pago via API
$preference = [
    'items' => $items,
    'back_urls' => [
        'success' => 'http://localhost/ExperienciaCriativaTigrano/pages/pagamento_sucesso.php',
        'failure' => 'http://localhost/ExperienciaCriativaTigrano/pages/pagamento_falha.php',
        'pending' => 'http://localhost/ExperienciaCriativaTigrano/pages/pagamento_pendente.php'
    ],
    'auto_return' => 'approved'
];

$access_token = 'APP_USR-5623965132337086-052114-5f63de0c253b7957d897e81968cb799a-2449785661';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.mercadopago.com/checkout/preferences');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($preference));
curl_setopt($ch, CURLOPT_POST, 1);

$headers = [];
$headers[] = 'Content-Type: application/json';
$headers[] = 'Authorization: Bearer ' . $access_token;
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Erro:' . curl_error($ch);
    exit;
}
curl_close($ch);

$response = json_decode($result, true);
$init_point = $response['init_point'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Pagamento</title>
  <link rel="stylesheet" href="../assets/css/pagamento.css" />
</head>
<body>
  <header>
    <h1>TIGRANO</h1>
    <nav>
      <a href="../pages/marketplace.php"><button>Marketplace</button></a>
      <a href="../pages/carrinho.php"><button>Carrinho</button></a>
    </nav>
  </header>

  <div id="resumo-compra">
    <h2>Resumo da Compra</h2>

    <?php foreach ($produtos as $produto): ?>
      <div class="produto">
        <strong><?php echo htmlspecialchars($produto['nome']); ?></strong><br>
        R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?><br>
        <p><?php echo htmlspecialchars($produto['descricao']); ?></p>
      </div>
    <?php endforeach; ?>

    <div class="total">
      <strong>Total: R$ <?php echo number_format($total, 2, ',', '.'); ?></strong>
    </div>

    <a href="<?php echo htmlspecialchars($init_point); ?>" target="_blank">
      <button class="btn-comprar">Pagar com Mercado Pago</button>
    </a>
  </div>
</body>
</html>
