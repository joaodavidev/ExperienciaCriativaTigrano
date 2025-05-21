CREATE DATABASE ecommerce;
USE ecommerce;

CREATE TABLE usuarios (
  email VARCHAR(255) PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  senha VARCHAR(255) NOT NULL,
  sexo ENUM('masculino', 'feminino', 'outro') NOT NULL,
  idade INT NOT NULL,
  cpf CHAR(11) UNIQUE
);
SELECT * FROM usuarios;
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
  FOREIGN KEY (email_usuario) REFERENCES usuarios (email)
);

CREATE TABLE produtos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  categoria VARCHAR(255) NOT NULL,
  preco DECIMAL(10,2) NOT NULL,
  descricao TEXT NOT NULL,
  status VARCHAR(50) DEFAULT 'Ativo',
  vendedor_email VARCHAR(255),
  FOREIGN KEY (vendedor_email) REFERENCES usuarios(email)
);

CREATE TABLE pedidos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  comprador_email VARCHAR(255),
  data_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
  status ENUM('pendente', 'pago', 'enviado', 'cancelado') DEFAULT 'pendente',
  FOREIGN KEY (comprador_email) REFERENCES usuarios (email)
);

CREATE TABLE produtos_pedido (
  pedido_id INT,
  produto_id INT,
  PRIMARY KEY (pedido_id, produto_id),
  FOREIGN KEY (pedido_id) REFERENCES pedidos (id),
  FOREIGN KEY (produto_id) REFERENCES produtos (id)
);

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

CREATE TABLE carrinho (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario_email VARCHAR(255),
  produto_id INT,
  FOREIGN KEY (usuario_email) REFERENCES usuarios (email),
  FOREIGN KEY (produto_id) REFERENCES produtos (id)
);

SELECT * FROM produtos;
SELECT * FROM carrinho;
CREATE TABLE avaliacao (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario_email VARCHAR(255),
  estrelas CHAR(1),
  comentario TEXT,
  produto_id INT,
  FOREIGN KEY (usuario_email) REFERENCES usuarios (email),
  FOREIGN KEY (produto_id) REFERENCES produtos (id)
);

CREATE TABLE vendas (
  id INT PRIMARY KEY AUTO_INCREMENT,
  fornecedor_email VARCHAR(255),
  produto_id INT,
  quantidade_vendas INT,
  data_vendas DATE,
  FOREIGN KEY (fornecedor_email) REFERENCES usuarios (email),
  FOREIGN KEY (produto_id) REFERENCES produtos (id)
);

CREATE TABLE historico_compras (
  id INT PRIMARY KEY AUTO_INCREMENT,
  comprador_email VARCHAR(255),
  produto_id INT,
  data_compras DATE,
  FOREIGN KEY (comprador_email) REFERENCES usuarios (email),
  FOREIGN KEY (produto_id) REFERENCES produtos (id)
);

CREATE TABLE produtos_historico_compras (
  produtos_id INT,
  historico_compras_id INT,
  PRIMARY KEY (produtos_id, historico_compras_id),
  FOREIGN KEY (produtos_id) REFERENCES produtos (id),
  FOREIGN KEY (historico_compras_id) REFERENCES historico_compras (id)
);

CREATE TABLE cartao_credito_usuario (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario_email VARCHAR(255),
  nome_titular TEXT,
  numero_cartao VARCHAR(20),
  cvv VARCHAR(4),
  parcelamento INT,
  FOREIGN KEY (usuario_email) REFERENCES usuarios (email)
);

CREATE TABLE cartao_debito_usuario (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario_email VARCHAR(255),
  nome_titular TEXT,
  numero_cartao VARCHAR(20),
  cvv VARCHAR(4),
  FOREIGN KEY (usuario_email) REFERENCES usuarios (email)
);

CREATE TABLE tipo_pagamento (
  id_cartao_credito INT,
  id_cartao_debito INT,
  FOREIGN KEY (id_cartao_credito) REFERENCES cartao_credito_usuario (id),
  FOREIGN KEY (id_cartao_debito) REFERENCES cartao_debito_usuario (id)
);

