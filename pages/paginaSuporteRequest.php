<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Chamar Suporte</title>
  <link rel="stylesheet" href="../assets/css/createSuporteRequest.css" />
</head>
<body>
  <div class="container">
    <h1>Chamar Suporte</h1>
    <form action="../includes/createSuporteRequest.php" method="POST"> 
      <label for="usuario">Usuário:</label>
      <input type="text" name="usuario" id="usuario" placeholder="Digite o nome de usuário" required>

      <label for="email">E-mail:</label>
      <input type="email" name="email" id="email" placeholder="Digite seu e-mail" required>

      <label for="assunto">Assunto:</label>
      <input type="text" name="assunto" id="assunto" placeholder="Digite o assunto" required>

      <label for="descricao">Descreva o problema:</label>
      <textarea name="descricao" id="descricao" placeholder="Detalhe o problema" required></textarea>

      <button type="submit">Enviar</button>
    </form>
  </div>
</body>
</html>
