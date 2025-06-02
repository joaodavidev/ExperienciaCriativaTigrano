<?php
session_start();
include '../includes/db.php';
include '../includes/verificar_login.php';
require '../vendor/autoload.php';

use Stripe\Stripe;
use Stripe\Checkout\Session;

// Carregar variáveis do .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$usuario_email = $_SESSION['usuario']['email'];

// ✅ Finalizar compra
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['finalizar_compra'])) {
    Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

    // Buscar produtos do carrinho
    $sql = "SELECT p.id, p.nome, p.preco, p.descricao 
            FROM carrinho c
            JOIN produtos p ON c.produto_id = p.id
            WHERE c.usuario_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario_email);
    $stmt->execute();
    $result = $stmt->get_result();

    $line_items = [];
    $produtosParaCompra = [];

    while ($produto = $result->fetch_assoc()) {
        $line_items[] = [
            'price_data' => [
                'currency' => 'brl',
                'product_data' => [
                    'name' => $produto['nome'],
                    'description' => $produto['descricao'],
                ],
                'unit_amount' => (int)($produto['preco'] * 100), // preço em centavos
            ],
            'quantity' => 1,
        ];

        // Salvar os produtos na sessão para processar na página sucessoCompra.php
        $produtosParaCompra[] = [
            'id' => $produto['id'],
            'nome' => $produto['nome'],
            'preco' => $produto['preco'],
            'descricao' => $produto['descricao']
        ];
    }

    $stmt->close();

    if (count($line_items) === 0) {
        // Carrinho vazio, não pode gerar checkout
        header("Location: carrinho.php?vazio=1");
        exit();
    }

    // ✅ Salva na sessão para sucessoCompra.php processar a venda
    $_SESSION['carrinho'] = $produtosParaCompra;

    // Cria sessão de checkout Stripe
    $checkout_session = Session::create([
        'line_items' => $line_items,
        'mode' => 'payment',
        'success_url' => 'http://localhost/ExperienciaCriativaTigrano/pages/sucessoCompra.php',
        'cancel_url' => 'http://localhost/ExperienciaCriativaTigrano/pages/falhaCompra.php',
    ]);

    // Redireciona para Stripe Checkout
    header("Location: " . $checkout_session->url);
    exit();
}

// ✅ Adicionar produto ao carrinho
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $produto_id = $_POST['id'];

    $check = $conn->prepare("SELECT * FROM carrinho WHERE usuario_email = ? AND produto_id = ?");
    $check->bind_param("si", $usuario_email, $produto_id);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows === 0) {
        $sql = "INSERT INTO carrinho (usuario_email, produto_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $usuario_email, $produto_id);
        $stmt->execute();
        $stmt->close();
    }

    $check->close();
    header("Location: marketplace.php?adicionado=1");
    exit();
}

// ✅ Remover produto do carrinho
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['remover_id'])) {
    $produto_id = $_POST['remover_id'];

    $delete = $conn->prepare("DELETE FROM carrinho WHERE usuario_email = ? AND produto_id = ? LIMIT 1");
    $delete->bind_param("si", $usuario_email, $produto_id);
    $delete->execute();
    $delete->close();

    header("Location: carrinho.php?removido=1");
    exit();
}

