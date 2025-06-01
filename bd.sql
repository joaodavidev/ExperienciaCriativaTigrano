CREATE DATABASE tigrano;
USE tigrano;

CREATE TABLE usuarios (
  email VARCHAR(255) PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  senha VARCHAR(255) NOT NULL,
  sexo ENUM('masculino', 'feminino', 'outro') NOT NULL,
  idade INT NOT NULL,
  cpf CHAR(11) UNIQUE
);

CREATE TABLE adm (
  email VARCHAR(255) UNIQUE PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  senha VARCHAR(255) NOT NULL
);

CREATE TABLE suporte (
  id INT PRIMARY KEY AUTO_INCREMENT,
  email_adm VARCHAR(255),
  email_usuario VARCHAR(255),
  assunto VARCHAR(255),
  descricao TEXT,
  data_envio DATETIME,
  resposta TEXT,
  data_resposta DATE,
  FOREIGN KEY (email_adm) REFERENCES adm (email),
  FOREIGN KEY (email_usuario) REFERENCES usuarios (email) ON DELETE CASCADE
);

CREATE TABLE produtos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  categoria VARCHAR(255) NOT NULL,
  preco DECIMAL(10,2) NOT NULL,
  descricao TEXT NOT NULL,
  status VARCHAR(50) DEFAULT 'Ativo',
  vendedor_email VARCHAR(255),
  arquivo_produto VARCHAR(500) NULL,
  FOREIGN KEY (vendedor_email) REFERENCES usuarios(email) ON DELETE CASCADE
);

CREATE TABLE pedidos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  comprador_email VARCHAR(255),
  data_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
  status ENUM('pendente', 'pago', 'enviado', 'cancelado') DEFAULT 'pendente',
  FOREIGN KEY (comprador_email) REFERENCES usuarios (email) ON DELETE CASCADE
);

CREATE TABLE produtos_pedido (
  pedido_id INT,
  produto_id INT,
  PRIMARY KEY (pedido_id, produto_id),
  FOREIGN KEY (pedido_id) REFERENCES pedidos (id),
  FOREIGN KEY (produto_id) REFERENCES produtos (id)
);

CREATE TABLE carrinho (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario_email VARCHAR(255),
  produto_id INT,
  FOREIGN KEY (usuario_email) REFERENCES usuarios (email) ON DELETE CASCADE,
  FOREIGN KEY (produto_id) REFERENCES produtos (id)
);

CREATE TABLE avaliacao (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario_email VARCHAR(255),
  estrelas CHAR(1),
  comentario TEXT,
  produto_id INT,
  FOREIGN KEY (usuario_email) REFERENCES usuarios (email) ON DELETE CASCADE,
  FOREIGN KEY (produto_id) REFERENCES produtos (id)
);

CREATE TABLE vendas (
  id INT PRIMARY KEY AUTO_INCREMENT,
  fornecedor_email VARCHAR(255),
  comprador_email VARCHAR(255),
  produto_id INT,
  quantidade_vendas INT,
  data_vendas DATE,
  FOREIGN KEY (fornecedor_email) REFERENCES usuarios (email) ON DELETE CASCADE,
  FOREIGN KEY (comprador_email) REFERENCES usuarios (email) ON DELETE CASCADE,
  FOREIGN KEY (produto_id) REFERENCES produtos (id)
);

CREATE TABLE historico_compras (
  id INT PRIMARY KEY AUTO_INCREMENT,
  comprador_email VARCHAR(255),
  produto_id INT,
  data_compras DATE,
  FOREIGN KEY (comprador_email) REFERENCES usuarios (email) ON DELETE CASCADE,
  FOREIGN KEY (produto_id) REFERENCES produtos (id)
);

CREATE TABLE produtos_historico_compras (
  produtos_id INT,
  historico_compras_id INT,
  PRIMARY KEY (produtos_id, historico_compras_id),
  FOREIGN KEY (produtos_id) REFERENCES produtos (id),
  FOREIGN KEY (historico_compras_id) REFERENCES historico_compras (id)
);