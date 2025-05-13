CREATE DATABASE ecommerce;

USE ecommerce;

-- Tabela de usuários (compradores e/ou fornecedores)
CREATE TABLE usuarios (
  email VARCHAR(255) PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  senha VARCHAR(255) NOT NULL,
  sexo ENUM('masculino', 'feminino', 'outro') NOT NULL,
  idade INT NOT NULL,
  cpf VARCHAR(14) UNIQUE
);

-- Tabela de administradores
CREATE TABLE adm (
  id INT PRIMARY KEY AUTO_INCREMENT,
  suporte_id INT
);

-- Tabela de suporte
CREATE TABLE suporte (
  id INT PRIMARY KEY AUTO_INCREMENT,
  adm_id INT,
  email_usuario VARCHAR(255),
  assunto VARCHAR(255),
  descricao TEXT,
  data_envio DATETIME,
  resposta TEXT,
  data_resposta DATE,
  FOREIGN KEY (adm_id) REFERENCES adm (id),
  FOREIGN KEY (email_usuario) REFERENCES usuarios (email)
);

-- Produtos agora têm apenas informações estáticas (sem estoque)
CREATE TABLE produtos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  fornecedor_email VARCHAR(255),
  nome VARCHAR(255),
  preco DECIMAL(10,2),
  preco_tigrano_coins INT,
  descricao TEXT,
  FOREIGN KEY (fornecedor_email) REFERENCES usuarios (email)
);

-- Tabela de pedidos (ordem de compra)
CREATE TABLE pedidos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  comprador_email VARCHAR(255),
  data_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
  status ENUM('pendente', 'pago', 'enviado', 'cancelado') DEFAULT 'pendente',
  FOREIGN KEY (comprador_email) REFERENCES usuarios (email)
);

-- Relacionamento entre produtos e pedidos (com quantidade comprada)
CREATE TABLE produtos_pedido (
  pedido_id INT,
  produto_id INT,
  quantidade INT NOT NULL,
  PRIMARY KEY (pedido_id, produto_id),
  FOREIGN KEY (pedido_id) REFERENCES pedidos (id),
  FOREIGN KEY (produto_id) REFERENCES produtos (id)
);

-- Pagamento via Pix vinculado ao pedido
CREATE TABLE pix (
  id INT PRIMARY KEY AUTO_INCREMENT,
  pedido_id INT,
  valor DECIMAL(10,2) NOT NULL,
  chave_pix VARCHAR(255) NOT NULL,
  tipo_chave ENUM('cpf', 'email', 'telefone', 'aleatoria') NOT NULL,
  status ENUM('pendente', 'confirmado', 'cancelado') DEFAULT 'pendente',
  data_pagamento DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (pedido_id) REFERENCES pedidos (id)
);

-- Carrinho de compras
CREATE TABLE carrinho (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario_email VARCHAR(255),
  produto_id INT,
  FOREIGN KEY (usuario_email) REFERENCES usuarios (email),
  FOREIGN KEY (produto_id) REFERENCES produtos (id)
);

-- Carteira de Tigrano Coins
CREATE TABLE tigrano_coins (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario_email VARCHAR(255),
  quantidade INT,
  FOREIGN KEY (usuario_email) REFERENCES usuarios (email)
);

-- Avaliações de produtos
CREATE TABLE avaliacao (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario_email VARCHAR(255),
  estrelas CHAR(1),
  comentario TEXT,
  produto_id INT,
  FOREIGN KEY (usuario_email) REFERENCES usuarios (email),
  FOREIGN KEY (produto_id) REFERENCES produtos (id)
);

-- Registro de vendas
CREATE TABLE vendas (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario_email VARCHAR(255),
  produto_id INT,
  quantidade_vendas INT,
  data_vendas DATE,
  FOREIGN KEY (usuario_email) REFERENCES usuarios (email),
  FOREIGN KEY (produto_id) REFERENCES produtos (id)
);

-- Histórico de compras
CREATE TABLE historico_compras (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario_email VARCHAR(255),
  produto_id INT,
  data_compras DATE,
  FOREIGN KEY (usuario_email) REFERENCES usuarios (email),
  FOREIGN KEY (produto_id) REFERENCES produtos (id)
);

-- Ligação entre produtos e histórico de compras
CREATE TABLE produtos_historico_compras (
  produtos_id INT,
  historico_compras_id INT,
  PRIMARY KEY (produtos_id, historico_compras_id),
  FOREIGN KEY (produtos_id) REFERENCES produtos (id),
  FOREIGN KEY (historico_compras_id) REFERENCES historico_compras (id)
);

-- Cartões de crédito
CREATE TABLE cartao_credito_usuario (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario_email VARCHAR(255),
  nome_titular TEXT,
  numero_cartao VARCHAR(20),
  cvv VARCHAR(4),
  parcelamento INT,
  FOREIGN KEY (usuario_email) REFERENCES usuarios (email)
);

-- Cartões de débito
CREATE TABLE cartao_debito_usuario (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario_email VARCHAR(255),
  nome_titular TEXT,
  numero_cartao VARCHAR(20),
  cvv VARCHAR(4),
  FOREIGN KEY (usuario_email) REFERENCES usuarios (email)
);

-- Tipos de pagamento (relacionamento opcional entre cartões)
CREATE TABLE tipo_pagamento (
  id_cartao_credito INT,
  id_cartao_debito INT,
  FOREIGN KEY (id_cartao_credito) REFERENCES cartao_credito_usuario (id),
  FOREIGN KEY (id_cartao_debito) REFERENCES cartao_debito_usuario (id)
);