// ✅ Carregar produtos do carrinho para exibir na página
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
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Carrinho</title>
  <link rel="stylesheet" href="../assets/css/carrinho.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<nav class="sidebar active">
  <div class="logo-menu">
    <h2 class="logo">Tigrano</h2>
    <i class='bx bx-menu toggle-btn'></i>
  </div>
  <ul class="lista">
    <li class="lista-item">
      <a href="../pages/marketplace.php">
        <i class='bx bxs-shopping-bag-alt'></i>
        <span class="nome-link" style="--i:1;">Marketplace</span>
      </a>
    </li>
    <li class="lista-item">
      <a href="../pages/dashboard.php">
        <i class='bx bxs-dashboard'></i>
        <span class="nome-link" style="--i:2;">Dashboard</span>
      </a>
    </li>
    <li class="lista-item">
      <a href="../pages/produto.php">
        <i class='bx bxs-purchase-tag'></i>
        <span class="nome-link" style="--i:3;">Produtos</span>
      </a>
    </li>
    <li class="lista-item">
      <a href="../pages/compras.php">
        <i class='bx bx-shopping-bag'></i>
        <span class="nome-link" style="--i:4;">Compras</span>
      </a>
    </li>
    <li class="espacador"></li>
    <li class="lista-item">
      <a href="#" class="btn-toggle-tema">
        <i class='bx bx-moon'></i>
        <span class="nome-link" style="--i:5;">Claro/Escuro</span>
      </a>
    </li>
    <li class="lista-item">
      <a href="../pages/suporteUsuario.php" class="btn-toggle-tema">
        <i class='bx bx-info-circle'></i>
        <span class="nome-link" style="--i:6;">Ajuda</span>
      </a>
    </li>
    <li class="lista-item">
      <a href="../pages/configuracoes.php">
        <i class='bx bx-cog'></i>
        <span class="nome-link" style="--i:7;">Configurações</span>
      </a>
    </li>
    <li class="lista-item">
      <a href="../pages/perfil.php">
        <i class='bx bx-user'></i>
        <span class="nome-link" style="--i:8;">Perfil</span>
      </a>
    </li>
  </ul>
</nav>  <div class="main-content">
    <div class="carrinho-header">
      <h1>Seu Carrinho</h1>
    </div>

    <div class="carrinho-container">      <?php if (count($produtosCarrinho) > 0): ?>        <div class="carrinho-table">
          <div class="carrinho-table-header">
            <div class="col-produto">PRODUTO</div>
            <div class="col-preco">PREÇO</div>
            <div class="col-acoes"></div>
          </div>
          
          <div class="carrinho-table-body">
            <?php 
              $total = 0;
              foreach ($produtosCarrinho as $produto): 
                $preco_item = $produto['preco'];
                $total += $preco_item;
            ?>
              <div class="carrinho-item" id="produto-<?= $produto['id'] ?>">
                <div class="col-produto">
                  <div class="produto-info">
                    <div class="produto-detalhes">
                      <strong><?= htmlspecialchars($produto['nome']) ?></strong>
                      <p class="produto-descricao"><?= htmlspecialchars($produto['descricao']) ?></p>
                    </div>
                  </div>
                </div>
                <div class="col-preco">R$ <?= number_format($preco_item, 2, ',', '.') ?></div>
                <div class="col-acoes">
                  <form action="carrinho.php" method="post">
                    <input type="hidden" name="remover_id" value="<?= $produto['id'] ?>">
                    <button type="submit" class="btn-remover" title="Remover item">×</button>
                  </form>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>        <div class="carrinho-resumo">
          <h2>Total do Carrinho</h2>
          <div class="resumo-produtos">
            <?php foreach ($produtosCarrinho as $produto): ?>
              <div class="resumo-produto-item">
                <span>1x <?= htmlspecialchars($produto['nome']) ?></span>
                <span>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></span>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="resumo-item total">
            <span>Total:</span>
            <span>R$ <?= number_format($total, 2, ',', '.') ?></span>
          </div>
           <form action="carrinho.php" method="POST" class="form-pagamento">
            <button type="submit" name="finalizar_compra" class="btn-comprar">Finalizar Pagamento</button>
          </form>
        </div>
        
      <?php else: ?>
        <div class="carrinho-vazio">
          <i class="bx bx-cart-alt"></i>
          <h2>Seu carrinho está vazio</h2>
          <p>Adicione produtos para continuar suas compras</p>
          <a href="marketplace.php" class="btn-continuar-comprando">Continuar Comprando</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
<?php if (isset($_GET['removido']) && $_GET['removido'] == 1): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  const temaClaro = localStorage.getItem("tema") === "claro";

  Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'success',
    title: 'Produto removido do carrinho!',
    showConfirmButton: false,
    timer: 2000,
    background: temaClaro ? "#E6E4E4" : "#262626",
    color: temaClaro ? "#121212" : "#ffffff",
    confirmButtonColor: "#1D4ED8"
  });
  const url = new URL(window.location.href);
  url.searchParams.delete("removido");
  window.history.replaceState({}, document.title, url.toString());
</script>
<?php endif; ?>
  <script src="../assets/css/js/script.js"></script>
  <script src="../assets/css/js/carrinho.js"></script>
</body>
</html>
