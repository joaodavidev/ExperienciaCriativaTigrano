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
    try {
        echo "Entrou no POST<br>";

        SDK::setAccessToken('APP_USR-5623965132337086-052114-5f63de0c253b7957d897e81968cb799a-2449785661'); 
        echo "Access token setado<br>";

        $preference = new Preference();
        $items = [];

        foreach ($_SESSION['carrinho'] as $produto) {
            $item = new Item();
            $item->title = $produto['nome'];
            $item->quantity = 1;
            $item->unit_price = (float) $produto['preco'];
            $items[] = $item;
            echo "Produto adicionado: " . $item->title . "<br>";
        }

        $preference->items = $items;

        $preference->back_urls = [
            "success" => "http://localhost/ExperienciaCriativaTigrano/pages/sucessoCompra.php",
            "failure" => "http://localhost/ExperienciaCriativaTigrano/pages/falhaCompra.php",
            "pending" => "http://localhost/ExperienciaCriativaTigrano/pages/pendenteCompra.php"
        ];
        //$preference->auto_return = "approved";

        $preference->save();

        // Verificar se a preferência possui erros
        if (isset($preference->error)) {
            echo "Erro na preferência: ";
            print_r($preference->error);
            exit;
        }

        echo "Preferência salva<br>";
        echo "URL de pagamento: " . $preference->init_point . "<br>";

    } catch (Exception $e) {
        echo "Exceção capturada: " . $e->getMessage();
        exit;
    }

    exit; // para não redirecionar e ver as mensagens
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
