<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cadastro de Produtos</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <header>
    <h1>TIGRANO</h1>
    <nav>
      <button>Home</button>
      <button>Produtos</button>
      <button>Carrinho</button>
      <button>Contato</button>
    </nav>
  </header>
  <h1>Cadastro de Produtos</h1>
  <form action="create.php" method="POST">
    <input type="text" name="nome" placeholder="Nome do Produto" required />
    <input type="number" name="preco" placeholder="Preço" step="0.01" required />
    <textarea name="descricao" placeholder="Descrição do Produto"></textarea>
    <button type="submit">Cadastrar</button>
  </form>

  
  <div id="produtos">
    <h2>Produtos Cadastrados</h2>
    <?php include 'read.php'; ?>
  </div>  
</body>
</html>