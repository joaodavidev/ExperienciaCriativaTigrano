<?php
session_start();
include '../includes/db.php';
include '../includes/verificar_login.php';
require '../vendor/autoload.php';

use Stripe\Stripe;
use Stripe\Checkout\Session;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$usuario_email = $_SESSION['usuario']['email'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['finalizar_compra'])) {
    // Configura a chave secreta do Stripe para autenticação
    \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

    // Array que vai armazenar os itens para o Stripe
    $line_items = [];

        // Prepara cada item do carrinho para o formato que o Stripe aceita
        foreach ($_SESSION['carrinho'] as $produto) {
            $line_items[] = [
                'price_data' => [
                    'currency' => 'brl',
                    'product_data' => [
                        'name' => $produto['nome'],
                        'description' => $produto['descricao'],
                    ],
                    'unit_amount' => (int)($produto['preco'] * 100), // Converte o preço para centavos como o Stripe requer
                ],
                'quantity' => 1,
            ];
        }

        // Cria uma nova sessão de checkout no Stripe com os produtos
        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => $line_items,
            'mode' => 'payment',
            'success_url' => 'http://localhost/ExperienciaCriativaTigrano/pages/sucessoCompra.php',
            'cancel_url' => 'http://localhost/ExperienciaCriativaTigrano/pages/falhaCompra.php',
        ]);

        // Redireciona o usuário para a página de checkout do Stripe
        header("Location: " . $checkout_session->url);
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