SHOW TABLES;
SELECT * FROM adm;
SELECT * FROM usuarios;

-- Carrinho
ALTER TABLE carrinho DROP FOREIGN KEY carrinho_ibfk_1;
ALTER TABLE carrinho ADD CONSTRAINT carrinho_ibfk_1
  FOREIGN KEY (usuario_email) REFERENCES usuarios(email) ON DELETE CASCADE;

-- Cartão Crédito
ALTER TABLE cartao_credito_usuario DROP FOREIGN KEY cartao_credito_usuario_ibfk_1;
ALTER TABLE cartao_credito_usuario ADD CONSTRAINT cartao_credito_usuario_ibfk_1
  FOREIGN KEY (usuario_email) REFERENCES usuarios(email) ON DELETE CASCADE;

-- Cartão Débito
ALTER TABLE cartao_debito_usuario DROP FOREIGN KEY cartao_debito_usuario_ibfk_1;
ALTER TABLE cartao_debito_usuario ADD CONSTRAINT cartao_debito_usuario_ibfk_1
  FOREIGN KEY (usuario_email) REFERENCES usuarios(email) ON DELETE CASCADE;

-- Avaliação
ALTER TABLE avaliacao DROP FOREIGN KEY avaliacao_ibfk_1;
ALTER TABLE avaliacao ADD CONSTRAINT avaliacao_ibfk_1
  FOREIGN KEY (usuario_email) REFERENCES usuarios(email) ON DELETE CASCADE;

-- Vendas
ALTER TABLE vendas DROP FOREIGN KEY vendas_ibfk_1;
ALTER TABLE vendas ADD CONSTRAINT vendas_ibfk_1
  FOREIGN KEY (fornecedor_email) REFERENCES usuarios(email) ON DELETE CASCADE;

-- Produtos
ALTER TABLE produtos DROP FOREIGN KEY produtos_ibfk_1;
ALTER TABLE produtos ADD CONSTRAINT produtos_ibfk_1
  FOREIGN KEY (vendedor_email) REFERENCES usuarios(email) ON DELETE CASCADE;

-- Pedidos
ALTER TABLE pedidos DROP FOREIGN KEY pedidos_ibfk_1;
ALTER TABLE pedidos ADD CONSTRAINT pedidos_ibfk_1
  FOREIGN KEY (comprador_email) REFERENCES usuarios(email) ON DELETE CASCADE;

-- Suporte
ALTER TABLE suporte DROP FOREIGN KEY suporte_ibfk_2;
ALTER TABLE suporte ADD CONSTRAINT suporte_ibfk_2
  FOREIGN KEY (email_usuario) REFERENCES usuarios(email) ON DELETE CASCADE;

-- Histórico de Compras
ALTER TABLE historico_compras DROP FOREIGN KEY historico_compras_ibfk_1;
ALTER TABLE historico_compras ADD CONSTRAINT historico_compras_ibfk_1
FOREIGN KEY (comprador_email) REFERENCES usuarios(email) ON DELETE CASCADE;

-- Ticket PENDENTE (sem resposta)
INSERT INTO suporte (email_adm, email_usuario, assunto, descricao, data_envio)
VALUES (NULL, 'cliente@email.com', 'Dúvida sobre pedido', 'Gostaria de saber o status do meu pedido.', NOW());

-- Ticket RESPONDIDO
INSERT INTO suporte (email_adm, email_usuario, assunto, descricao, data_envio, resposta, data_resposta)
VALUES ('admin@email.com', 'cliente@email.com', 'Problema com pagamento', 'Tive um erro ao tentar pagar com cartão.', NOW(), 'Verificamos e corrigimos o erro. Pode tentar novamente.', CURDATE());

-- Outro PENDENTE
INSERT INTO suporte (email_adm, email_usuario, assunto, descricao, data_envio)
VALUES (NULL, 'cliente@email.com', 'Troca de produto', 'Comprei um produto e quero trocar.', NOW());
