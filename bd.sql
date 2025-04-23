CREATE DATABASE ecommerce;

USE ecommerce;

CREATE TABLE produtos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255),
  preco DECIMAL(10, 2),
  descricao TEXT
);

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

SELECT * FROM ecommerce.usuarios;

SELECT * FROM ecommerce.produtos;