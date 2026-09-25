CREATE DATABASE IF NOT EXISTS rinocream
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE rinocream;

SET NAMES utf8mb4;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS itens_pedido;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS produtos;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS usuarios;

SET FOREIGN_KEY_CHECKS = 1;


CREATE TABLE usuarios (
    idusuario INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM(
        'cliente',
        'admin'
    ) NOT NULL DEFAULT 'cliente'
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


CREATE TABLE clientes (
    idcliente INT AUTO_INCREMENT PRIMARY KEY,
    idusuario INT NOT NULL UNIQUE,
    nome VARCHAR(100) NOT NULL,
    telefone VARCHAR(20),
    endereco VARCHAR(150),

    CONSTRAINT fk_clientes_usuarios
        FOREIGN KEY (idusuario)
        REFERENCES usuarios(idusuario)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


CREATE TABLE categorias (
    idcategoria INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


CREATE TABLE produtos (
    idproduto INT AUTO_INCREMENT PRIMARY KEY,
    idcategoria INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    descricao VARCHAR(255),
    preco DECIMAL(10,2) NOT NULL,
    imagem VARCHAR(255),
    estoque INT NOT NULL DEFAULT 0,
    ativo TINYINT(1) NOT NULL DEFAULT 1,

    CONSTRAINT fk_produtos_categorias
        FOREIGN KEY (idcategoria)
        REFERENCES categorias(idcategoria)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


CREATE TABLE pedidos (
    idpedido INT AUTO_INCREMENT PRIMARY KEY,
    idcliente INT NOT NULL,
    data_pedido DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    status ENUM(
        'aguardando',
        'preparando',
        'pronto',
        'entregue',
        'cancelado'
    ) NOT NULL DEFAULT 'aguardando',

    valor_total DECIMAL(10,2)
        NOT NULL DEFAULT 0.00,

    CONSTRAINT fk_pedidos_clientes
        FOREIGN KEY (idcliente)
        REFERENCES clientes(idcliente)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


CREATE TABLE itens_pedido (
    iditem INT AUTO_INCREMENT PRIMARY KEY,
    idpedido INT NOT NULL,
    idproduto INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,

    CONSTRAINT fk_itens_pedido_pedidos
        FOREIGN KEY (idpedido)
        REFERENCES pedidos(idpedido),

    CONSTRAINT fk_itens_pedido_produtos
        FOREIGN KEY (idproduto)
        REFERENCES produtos(idproduto)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


INSERT INTO categorias
(
    idcategoria,
    nome
)
VALUES
(1, 'Sorvetes'),
(2, 'Milk-shakes'),
(3, 'Açaí'),
(4, 'Sobremesas');


INSERT INTO produtos
(
    idproduto,
    idcategoria,
    nome,
    descricao,
    preco,
    imagem,
    estoque,
    ativo
)
VALUES

(
    1,
    1,
    'Chocolate',
    'Sorvete cremoso sabor chocolate.',
    7.00,
    'chocolate.png',
    50,
    1
),

(
    2,
    1,
    'Morango',
    'Sorvete cremoso sabor morango.',
    7.00,
    'morango.png',
    50,
    1
),

(
    3,
    1,
    'Baunilha',
    'Sorvete clássico sabor baunilha.',
    7.00,
    'baunilha.png',
    100,
    1
),

(
    4,
    1,
    'Flocos',
    'Sorvete de flocos com pedaços de chocolate.',
    7.50,
    'flocos.png',
    50,
    1
),

(
    5,
    2,
    'Milk-shake de Ovomaltine',
    'Milk-shake cremoso com Ovomaltine.',
    14.00,
    'milkshake-ovomaltine.png',
    30,
    1
),

(
    6,
    2,
    'Milk-shake de Morango',
    'Milk-shake cremoso com sabor de morango.',
    13.00,
    'milkshake-morango.png',
    30,
    1
),

(
    7,
    2,
    'Milk-shake de Chocolate',
    'Milk-shake cremoso de chocolate.',
    13.00,
    'milkshake-chocolate.png',
    30,
    1
),

(
    8,
    3,
    'Açaí 300ml',
    'Açaí cremoso servido no copo de 300ml.',
    12.00,
    'acai-300.png',
    30,
    1
),

(
    9,
    3,
    'Açaí 500ml',
    'Açaí cremoso servido no copo de 500ml.',
    16.00,
    'acai-500.png',
    30,
    1
),

(
    10,
    4,
    'Sundae de Chocolate',
    'Sorvete com cobertura de chocolate.',
    10.00,
    'sundae-chocolate.png',
    30,
    1
),

(
    11,
    4,
    'Sundae de Morango',
    'Sorvete com cobertura de morango.',
    10.00,
    'sundae-morango.png',
    30,
    1
),

(
    12,
    1,
    'Sorvete de Pistache',
    'Sabor moderno do pistache.',
    9.50,
    'pistache.png',
    0,
    1
);