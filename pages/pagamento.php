<?php

session_start();
include '../includes/db.php';
require '../vendor/autoload.php';

//integracao com API do mercado pago qnd o cara clica em finalizar compra
use MercadoPago\SDK;
use MercadoPago\Preference;
use MercadoPago\Item;


if (!isset($_SESSION['usuario']['email'])) {
    header("location: login.php");
    exit;
}

$usuario_email = $_SESSION['usuario']['email'];

// Buscar itens do carrinho no banco e colocar na sessão
$produtosCarrinho = [];
$sql = "SELECT p.id, p.nome, p.preco, p.descricao 
        FROM carrinho c
        JOIN produtos p ON c.produto_id = p.id
        WHERE c.usuario_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $produtosCarrinho = $result->fetch_all(MYSQLI_ASSOC);
    $_SESSION['carrinho'] = $produtosCarrinho;
} else {
    $_SESSION['carrinho'] = [];
}

$stmt->close();

if (empty($_SESSION['carrinho'])) {
    header("Location: carrinho.php");
    exit();
}

$total = 0;
foreach ($_SESSION['carrinho'] as $item) {
    $total += $item['preco'];
}

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['finalizar_compra'])) {

    //diz pra SDK qual conta do mercado pago vai ser usada para realizar o pagamento
    SDK::setAccessToken('APP_USR-5623965132337086-052114-5f63de0c253b7957d897e81968cb799a-2449785661'); 

    //cria uma preferencia que é tipo um "pedido" que vai ser pago, nela voce configura produtos, precos, qtd..
    $preference = new Preference();

    $items = [];

    //puxa os produtos do carrinho para a preferencia
    foreach ($_SESSION['carrinho'] as $produto) {
        $item = new Item();
        $item->title = $produto['nome'];
        $item->quantity = 1;
        $item->unit_price = (float) $produto['preco'];
        $items[] = $item;
    }

    // cada item da compra é add aq para o mercado pago mostrar no checkout
    $preference->items = $items;

    //as urls que o marcado pago usa pra redirecionar o cara dps do pagamento dependendo do resultado
    $preference->back_urls = [
    "success" => "http://localhost/ExperienciaCriativaTigrano/pages/sucessoCompra.php",
    "failure" => "http://localhost/ExperienciaCriativaTigrano/pages/falhaCompra.php",
    "pending" => "http://localhost/ExperienciaCriativaTigrano/pages/pendenteCompra.php"
    ];
    
    $preference->auto_return = "approved";//vai para a url de success

    $preference->save();

    // vai para o checkout pro, onde o cara de fato realizar o pagamento
    header("Location: " . $preference->init_point);
    exit();

}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Pagamento</title>
  <link rel="stylesheet" href="../assets/css/pagamento.css">
</head>
<body>
  <header>
    <h1>TIGRANO</h1>
    <nav>
      <a href="../pages/cadastrarProdutos.php"><button>Cadastrar</button></a>
      <a href="../pages/carrinho.php"><button>Carrinho</button></a>
    </nav>
  </header>

  <div id="resumo-compra">
    <h2>Resumo da Compra</h2>
    <?php
    if (!empty($_SESSION['carrinho'])) {
        foreach ($_SESSION['carrinho'] as $index => $item) {
            echo "<div class='produto'>";
            echo "<strong>" . htmlspecialchars($item['nome']) . "</strong><br>";
            echo "R$ " . number_format($item['preco'], 2, ',', '.') . "<br>";
            echo "<p>" . htmlspecialchars($item['descricao']) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>Nenhum produto no carrinho.</p>";
    }
    ?>

    <div class="total">
      <strong>Total: R$ <?php echo number_format($total, 2, ',', '.'); ?></strong>
    </div>

    <form action="pagamento.php" method="POST" class="form-pagamento">
      <button type="submit" name="finalizar_compra" class="btn-comprar">Finalizar Pagamento</button>
    </form>
  </div>
</body>
</html>
