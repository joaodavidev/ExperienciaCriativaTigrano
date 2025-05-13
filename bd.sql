CREATE DATABASE ecommerce;

USE ecommerce;

CREATE TABLE `usuarios` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `senha` VARCHAR(255) NOT NULL,
  `sexo` ENUM('masculino', 'feminino', 'outro') NOT NULL,
  `idade` INT NOT NULL,
  `email` VARCHAR(255) UNIQUE NOT NULL,
  `cpf` VARCHAR(14) UNIQUE
);

CREATE TABLE `adm` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `suporte_id` INT,
  FOREIGN KEY (`suporte_id`) REFERENCES `suporte` (`id`)
);

CREATE TABLE `suporte` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `adm_id` INT,
  `email` VARCHAR(255),
  `assunto` VARCHAR(255),
  `descricao` TEXT,
  `data_envio` DATETIME,
  `resposta` TEXT,
  `data_resposta` DATE,
  FOREIGN KEY (`adm_id`) REFERENCES `adm` (`id`)
  -- NÃO é recomendado referenciar `email` com FK aqui, pois não é PK em `usuarios`
);

CREATE TABLE `produtos` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `usuario_id` INT,
  `nome` VARCHAR(255),
  `preco` DECIMAL(10,2),
  `preco_tigrano_coins` INT,
  `descricao` TEXT,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
);

CREATE TABLE `carrinho` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `usuario_id` INT,
  `produto_id` INT,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
);

CREATE TABLE `tigrano_coins` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `usuario_id` INT,
  `quantidade` INT,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
);

CREATE TABLE `avaliacao` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `usuario_id` INT,
  `estrelas` CHAR(1),
  `comentario` TEXT,
  `produto_id` INT,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
);

CREATE TABLE `vendas` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `usuario_id` INT,
  `produto_id` INT,
  `quantidade_vendas` INT,
  `data_vendas` DATE,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
);

CREATE TABLE `historico_compras` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `usuario_id` INT,
  `produto_id` INT,
  `data_compras` DATE,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
);

CREATE TABLE `produtos_historico_compras` (
  `produtos_id` INT,
  `historico_compras_produto_id` INT,
  PRIMARY KEY (`produtos_id`, `historico_compras_produto_id`),
  FOREIGN KEY (`produtos_id`) REFERENCES `produtos` (`id`),
  FOREIGN KEY (`historico_compras_produto_id`) REFERENCES `historico_compras` (`id`)
);

CREATE TABLE `cartao_credito_usuario` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `usuario_id` INT,
  `nome_titular` TEXT,
  `numero_cartao` VARCHAR(20),
  `cvv` VARCHAR(4),
  `parcelamento` INT,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
);

CREATE TABLE `cartao_debito_usuario` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `usuario_id` INT,
  `nome_titular` TEXT,
  `numero_cartao` VARCHAR(20),
  `cvv` VARCHAR(4),
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
);

CREATE TABLE `pix` (
  `id` INT PRIMARY KEY AUTO_INCREMENT
  -- Pode incluir mais campos se desejar
);

CREATE TABLE `tipo_pagamento` (
  `id_cartao_credito` INT,
  `id_cartao_debito` INT,
  FOREIGN KEY (`id_cartao_credito`) REFERENCES `cartao_credito_usuario` (`id`),
  FOREIGN KEY (`id_cartao_debito`) REFERENCES `cartao_debito_usuario` (`id`)
);
